<?php

namespace Laravel\Nova\Exceptions;

use Illuminate\Auth\AuthenticationException as BaseAuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class AuthenticationException extends BaseAuthenticationException
{
    /**
     * Render the exception.
     *
     * @param  Request  $request
     * @return Response
     */
    public function render($request)
    {
        return $request->expectsJson()
                    ? response()->json(['message' => $this->getMessage()], 401)
                    : redirect()->guest($this->location());
    }

    /**
     * Determine the location the user should be redirected to.
     *
     * @return string
     */
    protected function location()
    {
        if (Route::getRoutes()->hasNamedRoute('nova.login')) {
            return route('nova.login');
        } elseif (Route::getRoutes()->hasNamedRoute('login')) {
            return route('login');
        }

        return '/login';
    }
}
