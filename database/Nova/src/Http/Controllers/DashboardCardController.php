<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\DashboardCardRequest;

class DashboardCardController extends Controller
{
    /**
     * List the cards for the dashboard.
     *
     * @param DashboardCardRequest $request
     * @return Response
     */
    public function index(DashboardCardRequest $request)
    {
        return $request->availableCards();
    }
}
