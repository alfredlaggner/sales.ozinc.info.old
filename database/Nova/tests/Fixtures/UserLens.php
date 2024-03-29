<?php

namespace Laravel\Nova\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

class UserLens extends Lens
{
    /**
     * Get the query builder / paginator for the lens.
     *
     * @param LensRequest $request
     * @param  Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->filter($query->withTrashed());
    }

    /**
     * Get the fields available to the lens.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('ID', 'id'),
            Text::make('Name', 'name'),
        ];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new IdFilter,
        ];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'user-lens';
    }

    /**
     * Determine if the action should be available for the given request.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizedToSee(Request $request)
    {
        return $_SERVER['nova.authorize.forbidden-user-lens'] ?? parent::authorizedToSee($request);
    }

    /**
     * Get the actions available on the entity.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            new NoopAction(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            (new TotalUsers)->canSee(function ($request) {
                return $_SERVER['nova.totalUsers.canSee'] ?? true;
            }),

            new UserGrowth,
            (new CustomerRevenue)->onlyOnDetail(),
        ];
    }
}
