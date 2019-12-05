<?php

namespace Laravel\Nova\Http\Requests;

use Illuminate\Support\Collection;
use Laravel\Nova\Nova;
use Laravel\Nova\Metrics\Metric;

class DashboardMetricRequest extends NovaRequest
{
    /**
     * Get the metric instance for the given request.
     *
     * @return Metric
     */
    public function metric()
    {
        return $this->availableMetrics()->first(function ($metric) {
            return $this->metric === $metric->uriKey();
        }) ?: abort(404);
    }

    /**
     * Get all of the possible metrics for the request.
     *
     * @return Collection
     */
    public function availableMetrics()
    {
        return Nova::availableDashboardCards($this)->whereInstanceOf(Metric::class);
    }
}
