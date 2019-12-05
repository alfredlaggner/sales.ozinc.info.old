<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class CreationFieldController extends Controller
{
    /**
     * List the creation fields for the given resource.
     *
     * @param NovaRequest $request
     * @return Response
     */
    public function index(NovaRequest $request)
    {
        $resource = $request->resource();

        $resource::authorizeToCreate($request);

        return response()->json(
            $request->newResource()->creationFields($request)->values()->all()
        );
    }
}
