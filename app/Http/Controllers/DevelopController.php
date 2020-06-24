<?php

namespace App\Http\Controllers;

    use App\AgedReceivable;
    use App\AgedReceivablesTotal;
    use App\Commission;
    use App\Customer;
    use App\Earning;
    use App\Earning2;
    use App\Exports\AgedReceivableDetailExport;
    use App\Exports\AgedReceivableTotalExport;
    use App\Invoice;
    use App\InvoiceAmountCollect;
    use App\Invoiceline;
    use App\InvoiceNote;
    use App\Margin;
    use App\Month;
    use App\SaleInvoice;
    use App\Salesline;
    use App\SalesOrder;
    use App\SalesPerson;
    use App\SavedCommission;
    use App\Traits\CommissionTrait;
    use App\User;
    use Auth;
    use Carbon\Carbon;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Lava;
    use Maatwebsite\Excel\Facades\Excel;
    use Session;
    use Yajra\Datatables\Datatables;

    class DevelopController extends Controller
    {
        public function __construct()
        {
            $this->middleware('auth');
        }

        use CommissionTrait;

        public function index(Request $request)
        {
            if ($request->session()->exists('data')) {
                $data = $request->session()->get('data');
            } else {
                $data = [
                    'month' => $request->get('month'),
                    'salesperson_id' => $request->get('salesperson_id'), ];
            }

            $salesperson = User::where('user_type', '=', 'salesperson')->get();
            //		dd($salesperson);
            $months = Month::all();
            $now = Carbon::now();
            $months = Month::where('month_id', '<=', $now->month)
                ->orderBy('month_id', 'desc')
                ->get();
            //	dd($months);
            $user = User::where('id', '=', auth()->id())->first();
            $salesperson_id = $user->sales_person_id;
            $salesperson_name = $user->name;

            return view('tables.admin', ['salesperson_name' => $salesperson_name, 'salesperson_id' => $salesperson_id, 'data' => $data, 'months' => $months, 'currentMonth' => Carbon::now()->month, 'salesperson' => $salesperson]);
        }

        public function all_products(Request $request)
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
            $data = [];
            $rep_id = 0;
            $rep_id = $request->get('rep_id');

            $products = Margin::orderby('brand')->get();

            foreach ($products as $product) {
                array_push($data, [
                    'code' => $product->code,
                    'name' => $product->name,
                    'brand' => $product->brand,
                    'cost' => $product->cost,
                    'revenue' => $product->revenue,
                    'margin' => $product->margin,
                    'commission' => $product->commission_percent * 100,
                ]);
            }

            return view('tables.products', ['data' => json_encode($data), 'header' => [], 'is_expanded' => $is_expanded, 'grouping' => $grouping]);
        }

        public function new_aged_receivables($rep_id, Request $request)
        {
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
                    $ars_totals = AgedReceivablesTotal::search($customer)->where('rep_id', $rep_id)->get();
                    session(['search_value' => '$customer']);
                } else {
                    $ars_totals = AgedReceivablesTotal::
                    when($rep_id, function ($query, $rep_id) {
                        return $query->where('rep_id', $rep_id);
                    })->orderBy('customer')->get();

                    session(['search_value' => $rep_id]);

                    //   dd($ars_totals);
                }
            } else {
                if ($customer) {
                    $ars_totals = AgedReceivablesTotal::search($customer)->get();
                    session(['search_value' => $customer]);
                } else {
                    $ars_totals = AgedReceivablesTotal::orderBy('customer')->get();
                    session(['search_value' => '']);
                }

                $ars = AgedReceivable::orderBy('customer')->get();
            }

            $amt_collects = InvoiceAmountCollect::orderBy('updated_at', 'desc')->get();
            $notes = InvoiceNote::orderBy('updated_at', 'desc')->get();

            //		$request->session()->forget('previous_screen'); // to start clean when adding notes

            return view('tables.ar_accordion', compact('ars', 'ars_totals', 'notes', 'amt_collects', 'rep_id'));
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

        public function xxxcreateSavedCommission(Request $request)
        {
            $timeFrame = ['year' => 2019, 'months' => [Carbon::now()->month]];
            $timeFrame = ['year' => $request->get('year'), 'months' => [$request->get('months')]];

            //	dd($timeFrame);
            $is_add = true;
            /*			if ($request->session()->exists('admin_data')) {
                            $data = $request->session()->get('admin_data');

                        } else {
                            $timeFrame = [
                                'months' => $request->get('months'),
                                'year' => $request->get('year')];
                            $is_add = $request->get('add_to_table');
                        }*/
            $lastMonth = end($timeFrame['months']);
            $dateFrom = substr(new Carbon($timeFrame['year'].'-'.$timeFrame['months'][0].'-01'), 0, 10);
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-01'), 0, 10);
            $lastDay = date('t', strtotime($dateTo));
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-'.$lastDay), 0, 10);

            $paidInvoiveOnly = $dateFrom >= '2019-06-01';

            $queries = SalesLine::select(DB::raw('*,EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month'))
                ->whereBetween('saleslines.invoice_date', [$dateFrom, $dateTo])
                ->where('amount', '>=', 0)
                /*             ->where('saleslines.state', '=', 'paid')
             /*            ->when($paidInvoiveOnly, function ($query) {
                                return $query->where('saleslines.state', '=', 'paid');
                            })*/
                ->orderBy('summary_year_month')
                ->orderBy('customer_name', 'asc')
                ->orderBy('order_number', 'desc')
                ->get();
            //	   ->toArray();
            //	dd($queries);
            //   dd($queries->count());
            $is_add = true;

            if ($is_add == true) {
                $now = Carbon::now();
                $currentTime = $now->format('_Y_m_d_h_i_s');
                $newtable = 'saleslines'.$currentTime;
                //			echo $newtable;
                Schema::create($newtable, function (Blueprint $table) {
                    $table->increments('id')->unique();
                    $table->char('month', 255)->nullable();
                    $table->date('order_date')->nullable();
                    $table->date('invoice_date')->nullable();
                    $table->char('order_number', 255)->nullable();
                    $table->char('name', 255)->nullable();
                    $table->char('customer_name', 255)->nullable();
                    $table->char('rep', 255)->nullable();
                    $table->char('sku', 255)->nullable();
                    $table->char('brand_name', 255)->nullable();
                    $table->char('category', 255)->nullable();
                    //	$table->integer('qty_invoiced')->nullable();
                    $table->integer('quantity')->nullable();
                    $table->integer('sales_person_id')->nullable();
                    $table->double('cost', 8, 2)->nullable();
                    $table->double('unit_price', 8, 2)->nullable();
                    $table->double('commission_percent', 8, 4)->nullable();
                    $table->double('commission', 8, 2)->nullable();
                    $table->double('amount', 8, 2)->nullable();
                    $table->double('amount_tax', 8, 2)->nullable();
                    $table->double('amount_untaxed', 8, 2)->nullable();
                    $table->double('amount_total', 8, 2)->nullable();
                    $table->double('margin', 8, 2)->nullable();
                    $table->timestamps();
                });
            }

            $data = [];
            $sc = new SavedCommission;
            $sc->description = 'add description';
            $sc->name = $newtable;
            $sc->date_created = $now;
            $sc->month = $request->get('months');
            $sc->year = $request->get('year');
            $sc->created_by = Auth::user()->name;
            $sc->save();

            foreach ($queries as $query) {
                $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
                $line = [
                    'month' => $month.' '.substr($query->summary_year_month, 0, 4),
                    'sales_person_id' => $query->sales_person_id,
                    'order_date' => $query->order_date,
                    'invoice_date' => $query->invoice_date,
                    'order_number' => $query->order_number,
                    'customer_name' => substr($query->customer_name, 0, 20),
                    'rep' => substr($query->rep, 0, 20),
                    'name' => $query->name,
                    'sku' => $query->sku,
                    'brand_name' => $query->brand_name,
                    'category' => $query->product_category,
                    //		'qty_invoiced' => $query->qty_invoiced,
                    'quantity' => $query->quantity,
                    'commission_percent' => $query->comm_percent,
                    'commission' => $query->commission,
                    'cost' => $query->cost,
                    'margin' => $query->margin,
                    'unit_price' => $query->unit_price,
                    'amount' => $query->amount,
                    'amount_tax' => $query->amount_tax,
                    'amount_untaxed' => $query->amount_untaxed,
                    'amount_total' => $query->amount_total,
                    'created_at' => now(),
                ];
                DB::table($newtable)->insert($line);
            }
            //   dd($newtable);
            session()->put($newtable);

            return redirect()->route('admin');
        }

        public function totalDetails(Request $request)
        {
            $timeFrame = ['year' => 2019, 'months' => [1, 2, 3]];
            if ($request->session()->exists('admin_data')) {
                $data = $request->session()->get('admin_data');
            } else {
                $timeFrame = [
                    'months' => $request->get('months'),
                    'year' => $request->get('year'), ];
            }
            $lastMonth = end($timeFrame['months']);
            $dateFrom = substr(new Carbon($timeFrame['year'].'-'.$timeFrame['months'][0].'-01'), 0, 10);
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-01'), 0, 10);
            $lastDay = date('t', strtotime($dateTo));
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-'.$lastDay), 0, 10);
            $queries = SalesLine::select(DB::raw('*,EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month'))
                ->whereBetween('saleslines.invoice_date', [$dateFrom, $dateTo])
                ->orderBy('summary_year_month')
                //   ->groupBy('summary_year_month')
                ->get();
            //    ->toJson();
            //   dd($queries->count());
            $data = [];
            foreach ($queries as $query) {
                $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
                array_push($data, [
                    'month' => $month.' '.substr($query->summary_year_month, 0, 4),
                    'order_date' => $query->order_date,
                    'invoice_date' => $query->invoice_date,
                    'order_number' => $query->order_number,
                    'customer_name' => substr($query->customer_name, 0, 20),
                    'rep' => substr($query->rep, 0, 20),
                    'name' => $query->name,
                    'sku' => $query->sku,
                    'brand_name' => $query->brand_name,
                    'category' => $query->product_category,
                    //			'qty_invoiced' => $query->qty_invoiced,
                    'quantity' => $query->quantity,
                    'commission_percent' => $query->comm_percent * 100,
                    'commission' => $query->commission,
                    'cost' => $query->cost,
                    'margin' => $query->margin,
                    'unit_price' => $query->unit_price,
                    'amount' => $query->amount,
                    'amount_tax' => $query->amount_tax,
                    'amount_untaxed' => $query->amount_untaxed,
                    'amount_total' => $query->amount_total,
                ]);
            }
            //dd(json_encode($data));
            return view('tables.total_details', ['data' => json_encode($data)]);
        }

        public function cleanOutSales(Request $request)
        {
            $timeFrame = ['year' => 2019, 'months' => [1, 2, 3]];
            if ($request->session()->exists('admin_data')) {
                $data = $request->session()->get('admin_data');
            } else {
                $timeFrame = [
                    'months' => $request->get('months'),
                    'year' => $request->get('year'), ];
            }
            $lastMonth = end($timeFrame['months']);
            $dateFrom = substr(new Carbon($timeFrame['year'].'-'.$timeFrame['months'][0].'-01'), 0, 10);
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-01'), 0, 10);
            $lastDay = date('t', strtotime($dateTo));
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-'.$lastDay), 0, 10);
            $queries = SalesLine::select(DB::raw('*,EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month'))
                ->whereBetween('saleslines.invoice_date', [$dateFrom, $dateTo])
                ->where('saleslines.state', 'paid')
                ->where(function ($q) {
                    $q->where('saleslines.amount', '>=', 1);
                    $q->where('saleslines.cost', 0);
                    $q->orWhere('saleslines.margin', '<', 0);
                })
                ->orderBy('summary_year_month')
                //   ->groupBy('summary_year_month')
                ->get();
            //    ->toJson();
            //	dd($queries->count());

            $data = [];
            foreach ($queries as $query) {
                $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
                array_push($data, [
                    'month' => $month.' '.substr($query->summary_year_month, 0, 4),
                    'order_date' => $query->order_date,
                    'invoice_date' => $query->invoice_date,
                    'order_number' => $query->order_number,
                    'customer_name' => substr($query->customer_name, 0, 20),
                    'rep' => substr($query->rep, 0, 20),
                    'name' => $query->name,
                    'sku' => $query->sku,
                    'brand_name' => $query->brand_name,
                    'category' => $query->product_category,
                    //			'qty_invoiced' => $query->qty_invoiced,
                    'quantity' => $query->quantity,
                    'commission_percent' => $query->comm_percent * 100,
                    'commission' => $query->commission,
                    'cost' => $query->cost,
                    'state' => $query->state,
                    'margin' => $query->margin,
                    'unit_price' => $query->unit_price,
                    'amount' => $query->amount,
                    'amount_tax' => $query->amount_tax,
                    'amount_untaxed' => $query->amount_untaxed,
                    'amount_total' => $query->amount_total,
                ]);
            }

            return view('tables.total_details', ['data' => json_encode($data)]);
        }

        public function totalSalesorders(Request $request)
        {
            $timeFrame = ['year' => 2019, 'months' => [1, 2, 3]];
            $is_add = true;
            if ($request->session()->exists('admin_data')) {
                $data = $request->session()->get('admin_data');
            } else {
                $timeFrame = [
                    'months' => $request->get('months'),
                    'year' => $request->get('year'), ];
                $is_add = $request->get('add_to_table');
            }
            $lastMonth = end($timeFrame['months']);
            $dateFrom = substr(new Carbon($timeFrame['year'].'-'.$timeFrame['months'][0].'-01'), 0, 10);
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-01'), 0, 10);
            $lastDay = date('t', strtotime($dateTo));
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-'.$lastDay), 0, 10);

            $queries = SalesLine::select(DB::raw('*,EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month'))
                ->whereBetween('saleslines.invoice_date', [$dateFrom, $dateTo])
                ->where('amount', '>=', 0)
                ->orderBy('summary_year_month')
                ->orderBy('customer_name', 'asc')
                ->orderBy('order_number', 'desc')
                ->get();
            //    ->toJson();
            //   dd($queries->count());
            $is_add = false;

            if ($is_add == true) {
                $currentTime = Carbon::now()->format('_Y_m_d_h_i_s');
                $newtable = 'saleslines'.$currentTime;
                //			echo $newtable;
                Schema::create($newtable, function (Blueprint $table) {
                    $table->increments('id')->unique();
                    $table->char('month', 255)->nullable();
                    $table->date('order_date')->nullable();
                    $table->date('invoice_date')->nullable();
                    $table->char('order_number', 255)->nullable();
                    $table->char('name', 255)->nullable();
                    $table->char('customer_name', 255)->nullable();
                    $table->char('rep', 255)->nullable();
                    $table->char('sku', 255)->nullable();
                    $table->char('brand_name', 255)->nullable();
                    $table->char('category', 255)->nullable();
                    $table->integer('qty_invoiced')->nullable();
                    $table->integer('quantity')->nullable();
                    $table->double('cost', 8, 2)->nullable();
                    $table->double('unit_price', 8, 2)->nullable();
                    $table->double('commission_percent', 8, 2)->nullable();
                    $table->double('commission', 8, 2)->nullable();
                    $table->double('amount', 8, 2)->nullable();
                    $table->double('amount_tax', 8, 2)->nullable();
                    $table->double('amount_untaxed', 8, 2)->nullable();
                    $table->double('amount_total', 8, 2)->nullable();
                    $table->double('margin', 8, 2)->nullable();
                    $table->timestamps();
                });
            }

            $data = [];
            foreach ($queries as $query) {
                $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
                $line = [
                    'month' => $month.' '.substr($query->summary_year_month, 0, 4),
                    'order_date' => $query->order_date,
                    'invoice_date' => $query->invoice_date,
                    'order_number' => $query->order_number,
                    'customer_name' => substr($query->customer_name, 0, 20),
                    'rep' => substr($query->rep, 0, 20),
                    'name' => $query->name,
                    'sku' => $query->sku,
                    'brand_name' => $query->brand_name,
                    'category' => $query->product_category,
                    'qty_invoiced' => $query->qty_invoiced,
                    'quantity' => $query->quantity,
                    'commission_percent' => $query->comm_percent * 100,
                    'commission' => $query->commission,
                    'cost' => $query->cost,
                    'margin' => $query->margin,
                    'unit_price' => $query->unit_price,
                    'amount' => $query->amount,
                    'amount_tax' => $query->amount_tax,
                    'amount_untaxed' => $query->amount_untaxed,
                    'amount_total' => $query->amount_total,
                    'created_at' => now(),
                ];
                array_push($data, $line);
                if ($is_add) {
                    DB::table($newtable)->insert($line);
                }
            }
            //dd(json_encode($data));
            return view('tables.total_salesorders', ['data' => json_encode($data), 'header' => [], 'overview' => 0]);
        }

        public function selectCustomer(Request $request)
        {
            $customers = Customer::search($request->get('customer'))->get();
            /*        foreach($customers as $customer){
                        if ($customer->has('sales_lines')){
                            echo "has sales line" . "<br>";
                        }else{
                        }else{s
                            echo "no sales line" . "<br>";
                        }
                    }
                    dd("vvv");*/
            return view('customers_table', compact('customers'));
        }

        public function calcPerCustomer(Request $request)
        {
            $timeFrame = ['year' => 2019, 'months' => [1, 2, 3]];
            if ($request->session()->exists('admin_data')) {
                $data = $request->session()->get('admin_data');
            } else {
                $timeFrame = [
                    'months' => $request->get('months'),
                    'year' => $request->get('year'), ];
            }
            $lastMonth = end($timeFrame['months']);
            $dateFrom = substr(new Carbon($timeFrame['year'].'-'.$timeFrame['months'][0].'-01'), 0, 10);
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-01'), 0, 10);
            $lastDay = date('t', strtotime($dateTo));
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-'.$lastDay), 0, 10);

            $queries = SaleInvoice::select(DB::raw('invoice_lines.customer_id as customer_id,customers.name as customer_name,
                sum(commission) as sp_commission,
                sum(price_subtotal) as sp_volume,
                avg(NULLIF(margin,0))as sp_margin,
                EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
                '))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->whereBetween('invoice_lines.invoice_date', [$dateFrom, $dateTo])
                ->orderBy('customer_name')
                //    ->orderBy('summary_year_month')
                ->groupBy('invoice_lines.customer_id')
                ->groupBy('summary_year_month')
                ->get();
            $data = [];
            foreach ($queries as $query) {
                if ($query->sp_volume) {
                    $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
                    array_push($data, [
                        'customer_name' => $query->customer_name,
                        'commission' => $query->sp_commission,
                        'volume' => $query->sp_volume,
                        'margin' => $query->sp_margin,
                        'month' => $month.' '.substr($query->summary_year_month, 0, 4),
                    ]);
                }
            }
            //   dd(json_encode($data));

            return view('tables.total_customers', ['data' => json_encode($data)]);
        }

        public function calcPerProduct(Request $request)
        {
            $timeFrame = ['year' => 2019, 'months' => [1, 2, 3]];
            if ($request->session()->exists('admin_data')) {
                $data = $request->session()->get('admin_data');
            } else {
                $timeFrame = [
                    'months' => $request->get('months'),
                    'year' => $request->get('year'), ];
            }
            $lastMonth = end($timeFrame['months']);
            $dateFrom = substr(new Carbon($timeFrame['year'].'-'.$timeFrame['months'][0].'-01'), 0, 10);
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-01'), 0, 10);
            $lastDay = date('t', strtotime($dateTo));
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-'.$lastDay), 0, 10);

            $queries = SaleInvoice::select(DB::raw('invoice_lines.product_id as product_id,invoice_lines.name as product_name,
                sum(commission) as sp_commission,
                sum(price_subtotal) as sp_volume,
                avg(NULLIF(invoice_lines.margin,0))as sp_margin,
                EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
                '))
                ->leftJoin('margins', 'margins.ext_id', '=', 'invoice_lines.product_id')
                ->whereBetween('invoice_lines.invoice_date', [$dateFrom, $dateTo])
                ->orderBy('product_name')
                //    ->orderBy('summary_year_month')
                ->groupBy('product_name')
                ->groupBy('summary_year_month')
                ->get();
            $data = [];
            foreach ($queries as $query) {
                if ($query->sp_volume) {
                    $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
                    array_push($data, [
                        'product_name' => $query->product_name,
                        'commission' => $query->sp_commission,
                        'volume' => $query->sp_volume,
                        'margin' => $query->sp_margin,
                        'month' => $month.' '.substr($query->summary_year_month, 0, 4),
                    ]);
                }
            }

            return view('tables.total_products', ['data' => json_encode($data)]);
        }

        public function calcPerBrand(Request $request)
        {
            $timeFrame = ['year' => 2019, 'months' => [1, 2, 3]];
            if ($request->session()->exists('admin_data')) {
                $data = $request->session()->get('admin_data');
            } else {
                $timeFrame = [
                    'months' => $request->get('months'),
                    'year' => $request->get('year'), ];
            }
            $lastMonth = end($timeFrame['months']);
            $dateFrom = substr(new Carbon($timeFrame['year'].'-'.$timeFrame['months'][0].'-01'), 0, 10);
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-01'), 0, 10);
            $lastDay = date('t', strtotime($dateTo));
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-'.$lastDay), 0, 10);

            $queries = SaleInvoice::select(DB::raw('invoice_lines.brand_id as brand_id,brands.name as brand_name,
                sum(commission) as sp_commission,
                sum(price_subtotal) as sp_volume,
                avg(NULLIF(margin,0))as sp_margin,
                EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
                '))
                ->leftJoin('brands', 'brands.ext_id', '=', 'invoice_lines.brand_id')
                ->whereBetween('invoice_lines.invoice_date', [$dateFrom, $dateTo])
                ->orderBy('brand_name')
                //	->orderBy('summary_year_month')
                ->groupBy('brand_name')
                ->groupBy('summary_year_month')
                ->get();
            $data = [];
            foreach ($queries as $query) {
                if ($query->sp_volume and $query->brand_name) {
                    if ($query->brand_name != null) {
                        $brand_name = $query->brand_name;
                    } else {
                        $brand_name = 'no brand name';
                    }
                    $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
                    array_push($data, [
                        'brand_name' => $brand_name,
                        'commission' => $query->sp_commission,
                        'volume' => $query->sp_volume,
                        'margin' => $query->sp_margin,
                        'month' => $month.' '.substr($query->summary_year_month, 0, 4),
                    ]);
                }
            }

            return view('tables.total_brands', ['data' => json_encode($data)]);
        }

        public function calcPerSalesPerson(Request $request)
        {
            $timeFrame = ['year' => 2019, 'months' => [1, 2, 3]];
            if ($request->session()->exists('admin_data')) {
                $data = $request->session()->get('admin_data');
            } else {
                $timeFrame = [
                    'months' => $request->get('months'),
                    'year' => $request->get('year'), ];
            }
            $lastMonth = end($timeFrame['months']);
            $dateFrom = substr(new Carbon($timeFrame['year'].'-'.$timeFrame['months'][0].'-01'), 0, 10);
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-01'), 0, 10);
            $lastDay = date('t', strtotime($dateTo));
            $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-'.$lastDay), 0, 10);

            $queries = SaleInvoice::select(DB::raw('invoice_lines.sales_person_id as salesperson_id,sales_persons.name as salesperson_name,
                sum(commission) as sp_commission,
                sum(price_subtotal) as sp_volume,
                avg(NULLIF(margin,0))as sp_margin,
                EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
                '))
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '!=', null)
                ->whereBetween('invoice_lines.invoice_date', [$dateFrom, $dateTo])
                ->orderBy('salesperson_name')
                ->orderBy('summary_year_month')
                ->groupBy('invoice_lines.sales_person_id')
                ->groupBy('summary_year_month')
                ->get();
            $data = [];
            foreach ($queries as $query) {
                if ($query->sp_volume) {
                    $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
                    array_push($data, [
                        'salesperson_name' => $query->salesperson_name,
                        'commission' => $query->sp_commission,
                        'volume' => $query->sp_volume,
                        'margin' => $query->sp_margin,
                        'month' => $month.' '.substr($query->summary_year_month, 0, 4),
                    ]);
                }
            }

            return view('tables.salespersons', ['data' => json_encode($data)]);
        }

        public function calcOneCustomer(Request $request, $ext_id)
        {
            $month = $request->get('month');
            $dMonth = date('F', mktime(0, 0, 0, $month, 1));

            $returnValues = [];
            //  dd($ext_id);
            $customerItems = SaleInvoice::select(DB::raw('customer_id,customers.name as customer_name,
                count(invoice_number) as customer_count,
                sum(commission) as customer_commission,
                sum(amt_invoiced) as customer_volume,
                avg(margin)as customer_margin
                '))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->where('invoice_lines.customer_id', '=', $ext_id)
                ->get()->toArray();

            //  dd($customerItems);

            $chartItems = SaleInvoice::select(DB::raw('customers.name as "0",
					sum(commission) as "1",
					avg(margin) as "2",
					count(customer_id) "3"
					'))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->whereRaw(
                    'MONTH(invoice_lines.invoice_date) = ? ', ($month))
                ->orderBy('1', 'desc')
                ->groupBy('customer_id')
                ->get()->toArray();

            $customers = Lava::DataTable();
            $customers->addStringColumn('Brand');
            $customers->addnumberColumn(' Commission $');
            $customers->addnumberColumn(' Avg. Margin %');
            $customers->addnumberColumn(' Sales #');
            if (count($chartItems)) {
                $customers->addRows($chartItems);
            }
            Lava::DonutChart('Customer', $customers, [
                'title' => 'Sales per Customer in '.$dMonth,
                'height' => 900,
                'width' => 900,
                'vAxis' => ['textPosition' => 'none'],
            ]);

            $salesTotal = 0;
            $marginTotal = 0;
            $commissionTotal = 0;

            for ($j = 0; $j < $customerItems->count(); $j++) {
                $salesTotal += ($customerItems->toArray()[$j]['customer_volume']);
                $marginTotal += ($customerItems->toArray()[$j]['customer_margin']);
                $commissionTotal += ($customerItems->toArray()[$j]['customer_commission']);
            }
            $avgMarginTotal = $marginTotal / $customerItems->count();

            $data = [
                'month' => $month,
                'dMonth' => $dMonth,
                'customers' => $customerItems,
                'salesTotal' => $salesTotal,
                'avgMarginTotal' => $avgMarginTotal,
                'commissionTotal' => $commissionTotal, ];

            array_push($returnValues, $data);

            return view('tables.customers', ['customers' => $returnValues[0]['customers'], 'customerData' => $data]);
        }

        public function calcCustomersPerMonth(Request $request)
        {
            $month = $request->get('month');
            $dMonth = date('F', mktime(0, 0, 0, $month, 1));

            $chartItems = SaleInvoice::select(DB::raw('customers.name as "0",
					sum(commission) as "1",
					avg(margin) as "2",
					count(customer_id) "3"
					'))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->whereRaw(
                    'MONTH(invoice_lines.invoice_date) = ? ', ($month))
                ->orderBy('1', 'desc')
                ->groupBy('customer_id')
                ->get()
                ->toArray();

            $customers = Lava::DataTable();
            $customers->addStringColumn('Customer');
            $customers->addnumberColumn(' Commission $');
            $customers->addnumberColumn(' Avg. Margin %');
            $customers->addnumberColumn(' Sales #');
            if (count($chartItems)) {
                $customers->addRows($chartItems);
            }
            Lava::DonutChart('Customer', $customers, [
                'title' => 'Sales per Customer in '.$dMonth,
                'height' => 900,
                'width' => 900,
                'vAxis' => ['textPosition' => 'none'],
            ]);

            $returnValues = [];
            $dMonth = date('F', mktime(0, 0, 0, $month, 1));
            $customerItems = SaleInvoice::select(DB::raw('customer_id,customers.name as customer_name,
                count(distinct(invoice_number)) as customer_count,
                sum(commission) as customer_commission,
                sum(price_subtotal) as customer_volume,
                avg(NULLIF(margin,0))as customer_margin
                '))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '!=', null)
                ->whereRaw('MONTH(invoice_lines.invoice_date) = ?', ($month))
                ->orderBy('customers.name')
                ->groupBy('customer_id')
                ->get()
                ->toArray();

            $customerItems2 = SaleInvoice::select(DB::raw('customer_id,customers.name as customer_name,
                count(distinct(invoice_number)) as customer_count,
                sum(commission) as customer_commission,
                sum(amt_invoiced  + amt_to_invoice) as customer_volume,
                avg(NULLIF(margin,0))as customer_margin
                '))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->whereRaw('MONTH(invoice_lines.invoice_date) = ?', ($month))
                ->orderBy('customers.name')
                ->groupBy('customer_id')
                ->get();
            //   dd($customerItems);

            $salesTotal = 0;
            $marginTotal = 0;
            $commissionTotal = 0;
            $margin_count = 0;
            for ($j = 0; $j < count($customerItems); $j++) {
                if ($customerItems[$j]['customer_volume'] > 0) {
                    $margin_count++;
                    $salesTotal += ($customerItems[$j]['customer_volume']);
                    $marginTotal += ($customerItems[$j]['customer_margin']);
                    $commissionTotal += ($customerItems[$j]['customer_commission']);
                }
            }
            $avgMarginTotal = $marginTotal / $margin_count;

            $data = [
                'month' => $month,
                'dMonth' => $dMonth,
                'customers' => $customerItems,
                'salesTotal' => $salesTotal,
                'avgMarginTotal' => $avgMarginTotal,
                'commissionTotal' => $commissionTotal, ];

            array_push($returnValues, $data);
            //  dd($returnValues);
            //	$table = \Table::create($customerItems2); // Generate a Table based on these "rows"
            return view('tables.customers', ['customers' => $returnValues[0]['customers'], 'customerData' => $data]);
        }

        public function customer_ajax($month = 1)
        {
            $customers = SaleInvoice::select(DB::raw('customer_id,customers.name as customer_name,
                count(distinct(invoice_number)) as customer_count,
                sum(commission) as customer_commission,
                sum(price_subtotal) as customer_volume,
                avg(NULLIF(margin,0))as customer_margin
                '))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '!=', null)
                ->whereRaw('MONTH(invoice_lines.invoice_date) = ?', ($month))
                ->orderBy('customer_volume')
                ->groupBy('customer_id')
                ->get();

            return DataTables::of($customers)
                ->editColumn('customer_commission', function ($customer) {
                    return number_format($customer->customer_commission, 2);
                })
                ->editColumn('customer_margin', function ($customer) {
                    return number_format($customer->customer_margin, 2);
                })
                ->editColumn('customer_volume', function ($customer) {
                    return number_format($customer->customer_volume, 2);
                })
                ->make(true);
        }

        public function calcBrandsPerMonth(Request $request)
        {
            $month = $request->get('month');
            $returnValues = [];
            $dMonth = date('F', mktime(0, 0, 0, $month, 1));

            $chartItems = SaleInvoice::select(DB::raw('brands.name as "0",
					sum(amt_invoiced) as "1",
					avg(margin) as "2",
					count(brand_id) "3"
					'))
                ->leftJoin('brands', 'brands.ext_id', '=', 'invoice_lines.brand_id')
                ->where('brands.is_active', '=', true)
                ->whereRaw(
                    'MONTH(invoice_lines.invoice_date) = ? ', ($month))
                ->orderBy('1', 'desc')
                ->groupBy('brand_id')
                ->get()->toArray();

            $brands = Lava::DataTable();
            $brands->addStringColumn('Brand');
            $brands->addnumberColumn(' Commission $');
            $brands->addnumberColumn(' Avg. Margin %');
            $brands->addnumberColumn(' Sales #');
            if (count($chartItems)) {
                $brands->addRows($chartItems);
            }
            Lava::DonutChart('Brand', $brands, [
                'title' => 'Sales per Brand in '.$dMonth,
                'height' => 600,
                'width' => 900,
                'vAxis' => ['textPosition' => 'none'],
            ]);

            $brandItems = SaleInvoice::select(DB::raw('brands.name as brand_name,
					avg(NULLIF(margin,0)) as brand_margin,
					sum(commission) as brand_commission,
					sum(amt_invoiced) as brand_volume
					'))
                ->leftJoin('brands', 'brands.ext_id', '=', 'invoice_lines.brand_id')
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '!=', null)
                ->where('brands.is_active', '=', true)
                ->whereRaw('MONTH(invoice_lines.invoice_date) = ?', ($month))
                ->orderBy('brand_volume', 'desc')
                ->groupBy('brand_id')
                ->get();

            $salesTotal = 0;
            $marginTotal = 0;
            $commissionTotal = 0;
            $margin_count = 0;
            for ($j = 0; $j < $brandItems->count(); $j++) {
                if ($brandItems->toArray()[$j]['brand_volume'] > 0) {
                    $margin_count++;
                    $salesTotal += ($brandItems->toArray()[$j]['brand_volume']);
                    $marginTotal += ($brandItems->toArray()[$j]['brand_margin']);
                    $commissionTotal += ($brandItems->toArray()[$j]['brand_commission']);
                }
            }
            $avgMarginTotal = $marginTotal / $margin_count;

            $data = [
                'month' => $month,
                'dMonth' => $dMonth,
                'brands' => $brandItems,
                'salesTotal' => $salesTotal,
                'avgMarginTotal' => $avgMarginTotal,
                'commissionTotal' => $commissionTotal, ];

            array_push($returnValues, $data);

            return view('tables.brands_test', ['brands' => $returnValues[0]['brands'], 'brandData' => $data]);
        }

        public function brand_ajax($month)
        {
            //  $month = $_POST["month"];
            $brands = SaleInvoice::select(DB::raw('brands.name as brand_name,
					avg(NULLIF(margin,0)) as brand_margin,
					sum(commission) as brand_commission,
					sum(price_subtotal) as brand_volume
					'))
                ->leftJoin('brands', 'brands.ext_id', '=', 'invoice_lines.brand_id')
                ->where('brands.is_active', '=', true)
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '!=', null)
                //                 ->whereRaw('MONTH(invoice_lines.invoice_date) = ?', (11))
                ->whereRaw('MONTH(invoice_lines.invoice_date) = ?', ($month))
                ->having('brand_volume', '>', 0)
                ->groupBy('brands.name')
                ->get();

            return DataTables::of($brands)
                ->editColumn('brand_commission', function ($brand) {
                    return number_format($brand->brand_commission, 2);
                })
                ->editColumn('brand_margin', function ($brand) {
                    return number_format($brand->brand_margin, 2);
                })
                ->editColumn('brand_volume', function ($brand) {
                    return number_format($brand->brand_volume, 2);
                })
                ->make(true);
        }

        public function calcCommissions(Request $request)
        {
            $commission_version = 2;
            SaleInvoice::where('sales_person_id', '>', 0)->where('invoice_status', '=', 'invoiced')->chunk(100, function ($items) {
                $commission_version = 2;
                foreach ($items as $item) {
                    $commission_percent = $this->getCommission(round($item->margin, 0, PHP_ROUND_HALF_DOWN), $item->salesperson->region, $commission_version);
                    $commission = $item->quantity * $item->unit_price * $commission_percent;

                    //$commission = ($item->amt_invoiced + $item->amt_to_invoice) * $commission_percent;

                    $si = SaleInvoice::find($item->id);
                    $si->commission = $commission;
                    $si->comm_version = $commission_version;
                    $si->comm_region = $item->salesperson->region;
                    $si->comm_percent = $commission_percent;
                    $si->save();
                }
            });
            dd('done with ');
        }

        public function calcRegions(Request $request)
        {
            $region = $request->get('region');
            if ($region == 'N') {
                $data = $this->calcNorth();

                return view('monthly_north', $data);
            } elseif ($region == 'S') {
                $data = $this->calcSouth();

                return view('monthly_south', $data);
            } else {
                $data = $this->CalcAll();
                //	dd($data);
                return view('monthly', $data);
            }

            return 'xxx';
        }

        public function testsalespersons()
        {
            $sp = SaleInvoice::select('invoice_lines.sales_person_id', 'sales_persons.name', 'sales_persons.region')
                ->orderBy('invoice_lines.sales_person_id')
                ->groupBy('invoice_lines.sales_person_id')
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '!=', null)
                ->get();

            return $sp->tojson();
        }

        public function calcAll()
        {
            $title = 'Sales per month';

            $monthItems = SaleInvoice::select(DB::raw('order_id,
        sum(price_subtotal) as month_sale,
        sum(commission) as month_commission,
        avg(NULLIF(margin,0)) as month_margin, 
        count(distinct(order_id)) as so_count,
        EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
        '))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '!=', null)
                ->orderBy('summary_year_month', 'DESC')
                ->groupBy('summary_year_month')
                ->get();

            $totalSales = 0;
            $totalCommission = 0;
            $totalAvgMargin = 0;
            $totalSO = 0;
            $margin_count = 0;
            foreach ($monthItems as $item) {
                if ($item->month_sale > 0) {
                    $margin_count++;
                    $totalSales += $item->month_sale;
                    $totalCommission += $item->month_commission;
                    $totalAvgMargin += $item->month_margin;
                }
                $totalSO += $item->so_count;
            }
            $AvMarginTotal = $totalAvgMargin / $margin_count;

            $AllTotals = ['totalSales' => $totalSales, 'totalCommission' => $totalCommission, 'AvMarginTotal' => $AvMarginTotal, 'totalSO' => $totalSO];

            $monthChartItems = SaleInvoice::select(DB::raw('
            EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as "0", 
            sum(commission) as "1",
            (avg(NULLIF(margin,0)) * 100) as "2",
            count(distinct(invoice_number)) as "3"
        '))
                ->orderBy('created_at', 'asc')
                ->orderBy('1')
                ->groupBy('0')
                ->get()->toArray();

            $months = Lava::DataTable();
            $months->addStringColumn('Month');
            $months->addnumberColumn(' Commission $');
            $months->addnumberColumn(' Avg. Margin %');
            $months->addnumberColumn(' Salesorders');
            if (count($monthChartItems)) {
                $months->addRows($monthChartItems);
            }
            Lava::ComboChart('Months', $months, [
                'title' => 'Sales per Month',
                'height' => 600,
                'width' => 780,
                'bar' => ['groupWidth' => '50%'],
                'seriesType' => 'bars',
                'series' => [1 => ['type' => 'line']],
            ]);

            // northern California
            $monthItemsNorth = SaleInvoice::select(DB::raw('order_id,
        sum(price_subtotal) as month_sale,
        sum(commission) as month_commission,
        avg(NULLIF(margin,0)) as month_margin, 
        count(distinct(order_id)) as so_count,
        EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
        '))
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '=', 'N')
                ->orderBy('summary_year_month', 'DESC')
                ->groupBy('summary_year_month')
                ->get();

            $totalSales = 0;
            $totalCommission = 0;
            $totalAvgMargin = 0;
            $totalSO = 0;
            $margin_count = 0;
            foreach ($monthItemsNorth as $item) {
                if ($item->month_sale > 0) {
                    $margin_count++;
                    $totalSales += $item->month_sale;
                    $totalCommission += $item->month_commission;
                    $totalAvgMargin += $item->month_margin;
                }
                $totalSO += $item->so_count;
            }
            $AvMarginTotal = $totalAvgMargin / $margin_count;

            $NorthernTotals = ['totalSales' => $totalSales, 'totalCommission' => $totalCommission, 'AvMarginTotal' => $AvMarginTotal, 'totalSO' => $totalSO];

            $monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(invoice_lines.invoice_date) as "0", 
            sum(commission) as "1",
            avg(NULLIF(margin,0)) * 100 as "2",
            count(distinct(invoice_number)) as "3"
        '))
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '=', 'N')
                ->orderBy('invoice_lines.created_at', 'asc')
                ->orderBy('1')
                ->groupBy('0')
                ->get()->toArray();

            $months = Lava::DataTable();
            $months->addStringColumn('Month');
            $months->addnumberColumn(' Commission $');
            $months->addnumberColumn(' Avg. Margin %');
            $months->addnumberColumn(' Salesorders');
            if (count($monthChartItems)) {
                $months->addRows($monthChartItems);
            }
            Lava::ComboChart('MonthsNorth', $months, [
                'title' => 'Sales per Month Northern Region',
                'height' => 600,
                'width' => 780,
                'bar' => ['groupWidth' => '50%'],
                'seriesType' => 'bars',
                'series' => [1 => ['type' => 'line']],
            ]);

            // southern California

            $monthItemsSouth = SaleInvoice::select(DB::raw('order_id,
        sum(price_subtotal) as month_sale,
        sum(commission) as month_commission,
        avg(NULLIF(margin,0)) as month_margin, 
        count(distinct(order_id)) as so_count,
        EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
        '))
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '=', 'S')
                ->orderBy('summary_year_month', 'DESC')
                ->groupBy('summary_year_month')
                ->get();

            $totalSales = 0;
            $totalCommission = 0;
            $totalAvgMargin = 0;
            $margin_count = 0;

            foreach ($monthItemsSouth as $item) {
                if ($item->month_sale > 0) {
                    $margin_count++;
                    $totalSales += $item->month_sale;
                    $totalCommission += $item->month_commission;
                    $totalAvgMargin += $item->month_margin;
                }
                $totalSO += $item->so_count;
            }
            $AvMarginTotal = $totalAvgMargin / $margin_count;

            $SouthernTotals = ['totalSales' => $totalSales, 'totalCommission' => $totalCommission, 'AvMarginTotal' => $AvMarginTotal, 'totalSO' => $totalSO];

            //dd($monthItemsSouth->toArray());

            $monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(invoice_lines.invoice_date) as "0", 
            sum(commission) as "1",
            avg(NULLIF(margin,0)) * 100 as "2",
            count(distinct(invoice_number)) as "3"
        '))
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '=', 'S')
                ->orderBy('invoice_lines.created_at', 'asc')
                ->orderBy('1')
                ->groupBy('0')
                ->get()->toArray();

            $months = Lava::DataTable();
            $months->addStringColumn('Month');
            $months->addnumberColumn(' Commission $');
            $months->addnumberColumn(' Avg. Margin %');
            $months->addnumberColumn(' Salesorders');
            if (count($monthChartItems)) {
                $months->addRows($monthChartItems);
            }
            Lava::ComboChart('MonthsSouth', $months, [
                'title' => 'Sales per Month Southern Region',
                'height' => 600,
                'width' => 780,
                'bar' => ['groupWidth' => '50%'],
                'seriesType' => 'bars',
                'series' => [1 => ['type' => 'line']],

            ]);

            return ['monthItems' => $monthItems, 'AllTotals' => $AllTotals,
                'monthItemsNorth' => $monthItemsNorth, 'NorthernTotals' => $NorthernTotals,
                'monthItemsSouth' => $monthItemsSouth, 'SouthernTotals' => $SouthernTotals,
            ];
        }

        public function testAllmonth()
        {
            $region = 'N';
            $all_months = SaleInvoice::select(DB::raw('order_id,
        sum(price_subtotal) as month_sale,
        sum(commission) as month_commission,
        avg(NULLIF(margin,0)) as month_margin, 
        count(distinct(order_id)) as so_count,
        EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
        '))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->when($region == 'N',
                    function ($q) {
                        //return $q->where('sales_persons.region', '!=', null);
                        return $q->where('sales_persons.region', '=', 'N');
                    },
                    function ($q) {
                        return $q->where('sales_persons.region', '=', 'S');
                    }
                )
                ->orderBy('summary_year_month', 'ASC')
                ->groupBy('summary_year_month');

            dd($all_months->get()->toArray());
        }

        public function ajax_all_months()
        {
            $all_months = SaleInvoice::select(DB::raw('order_id,
        sum(price_subtotal) as month_sale,
        sum(commission) as month_commission,
        avg(NULLIF(margin,0)) as month_margin, 
        count(distinct(order_id)) as so_count,
        EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
        '))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '!=', null)
                ->orderBy('summary_year_month', 'ASC')
                ->groupBy('summary_year_month')
                ->get();

            return DataTables::of($all_months)
                ->editColumn('summary_year_month', function ($all_month) {
                    return date('F', mktime(0, 0, 0, substr($all_month->summary_year_month, 4), 1));
                })
                ->editColumn('month_commission', function ($all_month) {
                    return number_format($all_month->month_commission, 2);
                })
                ->editColumn('month_margin', function ($all_month) {
                    return number_format($all_month->month_margin, 2);
                })
                ->editColumn('month_sale', function ($all_month) {
                    return number_format($all_month->month_sale, 2);
                })
                ->editColumn('so_count', function ($all_month) {
                    return number_format($all_month->so_count);
                })
                ->make(true);
        }

        public function ajax_region_months($region)
        {
            $all_months = SaleInvoice::select(DB::raw('order_id,
        sum(price_subtotal) as month_sale,
        sum(commission) as month_commission,
        avg(NULLIF(margin,0)) as month_margin, 
        count(distinct(order_id)) as so_count,
        EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
        '))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->when($region == 'N',
                    function ($q) {
                        //return $q->where('sales_persons.region', '!=', null);
                        return $q->where('sales_persons.region', '=', 'N');
                    },
                    function ($q) {
                        return $q->where('sales_persons.region', '=', 'S');
                    }
                )
                ->orderBy('summary_year_month', 'ASC')
                ->groupBy('summary_year_month')
                ->get();

            return DataTables::of($all_months)
                ->editColumn('summary_year_month', function ($all_month) {
                    return date('F', mktime(0, 0, 0, substr($all_month->summary_year_month, 4), 1));
                })
                ->editColumn('month_commission', function ($all_month) {
                    return number_format($all_month->month_commission, 2);
                })
                ->editColumn('month_margin', function ($all_month) {
                    return number_format($all_month->month_margin, 2);
                })
                ->editColumn('month_sale', function ($all_month) {
                    return number_format($all_month->month_sale, 2);
                })
                ->editColumn('so_count', function ($all_month) {
                    return number_format($all_month->so_count);
                })
                ->make(true);
        }

        public function ajax_all_north()
        {
            $all_months = SaleInvoice::select(DB::raw('order_id,
        sum(price_subtotal) as month_sale,
        sum(commission) as month_commission,
        avg(NULLIF(margin,0)) as month_margin, 
        count(distinct(order_id)) as so_count,
        EXTRACT(YEAR_MONTH FROM invoice_lines.invoice_date) as summary_year_month 
        '))
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '=', 'N')
                ->orderBy('summary_year_month', 'DESC')
                ->groupBy('summary_year_month')
                ->get();

            return DataTables::of($all_months)
                ->editColumn('summary_year_month', function ($all_month) {
                    return date('F', mktime(0, 0, 0, substr($all_month->summary_year_month, 4), 1));
                })
                ->editColumn('month_commission', function ($all_month) {
                    return number_format($all_month->month_commission, 2);
                })
                ->editColumn('month_margin', function ($all_month) {
                    return number_format($all_month->month_margin, 2);
                })
                ->editColumn('month_sale', function ($all_month) {
                    return number_format($all_month->month_sale, 2);
                })
                ->editColumn('so_count', function ($all_month) {
                    return number_format($all_month->so_count);
                })
                ->make(true);
        }

        public function calcNorth()
        {
            // northern California
            $monthItemsNorth = SaleInvoice::select(DB::raw('order_id,
        sum(price_subtotal) as month_sale,
        sum(commission) as month_commission,
        avg(NULLIF(margin,0)) as month_margin, 
        count(distinct(order_id)) as so_count,
        MONTH(invoice_lines.invoice_date) as month, 
        YEAR(invoice_lines.invoice_date) as year'))
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '=', 'N')
                ->orderBy('invoice_lines.created_at', 'desc')
                ->groupBy('month')
                ->get();

            $totalSales = 0;
            $totalCommission = 0;
            $totalAvgMargin = 0;
            $totalSO = 0;
            $margin_count = 0;

            foreach ($monthItemsNorth as $item) {
                if ($item->month_sale > 0) {
                    $margin_count++;
                    $totalSales += $item->month_sale;
                    $totalCommission += $item->month_commission;
                    $totalAvgMargin += $item->month_margin;
                }
                $totalSO += $item->so_count;
            }
            $AvMarginTotal = $totalAvgMargin / $margin_count;

            $NorthernTotals = ['totalSales' => $totalSales, 'totalCommission' => $totalCommission, 'AvMarginTotal' => $AvMarginTotal, 'totalSO' => $totalSO];

            $monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(invoice_lines.invoice_date) as "0", 
            sum(commission) as "1",
            avg(NULLIF(margin,0)) * 100 as "2",
            count(distinct(invoice_number)) as "3"
        '))
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '=', 'N')
                ->orderBy('invoice_lines.created_at', 'asc')
                ->orderBy('1')
                ->groupBy('0')
                ->get()->toArray();

            $months = Lava::DataTable();
            $months->addStringColumn('Month');
            $months->addnumberColumn(' Commission $');
            $months->addnumberColumn(' Avg. Margin %');
            $months->addnumberColumn(' Salesorders');
            if (count($monthChartItems)) {
                $months->addRows($monthChartItems);
            }
            Lava::ComboChart('MonthsNorth', $months, [
                'title' => 'Sales per Month Northern Region',
                'height' => 600,
                'width' => 780,
                'bar' => ['groupWidth' => '50%'],
                'seriesType' => 'bars',
                'series' => [1 => ['type' => 'line']],
            ]);

            return ['monthItemsNorth' => $monthItemsNorth, 'NorthernTotals' => $NorthernTotals];
        }

        public function calcSouth()
        {// southern California
            $monthItemsSouth = SaleInvoice::select(DB::raw('order_id,
        sum(price_subtotal) as month_sale,
        sum(commission) as month_commission,
        avg(NULLIF(margin,0)) as month_margin, 
        count(distinct(order_id)) as so_count,
        MONTH(invoice_lines.invoice_date) as month, 
        YEAR(invoice_lines.invoice_date) as year'))
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '=', 'S')
                ->orderBy('invoice_lines.created_at', 'desc')
                ->groupBy('month')
                ->get();

            //dd($monthItemsSouth->toArray());

            $monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(invoice_lines.invoice_date) as "0", 
            sum(commission) as "1",
            avg(NULLIF(margin,0)) * 100 as "2",
            count(distinct(invoice_number)) as "3"
        '))
                ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'invoice_lines.sales_person_id')
                ->where('sales_persons.region', '=', 'S')
                ->orderBy('invoice_lines.created_at', 'asc')
                ->orderBy('1')
                ->groupBy('0')
                ->get()->toArray();

            $totalSales = 0;
            $totalCommission = 0;
            $totalAvgMargin = 0;
            $totalSO = 0;
            $margin_count = 0;

            foreach ($monthItemsSouth as $item) {
                if ($item->month_sale > 0) {
                    $margin_count++;
                    $totalSales += $item->month_sale;
                    $totalCommission += $item->month_commission;
                    $totalAvgMargin += $item->month_margin;
                }
                $totalSO += $item->so_count;
            }
            $AvMarginTotal = $totalAvgMargin / $margin_count;

            $SouthernTotals = ['totalSales' => $totalSales, 'totalCommission' => $totalCommission, 'AvMarginTotal' => $AvMarginTotal, 'totalSO' => $totalSO];

            $months = Lava::DataTable();
            $months->addStringColumn('Month');
            $months->addnumberColumn(' Commission $');
            $months->addnumberColumn(' Avg. Margin %');
            $months->addnumberColumn(' Salesorders');
            if (count($monthChartItems)) {
                $months->addRows($monthChartItems);
            }
            Lava::ComboChart('MonthsSouth', $months, [
                'title' => 'Sales per Month Southern Region',
                'height' => 600,
                'width' => 780,
                'bar' => ['groupWidth' => '50%'],
                'seriesType' => 'bars',
                'series' => [1 => ['type' => 'line']],
            ]);

            return ['monthItemsSouth' => $monthItemsSouth, 'SouthernTotals' => $SouthernTotals];
        }

        public function allCommissions()
        {
            $items = SaleInvoice::select(DB::raw('sales_person_id,
        sum(amt_invoiced) as month_sale,
        sum(commission) as month_commission,
        MONTH(created_at) as month, 
        YEAR(created_at) as year'))
                ->has('salesperson')
                ->where('sales_person_id', '>', 0)
                ->orderBy('created_at')
                ->orderBy('month_commission')
                ->groupBy('month')
                ->groupBy('sales_person_id')
                ->get();
            //	return $items;
            foreach ($items as $item) {
                Earning2::updateOrCreate(
                    ['sales_person_id' => $item->sales_person_id,
                        'month' => $item->month,
                        'year' => $item->year, ],
                    ['name' => $item->salesperson->name,
                        'commission' => $item->month_commission,
                        'sale' => $item->month_sale, ]
                );
            }

            dd('done');
        }

        public function commissionsPerAccount()
        {
            $items = SaleInvoice::select(DB::raw('*,sum(commission) as month_commission,MONTH(created_at) as month, YEAR(created_at) as year'))
                ->where('sales_person_id', '>', 0)
                ->orderBy('created_at')
                ->groupBy('month')
                ->groupBy('sales_person_id')
                ->get();

            foreach ($items as $item) {
                echo $item->sales_person_id.' - '.$item->month.' - '.$item->month_commission.'<br>';
                Earning2::updateOrCreate(
                    ['sales_person_id' => $item->sales_person_id,
                        'commission' => $item->month_commission,
                        'month' => $item->month,
                        'year' => $item->year, ]
                );
            }
            dd('done');
        }
    }
