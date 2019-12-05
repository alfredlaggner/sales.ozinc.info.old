<?php

namespace Laravel\Nova;

use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;

class GlobalSearch
{
    /**
     * The request instance.
     *
     * @var NovaRequest
     */
    public $request;

    /**
     * The resource class names that should be searched.
     *
     * @var Collection
     */
    public $resources;

    /**
     * Create a new global search instance.
     *
     * @param NovaRequest $request
     * @param Collection $resources
     * @return void
     */
    public function __construct(NovaRequest $request, Collection $resources)
    {
        $this->request = $request;
        $this->resources = $resources;
    }

    /**
     * Get the matching resources.
     *
     * @return array
     */
    public function get()
    {
        $formatted = [];

        foreach ($this->getSearchResults() as $resource => $models) {
            foreach ($models as $model) {
                $instance = new $resource($model);

                $formatted[] = [
                    'resourceName' => $resource::uriKey(),
                    'resourceTitle' => $resource::label(),
                    'title' => $instance->title(),
                    'subTitle' => $instance->subtitle(),
                    'resourceId' => $model->getKey(),
                    'url' => url(Nova::path().'/resources/'.$resource::uriKey().'/'.$model->getKey()),
                    'avatar' => $instance->resolveAvatarUrl($this->request),
                ];
            }
        }

        return $formatted;
    }

    /**
     * Get the search results for the resources.
     *
     * @return array
     */
    protected function getSearchResults()
    {
        $results = [];

        foreach ($this->resources as $resource) {
            $query = $resource::buildIndexQuery(
                $this->request, $resource::newModel()->newQuery(),
                $this->request->search
            );

            if (count($models = $query->limit(5)->get()) > 0) {
                $results[$resource] = $models;
            }
        }

        return collect($results)->sortKeys()->all();
    }
}
