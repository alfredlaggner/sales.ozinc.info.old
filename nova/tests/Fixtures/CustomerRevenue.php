<?php

namespace Laravel\Nova\Tests\Fixtures;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Value;

class CustomerRevenue extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param Request $request
     * @param User
     * @return mixed
     */
    public function calculate(Request $request, User $user)
    {
        $_SERVER['nova.customerRevenue.user'] = $user;

        return 100;
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'customer-revenue';
    }
}
