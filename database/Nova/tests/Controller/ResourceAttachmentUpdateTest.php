<?php

namespace Laravel\Nova\Tests\Controller;

use Illuminate\Database\Eloquent\Relations\Relation;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Tests\Fixtures\Role;
use Laravel\Nova\Tests\Fixtures\RoleAssignment;
use Laravel\Nova\Tests\Fixtures\User;
use Laravel\Nova\Tests\IntegrationTest;

class ResourceAttachmentUpdateTest extends IntegrationTest
{
    public function setUp() : void
    {
        parent::setUp();

        $this->authenticate();
    }

    public function test_can_update_attached_resources()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();
        $user->roles()->attach($role, ['admin' => 'Y']);

        $this->assertEquals('Y', $user->fresh()->roles->first()->pivot->admin);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users/'.$user->id.'/update-attached/roles/'.$role->id, [
                            'roles' => $role->id,
                            'admin' => 'N',
                            'pivot-update' => 'N',
                            'viaRelationship' => 'roles',
                        ]);

        $response->assertStatus(200);

        $this->assertCount(1, $user->fresh()->roles);
        $this->assertEquals($role->id, $user->fresh()->roles->first()->id);
        $this->assertEquals('N', $user->fresh()->roles->first()->pivot->admin);

        $this->assertCount(1, ActionEvent::all());
        $this->assertEquals('Update Attached', ActionEvent::first()->name);
        $this->assertEquals(get_class($user), ActionEvent::first()->actionable_type);
        $this->assertEquals($user->id, ActionEvent::first()->actionable_id);
        $this->assertEquals($role->id, ActionEvent::first()->target->id);
        $this->assertTrue($role->is(ActionEvent::first()->target));
    }

    public function test_cant_update_pivot_fields_that_arent_authorized()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();
        $user->roles()->attach($role, ['admin' => 'Y']);

        $this->assertEquals('Y', $user->fresh()->roles->first()->pivot->admin);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users/'.$user->id.'/update-attached/roles/'.$role->id, [
                            'roles' => $role->id,
                            'admin' => 'N',
                            'pivot-update' => 'N',
                            'restricted' => 'No',
                            'viaRelationship' => 'roles',
                        ]);

        $response->assertStatus(200);

        $this->assertEquals('Yes', $user->fresh()->roles->first()->pivot->restricted);
    }

    public function test_can_update_attached_soft_deleted_resources()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();

        $user->delete();
        $role->users()->attach($user, ['admin' => 'Y']);

        $this->assertEquals('Y', $role->fresh()->users()->withTrashed()->first()->pivot->admin);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/roles/'.$role->id.'/update-attached/users/'.$user->id, [
                            'users' => $user->id,
                            'users_trashed' => 'true',
                            'admin' => 'N',
                            'pivot-update' => 'N',
                            'viaRelationship' => 'users',
                        ]);

        $response->assertStatus(200);

        $users = $role->fresh()->users()->withTrashed()->get();

        $this->assertCount(1, $users);
        $this->assertEquals($role->id, $users->first()->id);
        $this->assertEquals('N', $users->first()->pivot->admin);
    }

    public function test_cant_update_attached_resources_if_related_resource_is_not_relatable()
    {
        $user = factory(User::class)->create();

        $role = factory(Role::class)->create();
        $role2 = factory(Role::class)->create();
        $role3 = factory(Role::class)->create();

        $user->roles()->attach($role3, ['admin' => 'Y']);

        $this->assertEquals('Y', $user->fresh()->roles->first()->pivot->admin);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users/'.$user->id.'/update-attached/roles/'.$role->id, [
                            'roles' => $role3->id,
                            'admin' => 'N',
                            'pivot-update' => 'N',
                            'viaRelationship' => 'roles',
                        ]);

        $response->assertStatus(422);
        $this->assertFalse(isset($_SERVER['nova.user.relatableRoles']));
    }

    public function test_resource_may_specify_custom_relatable_query_customizer()
    {
        $user = factory(User::class)->create();

        $role = factory(Role::class)->create();
        $role2 = factory(Role::class)->create();
        $role3 = factory(Role::class)->create();

        $_SERVER['nova.user.useCustomRelatableRoles'] = true;
        unset($_SERVER['nova.user.relatableRoles']);

        $user->roles()->attach($role3, ['admin' => 'Y']);

        $this->assertEquals('Y', $user->fresh()->roles->first()->pivot->admin);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users/'.$user->id.'/update-attached/roles/'.$role->id, [
                            'roles' => $role3->id,
                            'admin' => 'N',
                            'pivot-update' => 'N',
                            'viaRelationship' => 'roles',
                        ]);

        unset($_SERVER['nova.user.useCustomRelatableRoles']);

        $this->assertNotNull($_SERVER['nova.user.relatableRoles']);
        $response->assertStatus(422);

        unset($_SERVER['nova.user.relatableRoles']);
    }

    public function test_404_is_returned_if_resource_is_not_attached()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();
        $user->roles()->attach($role);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users/'.$user->id.'/update-attached/roles/100', [
                            'roles' => $role->id,
                            'admin' => 'N',
                            'pivot-update' => 'N',
                            'viaRelationship' => 'roles',
                        ]);

        $response->assertStatus(404);
    }

    public function test_pivot_data_is_validated()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();
        $user->roles()->attach($role);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users/'.$user->id.'/update-attached/roles/'.$role->id, [
                            'roles' => $role->id,
                            'viaRelationship' => 'roles',
                        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['admin']);
    }

    public function test_action_event_should_honor_custom_polymorphic_type_for_attached_resource_update()
    {
        Relation::morphMap([
            'user' => User::class,
            'role' => Role::class,
            'role_user' => RoleAssignment::class,
        ]);

        $user = factory(User::class)->create();
        $role = factory(Role::class)->create();
        $user->roles()->attach($role, ['admin' => 'Y']);

        $response = $this->withExceptionHandling()
                        ->postJson('/nova-api/users/'.$user->id.'/update-attached/roles/'.$role->id, [
                            'roles' => $role->id,
                            'admin' => 'N',
                            'pivot-update' => 'N',
                            'viaRelationship' => 'roles',
                        ]);

        $actionEvent = ActionEvent::first();

        $this->assertEquals('Update Attached', $actionEvent->name);

        $this->assertEquals('user', $actionEvent->actionable_type);
        $this->assertEquals($user->id, $actionEvent->actionable_id);

        $this->assertEquals('role', $actionEvent->target_type);
        $this->assertEquals($role->id, $actionEvent->target_id);

        $this->assertEquals('role_user', $actionEvent->model_type);
        $this->assertEquals($user->roles->first->pivot->id, $actionEvent->model_id);

        Relation::morphMap([], false);
    }
}
