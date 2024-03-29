<?php

namespace Laravel\Nova\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class RoleResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Role::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name',
    ];

    /**
     * Determine if the resource should be displayed for the given request.
     *
     * @param Request $request
     * @return bool
     */
    public static function authorizedToViewAny(Request $request)
    {
        return $_SERVER['nova.authorize.roles'] ?? true;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('ID', 'id'),

            BelongsToMany::make('Users', 'users', UserResource::class)->fields(function () {
                return [
                    Text::make('Admin', 'admin')->rules('required'),

                    $this->when($_SERVER['__nova.role.pivotFile'] ?? false, function () {
                        return File::make('Photo', 'photo');
                    }),

                    Text::make('Restricted', 'restricted')->canSee(function () {
                        return false;
                    }),
                ];
            })->actions(function ($request) {
                return [
                    new FailingPivotAction,
                    new NoopAction,
                    new NoopActionWithPivotHandle,
                    new QueuedAction,
                    new QueuedUpdateStatusAction,
                    new UpdateStatusAction,
                ];
            })->prunable($_SERVER['__nova.role.prunable'] ?? false),

            Text::make('Name', 'name')->rules('required', 'string', 'max:255'),
        ];
    }

    /**
     * Get the actions displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            new NoopAction,
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [new IdFilter];
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param NovaRequest $request
     * @param  Builder  $query
     * @return Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return $query->where('id', '<', 3);
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'roles';
    }
}
