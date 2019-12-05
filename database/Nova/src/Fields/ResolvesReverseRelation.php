<?php

namespace Laravel\Nova\Fields;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

trait ResolvesReverseRelation
{
    /**
     * The reverse relation for the related resource.
     *
     * @var string
     */
    public $reverseRelation;

    /**
     * Determine if the field is the reverse relation of a showed index view.
     *
     * @param Request $request
     * @return bool
     */
    public function isReverseRelation(Request $request)
    {
        if (! $request->viaResource || ($this->resourceName && $this->resourceName !== $request->viaResource)) {
            return false;
        }

        $reverse = $this->getReverseRelation($request);

        return $reverse === $request->viaRelationship;
    }

    /**
     * Get reverse relation field name.
     *
     * @param NovaRequest $request
     * @return string
     */
    public function getReverseRelation(NovaRequest $request)
    {
        if (is_null($this->reverseRelation)) {
            $viaModel = forward_static_call(
                [$resourceClass = $request->viaResource(), 'newModel']
            );

            $viaResource = new $resourceClass($viaModel);

            $resource = $request->newResource();

            $this->reverseRelation = $viaResource->availableFields($request)
                    ->first(function ($field) use ($viaModel, $resource) {
                        if (! isset($field->resourceName) || $field->resourceName !== $resource::uriKey()) {
                            return false;
                        }

                        if ($field instanceof BelongsToMany || $field instanceof MorphToMany) {
                            return false;
                        }

                        $relation = $viaModel->{$field->attribute}();

                        return $relation->getForeignKeyName() === $resource->model()->{$this->attribute}()->getForeignKeyName();
                    })->attribute ?? '';
        }

        return $this->reverseRelation;
    }
}
