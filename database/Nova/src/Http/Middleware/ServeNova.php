<?php

namespace Laravel\Nova\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaServiceProvider;
use Laravel\Nova\Events\NovaServiceProviderRegistered;

class ServeNova
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle($request, $next)
    {
        if ($this->isNovaRequest($request)) {
            app()->register(NovaServiceProvider::class);

            NovaServiceProviderRegistered::dispatch();
        }

        return $next($request);
    }

    /**
     * Determine if the given request is intended for Nova.
     *
     * @param  Request  $request
     * @return bool
     */
    protected function isNovaRequest($request)
    {
        $path = trim(Nova::path(), '/') ?: '/';

        return $request->is($path) ||
               $request->is(trim($path.'/*', '/')) ||
               $request->is('nova-api/*');
    }
}
