<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;

class ResourceUpdateController extends Controller
{
    /**
     * Create a new resource.
     *
     * @param UpdateResourceRequest $request
     * @return JsonResponse
     */
    public function handle(UpdateResourceRequest $request)
    {
        $request->findResourceOrFail()->authorizeToUpdate($request);

        $resource = $request->resource();

        $resource::validateForUpdate($request);

        $model = DB::transaction(function () use ($request, $resource) {
            $model = $request->findModelQuery()->lockForUpdate()->firstOrFail();

            if ($this->modelHasBeenUpdatedSinceRetrieval($request, $model)) {
                return response('', 409)->throwResponse();
            }

            [$model, $callbacks] = $resource::fillForUpdate($request, $model);

            return tap(tap($model)->save(), function ($model) use ($request, $callbacks) {
                ActionEvent::forResourceUpdate($request->user(), $model)->save();

                collect($callbacks)->each->__invoke();
            });
        });

        return response()->json([
            'id' => $model->getKey(),
            'resource' => $model->attributesToArray(),
        ]);
    }

    /**
     * Determine if the model has been updated since it was retrieved.
     *
     * @param UpdateResourceRequest $request
     * @param  Model  $model
     * @return bool
     */
    protected function modelHasBeenUpdatedSinceRetrieval(UpdateResourceRequest $request, $model)
    {
        $column = $model->getUpdatedAtColumn();

        if (! $model->{$column}) {
            return false;
        }

        return $request->input('_retrieved_at') && $model->usesTimestamps() && $model->{$column}->gt(
            Carbon::createFromTimestamp($request->input('_retrieved_at'))
        );
    }
}
