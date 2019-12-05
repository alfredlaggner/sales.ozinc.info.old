<?php

namespace Laravel\Nova;

use Illuminate\View\View;

abstract class Tool extends Card
{
    /**
     * Perform any tasks that need to happen on tool registration.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return View|string
     */
    public function renderNavigation()
    {
        return '';
    }
}
