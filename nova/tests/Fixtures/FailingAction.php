<?php

namespace Laravel\Nova\Tests\Fixtures;

use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;

class FailingAction extends Action implements ShouldQueue
{
    use InteractsWithQueue;

    public static $failedForUser = false;

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @return string|void
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $this->fail();
    }

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @return string|void
     */
    public function handleForUsers(ActionFields $fields, Collection $models)
    {
        $this->fail();
    }

    /**
     * Handle an action failure.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @param  Throwable  $e
     * @return string|void
     */
    public function failedForUsers(ActionFields $fields, Collection $models, $e)
    {
        static::$failedForUser = true;
    }
}
