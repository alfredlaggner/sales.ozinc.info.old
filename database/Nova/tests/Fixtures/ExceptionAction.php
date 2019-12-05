<?php

namespace Laravel\Nova\Tests\Fixtures;

use Exception;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;

class ExceptionAction extends Action
{
    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @return string|void
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        throw new Exception('Something went wrong.');
    }
}
