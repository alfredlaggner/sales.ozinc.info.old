<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\LensRequest;

class LensController extends Controller
{
    /**
     * List the lenses for the given resource.
     *
     * @param LensRequest $request
     * @return Response
     */
    public function index(LensRequest $request)
    {
        return $request->availableLenses();
    }

    /**
     * Get the specified lens and its resources.
     *
     * @param LensRequest $request
     * @return Response
     */
    public function show(LensRequest $request)
    {
        $lens = $request->lens();

        $paginator = $lens->query($request, $request->newQuery());

        if ($paginator instanceof Builder) {
            $paginator = $paginator->simplePaginate($request->perPage ?? 25);
        }

        return response()->json([
            'name' => $request->lens()->name(),
            'resources' => $request->toResources($paginator->getCollection()),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'softDeletes' => $request->resourceSoftDeletes(),
            'hasId' => $lens->availableFields($request)->whereInstanceOf(ID::class)->isNotEmpty(),
        ]);
    }
}
