<?php

namespace Laravel\Nova;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;

trait Authorizable
{
    /**
     * Determine if the given resource is authorizable.
     *
     * @return bool
     */
    public static function authorizable()
    {
        return ! is_null(Gate::getPolicyFor(static::newModel()));
    }

    /**
     * Determine if the resource should be available for the given request.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizeToViewAny(Request $request)
    {
        if (! static::authorizable()) {
            return;
        }

        if (method_exists(Gate::getPolicyFor(static::newModel()), 'viewAny')) {
            $this->authorizeTo($request, 'viewAny');
        }
    }

    /**
     * Determine if the resource should be available for the given request.
     *
     * @param Request $request
     * @return bool
     */
    public static function authorizedToViewAny(Request $request)
    {
        if (! static::authorizable()) {
            return true;
        }

        return method_exists(Gate::getPolicyFor(static::newModel()), 'viewAny')
                        ? Gate::check('viewAny', get_class(static::newModel()))
                        : true;
    }

    /**
     * Determine if the current user can view the given resource or throw an exception.
     *
     * @param Request $request
     * @return void
     *
     * @throws AuthorizationException
     */
    public function authorizeToView(Request $request)
    {
        return $this->authorizeTo($request, 'view') && $this->authorizeToViewAny($request);
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizedToView(Request $request)
    {
        return $this->authorizedTo($request, 'view') && $this->authorizedToViewAny($request);
    }

    /**
     * Determine if the current user can create new resources or throw an exception.
     *
     * @param Request $request
     * @return void
     *
     * @throws AuthorizationException
     */
    public static function authorizeToCreate(Request $request)
    {
        throw_unless(static::authorizedToCreate($request), AuthorizationException::class);
    }

    /**
     * Determine if the current user can create new resources.
     *
     * @param Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        if (static::authorizable()) {
            return Gate::check('create', get_class(static::newModel()));
        }

        return true;
    }

    /**
     * Determine if the current user can update the given resource or throw an exception.
     *
     * @param Request $request
     * @return void
     *
     * @throws AuthorizationException
     */
    public function authorizeToUpdate(Request $request)
    {
        return $this->authorizeTo($request, 'update');
    }

    /**
     * Determine if the current user can update the given resource.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return $this->authorizedTo($request, 'update');
    }

    /**
     * Determine if the current user can delete the given resource or throw an exception.
     *
     * @param Request $request
     * @return void
     *
     * @throws AuthorizationException
     */
    public function authorizeToDelete(Request $request)
    {
        return $this->authorizeTo($request, 'delete');
    }

    /**
     * Determine if the current user can delete the given resource.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizedToDelete(Request $request)
    {
        return $this->authorizedTo($request, 'delete');
    }

    /**
     * Determine if the current user can restore the given resource.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizedToRestore(Request $request)
    {
        return $this->authorizedTo($request, 'restore');
    }

    /**
     * Determine if the current user can force delete the given resource.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizedToForceDelete(Request $request)
    {
        return $this->authorizedTo($request, 'forceDelete');
    }

    /**
     * Determine if the user can add / associate models of the given type to the resource.
     *
     * @param NovaRequest $request
     * @param  Model|string  $model
     * @return bool
     */
    public function authorizedToAdd(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'add'.class_basename($model);

        return method_exists(Gate::getPolicyFor($this->model()), $method)
                        ? Gate::check($method, $this->model())
                        : true;
    }

    /**
     * Determine if the user can attach any models of the given type to the resource.
     *
     * @param NovaRequest $request
     * @param  Model|string  $model
     * @return bool
     */
    public function authorizedToAttachAny(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'attachAny'.Str::singular(class_basename($model));

        return method_exists(Gate::getPolicyFor($this->model()), $method)
                    ? Gate::check($method, [$this->model()])
                    : true;
    }

    /**
     * Determine if the user can attach models of the given type to the resource.
     *
     * @param NovaRequest $request
     * @param  Model|string  $model
     * @return bool
     */
    public function authorizedToAttach(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'attach'.Str::singular(class_basename($model));

        return method_exists(Gate::getPolicyFor($this->model()), $method)
                    ? Gate::check($method, [$this->model(), $model])
                    : true;
    }

    /**
     * Determine if the user can detach models of the given type to the resource.
     *
     * @param NovaRequest $request
     * @param  Model|string  $model
     * @param  string  $relationship
     * @return bool
     */
    public function authorizedToDetach(NovaRequest $request, $model, $relationship)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'detach'.Str::singular(class_basename($model));

        return method_exists(Gate::getPolicyFor($this->model()), $method)
                    ? Gate::check($method, [$this->model(), $model])
                    : true;
    }

    /**
     * Determine if the current user has a given ability.
     *
     * @param Request $request
     * @param  string  $ability
     * @return void
     *
     * @throws AuthorizationException
     */
    public function authorizeTo(Request $request, $ability)
    {
        throw_unless($this->authorizedTo($request, $ability), AuthorizationException::class);
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param Request $request
     * @param  string  $ability
     * @return bool
     */
    public function authorizedTo(Request $request, $ability)
    {
        return static::authorizable() ? Gate::check($ability, $this->resource) : true;
    }
}
