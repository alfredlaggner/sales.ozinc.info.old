<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\DeleteField;
use Laravel\Nova\Http\Requests\PivotFieldDestroyRequest;

class PivotFieldDestroyController extends Controller
{
    /**
     * Delete the file at the given field.
     *
     * @param PivotFieldDestroyRequest $request
     * @return Response
     */
    public function handle(PivotFieldDestroyRequest $request)
    {
        $request->authorizeForAttachment();

        DeleteField::forRequest(
            $request, $request->findFieldOrFail(),
            $pivot = $request->findPivotModel()
        )->save();

        ActionEvent::forAttachedResourceUpdate(
            $request, $request->findModelOrFail(), $pivot
        )->save();
    }
}
