<?php

namespace Laravel\Nova\Trix;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Trix;

class DeleteAttachments
{
    /**
     * The field instance.
     *
     * @var Trix
     */
    public $field;

    /**
     * Create a new class instance.
     *
     * @param  Trix  $field
     * @return void
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * Delete the attachments associated with the field.
     *
     * @param Request $request
     * @param  mixed  $model
     * @return void
     */
    public function __invoke(Request $request, $model)
    {
        Attachment::where('attachable_type', get_class($model))
                ->where('attachable_id', $model->getKey())
                ->get()
                ->each
                ->purge();

        return [$this->field->attribute => ''];
    }
}
