<?php

namespace Laravel\Nova;

use Closure;
use Illuminate\Http\Request;

trait AuthorizedToSee
{
    /**
     * The callback used to authorize viewing the filter.
     *
     * @var Closure|null
     */
    public $seeCallback;

    /**
     * Determine if the filter should be available for the given request.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizedToSee(Request $request)
    {
        return $this->seeCallback ? call_user_func($this->seeCallback, $request) : true;
    }

    /**
     * Set the callback to be run to authorize viewing the filter.
     *
     * @param Closure $callback
     * @return $this
     */
    public function canSee(Closure $callback)
    {
        $this->seeCallback = $callback;

        return $this;
    }
}
