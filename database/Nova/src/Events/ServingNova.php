<?php

namespace Laravel\Nova\Events;

use Illuminate\Http\Request;
use Illuminate\Foundation\Events\Dispatchable;

class ServingNova
{
    use Dispatchable;

    /**
     * The request instance.
     *
     * @var Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
