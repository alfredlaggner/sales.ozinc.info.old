<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActionController extends Controller
{
    /**
     * List the actions for the given resource.
     *
     * @param NovaRequest $request
     * @return Response
     */
    public function index(NovaRequest $request)
    {
        return response()->json([
            'actions' => $request->newResource()->availableActions($request),
            'pivotActions' => [
                'name' => $request->pivotName(),
                'actions' => $request->newResource()->availablePivotActions($request),
            ],
        ]);
    }

    /**
     * Perform an action on the specified resources.
     *
     * @param ActionRequest $request
     * @return Response
     */
    public function store(ActionRequest $request)
    {
        $request->validateFields();

        return $request->action()->handleRequest($request);
    }
}
