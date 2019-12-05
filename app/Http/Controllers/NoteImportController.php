<?php

	namespace App\Http\Controllers;

	use Gate;
	use Auth;
	use Illuminate\Http\Request;
	use App\Exports\UsersExport;
	use App\Exports\InvoiceNoteExport;
	use App\InvoiceNote;
	use App\Imports\ReceivableCommentsImport;
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
