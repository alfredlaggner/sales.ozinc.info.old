<?php

namespace App\Http\Controllers;

    use App\Exports\InvoiceNoteExport;
    use App\Exports\UsersExport;
    use App\Imports\ReceivableCommentsImport;
    use App\InvoiceNote;
    use Auth;
    use Gate;
    use Illuminate\Http\Request;
    use Illuminate\Support\Collection;
    use Maatwebsite\Excel\Facades\Excel;

    class NoteImportController extends Controller
    {
        public function importExportView()
        {
            return view('invoice_notes.import');
        }

        /**
         * @return Collection
         */
        public function import()
        {
            //	dd(request()->file('file'));

            Excel::import(new ReceivableCommentsImport, request()->file('file'));

            return back();
        }
    }
