<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\ResourceDetailRequest;
use Laravel\Nova\Panel;

class ResourceShowController extends Controller
{
    /**
     * Display the resource for administration.
     *
     * @param ResourceDetailRequest $request
     * @return Response
     */
    public function handle(ResourceDetailRequest $request)
    {
        $resource = $request->newResourceWith(tap($request->findModelQuery(), function ($query) use ($request) {
            $request->newResource()->detailQuery($request, $query);
        })->firstOrFail());

        $resource->authorizeToView($request);

        return response()->json([
            'panels' => $resource->availablePanels($request),
            'resource' => $this->assignFieldsToPanels(
                $request, $resource->serializeForDetail($request)
            ),
        ]);
    }

    /**
     * Assign any un-assigned fields to the default panel.
     *
     * @param ResourceDetailRequest $request
     * @param  array  $resource
     * @return Response
     */
    protected function assignFieldsToPanels(ResourceDetailRequest $request, array $resource)
    {
        foreach ($resource['fields'] as $field) {
            $field->panel = $field->panel ?? Panel::defaultNameFor($request->newResource());
        }

        return $resource;
    }
}
