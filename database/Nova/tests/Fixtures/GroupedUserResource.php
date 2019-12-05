<?php

namespace Laravel\Nova\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

class GroupedUserResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = User::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [];
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param NovaRequest $request
     * @param  Builder  $query
     * @return Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->select('users.id')
            ->join('user_roles', 'user_roles.user_id', '=', 'users.id')
            ->where('user_roles.role_id', '=', 1)
            ->groupBy('users.id');
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'grouped-users';
    }
}
