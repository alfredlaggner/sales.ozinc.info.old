<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class UpdateFieldController extends Controller
{
    /**
     * List the update fields for the given resource.
     *
     * @param NovaRequest $request
     * @return Response
     */
    public function index(NovaRequest $request)
    {
        $resource = $request->newResourceWith($request->findModelOrFail());

        $resource->authorizeToUpdate($request);

        return response()->json(
            $resource
                ->updateFields($request)
                ->values()
                ->all()
        );
    }
}
