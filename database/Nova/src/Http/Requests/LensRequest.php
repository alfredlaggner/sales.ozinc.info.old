<?php

namespace Laravel\Nova\Http\Requests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LensRequest extends NovaRequest
{
    use DecodesFilters, InteractsWithLenses;

    /**
     * Apply the specified filters to the given query.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function withFilters($query)
    {
        return $this->filter($query);
    }

    /**
     * Apply the specified filters to the given query.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function filter($query)
    {
        $this->filters()->each->__invoke($this, $query);

        return $query;
    }

    /**
     * Apply the specified ordering to the given query.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function withOrdering($query)
    {
        if (! $this->orderBy || ! $this->orderByDirection) {
            return $query;
        }

        if ($this->lens()->resolveFields($this)->findFieldByAttribute($this->orderBy)) {
            return $query->orderBy(
                $query->getModel()->getTable().'.'.$this->orderBy,
                $this->orderByDirection === 'asc' ? 'asc' : 'desc'
            );
        }

        return $query;
    }

    /**
     * Get all of the possibly available filters for the request.
     *
     * @return Collection
     */
    protected function availableFilters()
    {
        return $this->lens()->availableFilters($this);
    }

    /**
     * Map the given models to the appropriate resource for the request.
     *
     * @param Collection $models
     * @return Collection
     */
    public function toResources(Collection $models)
    {
        $resource = $this->resource();

        $lens = get_class($this->lens());

        return $models->map(function ($model) use ($resource, $lens) {
            return (new $resource($model))->serializeForIndex(
                $this, (new $lens($model))->resolveFields($this)
            );
        });
    }
}
