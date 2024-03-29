<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Http\Requests\ForceDeleteResourceRequest;

class ResourceForceDeleteController extends Controller
{
    use DeletesFields;

    /**
     * Force delete the given resource(s).
     *
     * @param ForceDeleteResourceRequest $request
     * @return Response
     */
    public function handle(ForceDeleteResourceRequest $request)
    {
        $request->chunks(150, function ($models) use ($request) {
            $models->each(function ($model) use ($request) {
                $this->forceDeleteFields($request, $model);

                if (in_array(Actionable::class, class_uses_recursive($model))) {
                    $model->actions()->delete();
                }

                $model->forceDelete();

                DB::table('action_events')->insert(
                    ActionEvent::forResourceDelete($request->user(), collect([$model]))
                                ->map->getAttributes()->all()
                );
            });
        });
    }
}
