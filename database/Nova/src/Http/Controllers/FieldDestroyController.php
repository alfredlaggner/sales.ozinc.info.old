<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\DeleteField;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Http\Requests\NovaRequest;

class FieldDestroyController extends Controller
{
    /**
     * Delete the file at the given field.
     *
     * @param NovaRequest $request
     * @return Response
     */
    public function handle(NovaRequest $request)
    {
        $resource = $request->findResourceOrFail();

        $resource->authorizeToUpdate($request);

        $field = $resource->updateFields($request)
                    ->whereInstanceOf(File::class)
                    ->findFieldByAttribute($request->field, function () {
                        abort(404);
                    });

        DeleteField::forRequest(
            $request, $field, $resource->resource
        )->save();

        ActionEvent::forResourceUpdate(
            $request->user(), $resource->resource
        )->save();
    }
}
