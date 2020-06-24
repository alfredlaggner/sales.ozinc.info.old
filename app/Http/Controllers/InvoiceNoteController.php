<?php

namespace App\Http\Controllers;

    use App\AgedReceivablesTotal;
    use App\Invoice;
    use App\InvoiceAmountCollect;
    use App\InvoiceNote;
    use Auth;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Validation\Rules\In;

    class InvoiceNoteController extends Controller
    {
        public function __construct()
        {
            $this->middleware('auth');
        }

        public function tmp_update()
        {
            $notes = InvoiceNote::select('*', 'invoice_notes.id as notes_id', 'users.id as users_user_id')
                ->leftJoin('users', 'users.name', '=', 'invoice_notes.note_by')
                ->get();
            foreach ($notes as $note) {
                echo $note->notes_id;
                echo $note->name.'<br>';
                InvoiceNote::find($note->notes_id)
                    ->update(['user_id' => $note->users_user_id]);
            }
            $notes = InvoiceAmountCollect::select('*', 'invoice_amt_collects.id as notes_id', 'users.id as users_user_id')
                ->leftJoin('users', 'users.name', '=', 'invoice_amt_collects.note_by')
                ->get();
            foreach ($notes as $note) {
                echo $note->notes_id;
                echo $note->name.'<br>';
                InvoiceAmountCollect::find($note->notes_id)
                    ->update(['user_id' => $note->users_user_id]);
            }
        }

        public function index($so)
        {
            if (! $so) {
            }

            $invoices = Invoice::select('*', 'invoices.id as iid', 'invoices.sales_order as sales_order', 'invoice_notes.id as invoice_notes_id')
                ->where('invoices.sales_order', $so)
                ->leftjoin('invoice_notes', 'invoices.id', 'invoice_notes.invoice_id')
                ->orderby('invoice_notes.id', 'desc')
                ->get();
            $invoice = Invoice::select('*',
                'invoices.id as iid',
                'invoices.customer_id as invoices_customer_id',
                'invoices.sales_order as sales_order',
                'invoice_notes.id as invoice_notes_id'
            )
                ->where('invoices.sales_order', $so)
                ->leftjoin('invoice_notes', 'invoices.customer_id', 'invoice_notes.customer_id')
                ->leftjoin('invoice_amt_collects', 'invoices.customer_id', 'invoice_amt_collects.customer_id')
                ->first();

            $customer_id = $invoice->customer_id;
            //dd($customer_id);
            $customers = Invoice::select(DB::raw('
			    customer_id, 
                customer_name, 
                sales_person_id,
                sum(residual) as sum_residual
                '
            ))
                ->where('invoices.customer_id', $customer_id)
                ->groupBy('customer_id')
                ->orderBy('customer_name')
                ->first();

            $residual = $customers->sum_residual;

            $amt_collects = InvoiceAmountCollect::where('customer_id', $customer_id)->get();
            // dd($invoice);
            //     $initial = true;
            return view('invoice_notes.list', compact('invoices', 'invoice', 'residual', 'amt_collects'));
        }

        public function list_notes($customer_id)
        {
            $amt_collects = InvoiceAmountCollect::where('customer_id', $customer_id)->get();
            $notes = InvoiceNote::where('customer_id', $customer_id)->get();

            return view('invoice_notes.list', compact('invoices', 'invoice', 'residual', 'amt_collects'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return Response
         */
        public function create()
        {
            return view('invoice_notes.create');
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param Request $request
         * @return Response
         */
        public function store(Request $request)
        {
            $url = $request->input('url');
            $customer_id = $request->get('customer_id');

            if ($request->get('return') == 'return') {
                if ($request->session()->has('previous_screen')) {
                    $url = session('previous_screen');
                    $request->session()->forget('previous_screen');
                }
            }

            if (! $request->session()->has('previous_screen')) {
                session(['previous_screen' => $url]);
            }

            if ($request->get('collect') == 'collect') {
                $receivableTotal = AgedReceivablesTotal::where('customer_id', $customer_id)->get()->toArray();
                $collect = new InvoiceAmountCollect();

                $collect->customer_id = $customer_id;
                $collect->user_id = Auth::user()->id;
                $collect->note_by = Auth::user()->name;
                $collect->amount_to_collect = $request->get('amount_to_collect');
                $collect->saved_residual = $receivableTotal[0]['customer_total'];
                $collect->customer_name = $request->get('customer_name');

                $collect->save();
            }

            if ($request->get('notes') == 'notes') {
                $invoice_note = new InvoiceNote();

                $invoice_note->note = $request->get('note');
                $invoice_note->user_id = Auth::user()->id;
                $invoice_note->note_by = Auth::user()->name;
                $invoice_note->customer_id = $customer_id;
                $invoice_note->customer_name = $request->get('customer_name');

                $invoice_note->save();
            }
            $rep_id = $request->session()->get('rep_id');

            return redirect(route('aged_receivables1', [$rep_id]));
        }

        public function store2_old(Request $request)
        {
            $url = $request->input('url');
            //dd($url);
            if ($request->get('return') == 'return') {
                if ($request->session()->has('previous_screen')) {
                    $url = session('previous_screen');
                    $request->session()->forget('previous_screen');
                }

                return redirect($url);
            }

            if (! $request->session()->has('previous_screen')) {
                session(['previous_screen' => $url]);
            }

            $sales_order = $request->get('sales_order');
            $customer_id = $request->get('customer_id');

            /*check if amount is changed*/
            //dd($sales_order);
            InvoiceAmountCollect:: updateOrCreate(
                ['customer_id' => $customer_id],
                ['amount_to_collect' => $request->get('amount_to_collect'),
                    'customer_id' => $customer_id,
                    'sales_order' => $sales_order,
                    'saved_residual' => $request->get('residual'),

                ]);

            $invoice_note = new InvoiceNote();

            $invoice_note->note = $request->get('note');
            $invoice_note->amount_to_collect = $request->get('amount_to_collect');
            $invoice_note->note_by = Auth::user()->name;
            $invoice_note->sales_order = $sales_order;
            $invoice_note->invoice_id = $request->get('invoice_id');
            $invoice_note->customer_id = $customer_id;

            $invoice_note->save();

            return redirect()->route('show_notes', ['so' => $request->get('sales_order')]);
        }

        public function store1_old(Request $request)
        {
            if ($request->get('return') == 'return') {
                return redirect()->route('aged_receivables');
                //    return redirect()->action('DevelopController@aged_receivables');
            }
            $note = InvoiceNote::where('invoice_id', $request->get('invoice_id'))->first();
            if ($note) {
                $next_line =
                    '<p> '.
                    $request->get('note').
                    '<span style="font-size: 8pt;" data-mce-style="font-size: 8pt;"> by '.Auth::user()->name.
                    ' at '.Carbon::now()->format('m-d-Y').
                    '</span></p>';
                $new_note = $note->note.$next_line;
            } else {
                $first_line =
                    '<p> '.
                    $request->get('note').
                    '<span style="font-size: 8pt;" data-mce-style="font-size: 8pt;"> by '.Auth::user()->name.
                    ' at '.Carbon::now()->format('m-d-Y').
                    '</span></p>';
                $new_note = $first_line;
            }

            InvoiceNote::updateOrCreate(
                ['invoice_id' => $request->get('invoice_id')],
                [
                    'note' => $new_note,
                    'note_by' => Auth::user()->name,
                ]
            );

            return redirect()->route('show_notes', ['so' => $request->get('sales_order')]);
            //     return redirect()->route('aged_receivables');
        }

        /**
         * Display the specified resource.
         *
         * @param int $id
         * @return Response
         */
        public function show($id)
        {
            //
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param int $id
         * @return Response
         */
        public function edit($id)
        {
            //
        }

        /**
         * Update the specified resource in storage.
         *
         * @param Request $request
         * @param int $id
         * @return Response
         */
        public function update(Request $request, $id)
        {
            //
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param int $id
         * @return Response
         */
        public function destroy($id)
        {
            //
        }
    }
