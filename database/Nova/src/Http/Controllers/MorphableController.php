<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class MorphableController extends Controller
{
    /**
     * List the available morphable resources for a given resource.
     *
     * @param NovaRequest $request
     * @return Response
     */
    public function index(NovaRequest $request)
    {
        $relatedResource = Nova::resourceForKey($request->type);

        $field = $request->newResource()
                        ->availableFields($request)
                        ->firstWhere('attribute', $request->field);

        $withTrashed = $this->shouldIncludeTrashed(
            $request, $relatedResource
        );

        return [
            'resources' => $field->buildMorphableQuery($request, $relatedResource, $withTrashed)->get()
                                ->mapInto($relatedResource)
                                ->filter->authorizedToAdd($request, $request->model())
                                ->map(function ($resource) use ($request, $field, $relatedResource) {
                                    return $field->formatMorphableResource($request, $resource, $relatedResource);
                                })->sortBy('display')->values(),
            'withTrashed' => $withTrashed,
            'softDeletes' => $relatedResource::softDeletes(),
        ];
    }

    /**
     * Determine if the query should include trashed models.
     *
     * @param NovaRequest $request
     * @param  string  $associatedResource
     * @return bool
     */
    protected function shouldIncludeTrashed(NovaRequest $request, $associatedResource)
    {
        if ($request->withTrashed === 'true') {
            return true;
        }

        $associatedModel = $associatedResource::newModel();

        if ($request->current && empty($request->search) && $associatedResource::softDeletes()) {
            $associatedModel = $associatedModel->newQueryWithoutScopes()->find($request->current);

            return $associatedModel ? $associatedModel->trashed() : false;
        }

        return false;
    }
}
