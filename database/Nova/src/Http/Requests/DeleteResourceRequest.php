<?php

namespace Laravel\Nova\Http\Requests;

use Closure;
use Illuminate\Support\Collection;

class DeleteResourceRequest extends DeletionRequest
{
    /**
     * Get the selected models for the action in chunks.
     *
     * @param  int  $count
     * @param Closure $callback
     * @return mixed
     */
    public function chunks($count, Closure $callback)
    {
        return $this->chunkWithAuthorization($count, $callback, function ($models) {
            return $this->deletableModels($models);
        });
    }

    /**
     * Get the models that may be deleted.
     *
     * @param Collection $models
     * @return Collection
     */
    protected function deletableModels(Collection $models)
    {
        return $models->mapInto($this->resource())
                        ->filter
                        ->authorizedToDelete($this)
                        ->map->model();
    }
}
