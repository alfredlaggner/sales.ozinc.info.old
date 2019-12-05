<?php

namespace Laravel\Nova\Http\Controllers;

use DateTime;
use Illuminate\Database\Eloquent\Pivot;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class MorphedResourceAttachController extends ResourceAttachController
{
    /**
     * Initialize a fresh pivot model for the relationship.
     *
     * @param NovaRequest $request
     * @param  MorphToMany  $relationship
     * @return Pivot
     */
    protected function initializePivot(NovaRequest $request, $relationship)
    {
        ($pivot = $relationship->newPivot())->forceFill([
            $relationship->getForeignPivotKeyName() => $request->resourceId,
            $relationship->getRelatedPivotKeyName() => $request->input($request->relatedResource),
            $relationship->getMorphType() => $request->findModelOrFail()->{$request->viaRelationship}()->getMorphClass(),
        ]);

        if ($relationship->withTimestamps) {
            $pivot->forceFill([
                $relationship->createdAt() => new DateTime,
                $relationship->updatedAt() => new DateTime,
            ]);
        }

        return $pivot;
    }
}
