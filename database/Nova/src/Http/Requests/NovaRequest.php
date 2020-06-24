<?php

namespace Laravel\Nova\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Nova\MemoizesMethods;

class NovaRequest extends FormRequest
{
    use InteractsWithResources, InteractsWithRelatedResources, MemoizesMethods;

    /**
     * Determine if this request is via a many to many relationship.
     *
     * @return bool
     */
    public function viaManyToMany()
    {
        return in_array(
            $this->relationshipType,
            ['belongsToMany', 'morphToMany']
        );
    }
}
