<?php

namespace Laravel\Nova\Http\Requests;

use Illuminate\Support\Collection;
use Laravel\Nova\Nova;

class DashboardCardRequest extends NovaRequest
{
    /**
     * Get all of the possible cards for the request.
     *
     * @return Collection
     */
    public function availableCards()
    {
        return Nova::availableDashboardCards($this);
    }
}
