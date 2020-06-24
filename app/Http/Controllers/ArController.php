<?php
/**
 * @mixin Eloquent
 */

namespace App\Http\Controllers;

use App\AgedReceivable;
use App\AgedReceivablesTotal;
use App\Exports\AgedReceivableDetailExport;
use App\Exports\AgedReceivableTotalExport;
use App\Exports\InvoiceNoteExport;
use App\Invoice;
use App\InvoiceAmountCollect;
use App\InvoiceNote;
use App\Payment;
use Auth;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ArController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function customer_statement($customer_id = 6322, $customer_name = '')
    {
        $ledger = [];
        $total_amount = 0;
        $query = Invoice::whereYear('invoice_date', 2019)
            ->where('customer_id', $customer_id)
            ->get();
        $total_residual = 0;
        foreach ($query as $q) {
            $total_amount += $q->amount_total;
            $total_residual += $q->residual;
            /*                echo $q->invoice_date . " " . $q->name . " " . $q->due_date . " " . $q->payment_date . " " . $q->payment_amount . " " . $q->residual .  "<br>";*/
            array_push($ledger, [
                    'sales_order' => $q->sales_order,
                    'date' => $q->invoice_date,
                    'name' => $q->name,
                    'due' => $q->due_date,
                    'payment_date' => 0,
                    'amount' => $q->amount_total,
                    'payment_amount' => 0,
                    'residual' => $q->residual,
                    'difference' => 0, ]
            );
        }
        $query = Payment::whereYear('payment_date', 2019)
            ->where('customer_id', $customer_id)
            ->get();
        $total_payment = 0;
        foreach ($query as $q) {
            echo $q->residual.'<br>';
            $total_payment += $q->amount;
            array_push($ledger, [
                    'sales_order' => '',
                    'date' => $q->payment_date,
                    'name' => $q->move_name,
                    'due' => '',
                    'payment_date' => $q->payment_date,
                    'amount' => 0,
                    'payment_amount' => $q->amount,
                    'residual' => 0,
                    'difference' => $q->payment_difference, ]
            );
        }
        //     dd("xxx");
        $out_ledger = collect($ledger);
        $ledgers = $out_ledger->sortByDesc('date');
        //  dd($out_ledger);
        return PDF:: loadView('ar.pdf_customer_statement', compact('customer_name', 'ledgers', 'total_amount', 'total_residual', 'total_payment'))->stream('customer_statement.pdf');
        //   return view('ar.customer_statement', compact('customer_name','ledgers', 'total_amount','total_residual','total_payment'));
    }

    public function new_aged_receivables($rep_id, Request $request)
    {
        /*        Artisan::call('tntsearch:import', ['model' => 'App\AgedReceivable']);
                Artisan::call('tntsearch:import', ['model' => 'App\AgedReceivablesTotal']);*/

        $data = [];
        if (! $rep_id) {
            $rep_id = $request->get('rep_id');
        }

        $customer = $request->get('customer');

        session(['rep_id' => $rep_id]);

        if ($rep_id) {
            $ars = AgedReceivable::
            when($rep_id, function ($query, $rep_id) {
                return $query->where('rep_id', $rep_id);
            })
                ->orderBy('customer')
                ->get();
            if ($customer) {
                if ($value = Redis::get('ars_totals1.all')) {
                    $ars_totals = collect(json_decode($value));
                } else {
                    $ars_totals = AgedReceivablesTotal::search($customer)->where('rep_id', $rep_id)->get();
                    //       Redis::set('ars_totals1.all', $ars_totals);
                }
                session(['search_value' => '$customer']);
            } else {
                if ($value = Redis::get('ars_totals2.all')) {
                    $ars_totals = collect(json_decode($value));
                } else {
                    $ars_totals = AgedReceivablesTotal::
                    when($rep_id, function ($query, $rep_id) {
                        return $query->where('rep_id', $rep_id);
                    })->orderBy('customer')->get();
                    //          Redis::set('ars_totals2.all', $ars_totals);
                }

                session(['search_value' => $rep_id]);

                //   dd($ars_totals);
            }
        } else {
            if ($customer) {
                if ($value = Redis::get('ars_totals3.all')) {
                    $ars_totals = collect(json_decode($value));
                } else {
                    $ars_totals = AgedReceivablesTotal::search($customer)->get();
                    session(['search_value' => $customer]);
                    //            Redis::set('ars_totals3.all', $ars_totals);
                }
            } else {
                if ($value = Redis::get('ars_totals4.all')) {
                    $ars_totals = collect(json_decode($value));
                } else {
                    $ars_totals = AgedReceivablesTotal::orderBy('customer')->get();
                    //           Redis::set('ars_totals4.all', $ars_totals);
                    session(['search_value' => '']);
                }
            }
            if ($value = Redis::get('ars.all')) {
                $ars = collect(json_decode($value));
            } else {
                $ars = AgedReceivable::orderBy('customer')->get();
                //       Redis::set('ars.all', $ars);
            }
        }

        $amt_collects = InvoiceAmountCollect::orderBy('updated_at', 'desc')->get();
        $notes = InvoiceNote::orderBy('updated_at', 'desc')->get();

        //		$request->session()->forget('previous_screen'); // to start clean when adding notes

        return view('ar.accordion', compact('ars', 'ars_totals', 'notes', 'amt_collects', 'rep_id'));
    }

    public function export_aged_ar(Request $request)
    {
        //dd(Session::get('search_value')->toArray());
        return Excel::download(new AgedReceivableTotalExport(Session::get('search_value')), 'aged_receivables.xlsx');
    }

    public function export_aged_ar_detail(Request $request)
    {
        //dd(Session::get('search_value')->toArray());
        return Excel::download(new AgedReceivableDetailExport(Session::get('search_value')), 'aged_receivables_so.xlsx');
    }

    public function search_customer(Request $request)
    {
        $customer = $request->get('customer');
        $customers = AgedReceivablesTotal::search($customer)->get();
    }

    public function export(Request $request)
    {
        // dd($request->get('selection'));
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if (Gate::allows('isAdmin')) {
            $timespan = 'Last seven days';
            if ($request->get('selection') == 'last_week') {
                $data = InvoiceNote::where('created_at', '>=', date('Y-m-d', strtotime('-7 days')))->get();
            } elseif ($request->get('selection') == 'selection') {
                if (! $date_from or ! $date_to
                    or ($date_to < $date_from)) {
                    $data = InvoiceNote::where('created_at', '>=', date('Y-m-d', strtotime('-7 days')))->get();
                } else {
                    $timespan = 'From '.$date_from.' to '.$date_to;

                    $data = InvoiceNote::where('created_at', '>=', $date_from)
                        ->where('created_at', '>=', $date_to)
                        ->get();
                }
            } else {
                $data = InvoiceNote::get();
            }
        } else {
            $data = InvoiceNote::where('user_id', Auth::user()->id)->get();
        }
        $return_array = [];
        array_push($return_array, $data);
        array_push($return_array, [$timespan]);

        return Excel::download(new InvoiceNoteExport($return_array), 'ar_notes.xlsx');
    }

    public function export_ar_notes()
    {
        return view('ar.display_notes_selection');
    }

    public function aged_receivables(Request $request)
    {
        $is_expanded = $request->get('is_expanded');
        $is_grouped_by_reps = $request->get('is_grouped_by_reps');
        $is_print = $request->get('is_print');

        $is_expanded = $is_expanded ? true : false;
        $grouping = $is_grouped_by_reps ? "['rep','customer']" : "['customer']";

        $is_print = $is_print ? true : false;

        if ($is_print) {
            $is_expanded = true;
            $grouping = "['']";
        }

        $rep_id = 0;
        $rep_id = $request->get('rep_id');
        $customers = Invoice::select(DB::raw('
			    customer_id, 
                customer_name, 
                sales_person_id,
                sum(residual) as sum_residual
                '
        ))
            ->groupBy('customer_id')
            ->orderBy('customer_name')
            ->get();

        $data = [];

        foreach ($customers as $customer) {
            $invoices = Invoice::select(DB::raw("*,
				name,
				invoices.id as invoice_id,
				sales_person_id, 
				sales_person_name, 
				age, 
				invoice_date,
				residual,
				invoices.sales_order as i_sales_order,
				customer_name,
				CASE
					WHEN age <=0  THEN 'not today'
					WHEN age BETWEEN 1 and 7 THEN '1 - 7'
					WHEN age BETWEEN 8 and 14 THEN '8 - 14'
					WHEN age BETWEEN 15 and 30 THEN '15 - 30'
					WHEN age BETWEEN 31 and 60 THEN '31 - 60'
					WHEN age BETWEEN 61 and 90 THEN '61 - 90'
					WHEN age BETWEEN 91 and 120 THEN '91 - 120'
					WHEN age > 120 THEN 'Over 120'
					WHEN age IS NULL THEN 'Not Filled In (NULL)'
				END as age_range,
				CASE
					WHEN age <= 0 THEN 0
					WHEN age BETWEEN 1 and 7 THEN 1
					WHEN age BETWEEN 8 and 14 THEN 2
					WHEN age BETWEEN 15 and 30 THEN 3
					WHEN age BETWEEN 31 and 60 THEN 4
					WHEN age BETWEEN 61 and 90 THEN 5
					WHEN age BETWEEN 91 and 120 THEN 6
					WHEN age >= 120 THEN 7
					WHEN age IS NULL THEN 8
				END as age_rank 
						"))
                //			->leftjoin('invoice_amt_collects', 'invoices.sales_order', 'invoice_amt_collects.sales_order')
                ->orderBy('customer_name')
                ->orderBy('age_rank')
                ->when($rep_id, function ($query, $rep_id) {
                    return $query->where('sales_person_id', $rep_id);
                })
                ->where('customer_id', '=', $customer->customer_id)
                ->where('state', '!=', 'paid')
                ->where('type', '=', 'out_invoice')
                ->where('residual', '!=', 0)
                ->get();

            foreach ($invoices as $invoice) {
                $rank = [];
                for ($i = 0; $i < 9; $i++) {
                    if ($i == $invoice->age_rank) {
                        array_push($rank, $invoice->residual);
                    } else {
                        array_push($rank, null);
                    }
                }
                //               echo $invoice->id;
                $all_notes = '';
                $notes = InvoiceNote::where('invoice_id', '=', $invoice->invoice_id)
                    ->orderBy('id', 'desc')
                    ->get();

                //dd($notes->toArray());
                foreach ($notes as $note) {
                    $updated_at = new Carbon($note->updated_at);
                    $all_notes = $all_notes.$note->note.' (by '.$note->note_by.' '.$updated_at->format('m-d-Y').')'.PHP_EOL;
                }

                /*					if ($all_notes) {
                                        echo $all_notes;
                                        die();
                                    }*/

                array_push($data, [
                    'rep' => $invoice->sales_person_name,
                    'rep_id' => $invoice->sales_person_id,
                    'customer' => $invoice->customer_name,
                    'sales_order' => $invoice->i_sales_order,
                    'sum_residual' => $invoice->residual,
                    'amount_to_collect' => $invoice->amount_collect,
                    'amount_collected' => 0,
                    'note' => $all_notes,
                    'range0' => $rank[0],
                    'range1' => $rank[1],
                    'range2' => $rank[2],
                    'range3' => $rank[3],
                    'range4' => $rank[4],
                    'range5' => $rank[5],
                    'range6' => $rank[6],
                    'range7' => $rank[7],
                    'range8' => $rank[8],
                ]);
            }
            dd($data);
        }
        //   dd('xx');
        $request->session()->forget('previous_screen'); // to start clean when adding notes

        return view('tables.aged_receivables', ['data' => json_encode($data), 'header' => [], 'is_expanded' => $is_expanded, 'grouping' => $grouping]);
    }

    public function toggle_felon($customer_id)
    {
        $query = AgedReceivablesTotal::where('customer_id', $customer_id)->first();
        $is_felon = $query->is_felon;
        if ($is_felon < 3) {
            $is_felon++;
        } else {
            $is_felon = 0;
        }
        AgedReceivablesTotal::where('customer_id', $customer_id)->update(['is_felon' => $is_felon]);

        return back();
    }

    public function ajaxRequest()
    {
        return view('ajaxRequest');
    }

    public function ajaxRequestPost(Request $request)
    {
        $customer_id = $request->get('customer_id');

        $query = AgedReceivablesTotal::where('customer_id', $customer_id)->first();
        $is_felon = $query->is_felon;
        if ($is_felon < 3) {
            $is_felon++;
        } else {
            $is_felon = 0;
        }
        AgedReceivablesTotal::where('customer_id', $customer_id)->update(['is_felon' => $is_felon]);
        //    return back();
        $return = [];
        if ($is_felon == 0) {
            $return = [
                'old' => 'btn-danger',
                'new' => 'btn-success',
            ];
        } else {
            $return = [
                'new' => 'btn-danger',
                'old' => 'btn-success',
            ];
        }

        $new = 'btn-success';
        $old = 'btn-danger';

        if ($is_felon == 0) {
            $felony_state = 'ok';
            $felony_color = 'color: #3ADF00; font-size: 100%';
            $new = 'btn btn-sm btn-success';
            $old = 'btn btn-sm btn-danger';
        } elseif ($is_felon == 1) {
            $felony_state = 'bad';
            $felony_color = 'color: #FF8000; font-size: 110%';
            $new = 'btn btn-sm btn-warning';
            $old = 'btn btn-sm btn-success';
        } elseif ($is_felon == 2) {
            $felony_state = 'worse';
            $felony_color = 'color: #FF0000; font-size: 120%';
            $new = 'btn btn-sm btn-secondary';
            $old = 'btn btn-sm btn-warning';
        } elseif ($is_felon == 3) {
            $felony_state = 'worst';
            $felony_color = 'color:#FF0040; font-size: 120%';
            $new = 'btn btn-sm btn-danger';
            $old = 'btn btn-sm btn-warning';
        }
        $return = [
            'old' => $old,
            'new' => $new,
            'felony_state' => $felony_state,
            'felony_color' => $felony_color,
        ];

        return response()->json($return);
    }
}
