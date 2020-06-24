<?php

namespace App\Exports;

    use App\InvoiceNote;
    use Illuminate\Contracts\View\View;
    use Maatwebsite\Excel\Concerns\FromView;

    class InvoiceNoteExport implements FromView
    {
        private $data;

        public function __construct($data)
        {
            $this->data = $data;
        }

        public function view(): View
        {
            return view('exports.ar_notes', ['notes' => $this->data]);
        }
    }
