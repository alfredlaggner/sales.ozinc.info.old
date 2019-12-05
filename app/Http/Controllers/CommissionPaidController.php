<?php

namespace App\Http\Controllers;

use App\CommissionsPaid;
use App\Customer;
use App\InvoiceLine;
use App\Month;
use App\Salesline;
use App\SalesPerson;
use App\SavedCommission;
use Carbon\Carbon;
use Auth;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CommissionPaidController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function admin(Request $request)
    {
        $commissions = SavedCommission::all();
        foreach ($commissions as $commission)
            if (!Schema::hasTable($commission->name)) {
                $savedCommission = SavedCommission::find($commission->id);
                $savedCommission->delete();
            }
        $now = Carbon::now();
        $months = Month::where('month_id', '<=', $now->month)
            ->orderBy('month_id', 'desc')
            ->get();
        $commissions = SavedCommission::orderBy('updated_at', 'desc')->get();
        //dd($commissions);
        $data = ['id' => 0, 'month' => $now->month];
        return view('admin', ['jcommissions' => json_encode($commissions), 'commissions' => $commissions, 'data' => $data, 'data' => $data, 'months' => $months, 'year' => $now->year]);
    }


    public function createSavedCommission(Request $request)
    {
        $paidCommissionDateFrom = env('PAID_INVOICES_START_DATE', '2019-06-01');

        $timeFrame = ['year' => 2019, 'months' => [Carbon::now()->month]];
        $timeFrame = ['year' => $request->get('year'), 'months' => [$request->get('months')]];
        $lastMonth = end($timeFrame['months']);
        $dateTo = substr(new Carbon($timeFrame['year'] . '-' . $lastMonth . '-01'), 0, 10);
        $lastDay = date('t', strtotime($dateTo));
        $dateTo = substr(new Carbon($timeFrame['year'] . '-' . $lastMonth . '-' . $lastDay), 0, 10);

        $queries = SalesLine::select(DB::raw('*,EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month'))
            ->whereBetween('saleslines.invoice_date', [$paidCommissionDateFrom, $dateTo])
            //  ->where('amount', '>=', 0)
            ->whereHas('invoice_paid', function ($query) {
                $query->where('is_paid', false);
            })
            ->orderBy('summary_year_month')
            ->orderBy('customer_name', 'asc')
            ->orderBy('order_number', 'desc')
            ->get();

//dd('queries');

        /*        foreach ($queries as $query) {
                    echo $query->invoice_paid->ext_id . "<br>";
                }*/
        // dd();
        //   dd($queries->count());
        $is_add = true;

        if ($is_add == true) {
            $now = Carbon::now();
            $currentTime = $now->format('_Y_m_d_h_i_s');
            $newtable = "invoices_paid" . $currentTime;
            //			echo $newtable;
            Schema::create($newtable, function (Blueprint $table) {
                $table->increments('id')->unique();
                $table->integer('ext_id')->nullable();
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

            $data = [];
            $sc = new SavedCommission;
            $sc->description = "add description";
            $sc->name = $newtable;
            $sc->date_created = $now;
            $sc->month = $request->get('months');
            $sc->year = $request->get('year');
            $sc->created_by = Auth::user()->name;
            $sc->save();
        }

        foreach ($queries as $query) {
            $month = date("F", mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
            $line = [
                'month' => $month . ' ' . substr($query->summary_year_month, 0, 4),
                'ext_id' => $query->ext_id,
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
        session(['commissions_paid_table_name' => $newtable]);

        return redirect()->route('admin');
    }

    public function paySavedCommission(Request $request, $saved_commissions_id, $table_name)
    {
        SavedCommission::find($saved_commissions_id)
            ->update(['is_commissions_paid' => true]);

        $newtable = $table_name;
        $payed_commissions = DB::table($newtable)->get();
        foreach ($payed_commissions as $payed_commission) {

            CommissionsPaid::where('ext_id', $payed_commission->ext_id)
                ->where('is_paid', 0)
                ->update([
                    'saved_commissions_id' => $saved_commissions_id,
                    'is_paid' => 1,
                    'paid_at' => now(),
                    'paid_by' => Auth::user()->name,
                ]);

        }
        return redirect()->route('admin');
    }

    public
    function editSavedCommission(Request $request, $id)
    {
        $saved_commission = SavedCommission::find($id);


        return (view('accounting.edit_saved_commission', compact('saved_commission')));
    }

    public function saveSavedCommission(Request $request)
    {
        $saved_commission = SavedCommission::find($request->get('id'));
        $saved_commission->description = $request->get('description');
        $saved_commission->comment = $request->get('comment');
        $saved_commission->save();

        return redirect()->route('admin');
    }

    public function viewSavedCommission(Request $request, $id)
    {
        //	$name = $request->get('saved_name');
        $data = [];
        $line = [];
        $savedCommission = SavedCommission::find($id);

        if (!Schema::hasTable($savedCommission->name)) {
            return (view('nodata'));
        } else {
            $queries = DB::table($savedCommission->name)
           //     ->orderBy('customer_name', 'asc')
                ->orderBy('order_number', 'desc')
                ->get();

            foreach ($queries as $query) {
                $date_created = strtotime($query->invoice_date);
                $month = date('F', $date_created);
                $line = [
                    'month' => $month,
                    'order_date' => $query->order_date,
                    'invoice_date' => $query->invoice_date,
                    'order_number' => $query->order_number,
                    'customer_name' => substr($query->customer_name, 0, 20),
                    'rep' => substr($query->rep, 0, 20),
                    'name' => $query->name,
                    'sku' => $query->sku,
                    'brand_name' => $query->brand_name,
                    'category' => $query->category,
                    'quantity' => $query->quantity,
                    'commission_percent' => $query->commission_percent,
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

            }

            $queries = DB::table($savedCommission->name)->select(DB::raw('*,
                sum(commission) as sp_commission,
                sum(amount) as sp_volume,
                avg(NULLIF(margin,0))as sp_margin,
                EXTRACT(YEAR_MONTH FROM ' . $savedCommission->name . '.invoice_date) as summary_year_month 
                '))
                //			->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', $savedCommission->name . '.sales_person_id')
                //			->where('sales_persons.region', '!=', null)
                ->orderBy('rep')
                ->groupBy('rep')
                ->get();

            //		dd($queries->toarray());
            $data2 = [];
            foreach ($queries as $query) {
                if ($query->sp_volume) {
                    $month = date('F', $date_created);
                    array_push($data2, [
                        'rep' => $query->rep,
                        'commission' => $query->sp_commission,
                        'volume' => $query->sp_volume,
                        'margin' => $query->sp_margin,
                        'month' => $month . ' ' . substr($query->summary_year_month, 0, 4),
                    ]);
                }
            }
//dd($data2);


            return (view('tables.total_salesorders', ['data' => json_encode($data), 'header' => $savedCommission, 'overview' => json_encode($data2)]));
        }
    }

    public function view_paid_unpaid_commissions()
    {
        return view('view_paid_unpaid', [
            'today' => Carbon::now()->today()->format("Y-m-d"),
            'salesperson' => Salesperson::all(),
        ]);
    }

    public function list_paid_unpaid_commissions(Request $request)
    {
        $paidCommissionDateFrom = env('PAID_INVOICES_START_DATE', '2019-06-01');
        $dateTo = $request->get('today');
        $rep_id = $request->get('salesperson_id');
        $agent_name = SalesPerson::where('sales_person_id', $rep_id)->first();
//dd($agent_name->name);

        $paid_subtotals_so = $this->paid_subtotals_so($rep_id, $paidCommissionDateFrom, $dateTo);
        $paid_subtotals_month = $this->paid_subtotals_month($rep_id, $paidCommissionDateFrom, $dateTo);
        $unpaid_subtotals_so = $this->unpaid_subtotals_so($rep_id, $paidCommissionDateFrom, $dateTo);
        $unpaid_subtotals_month = $this->unpaid_subtotals_month($rep_id, $paidCommissionDateFrom, $dateTo);
        //  dd( $unpaid_subtotals_month);
        $data = [];
        $commissions_paids = Salesline::select(DB::raw('*,
                EXTRACT(YEAR_MONTH FROM invoice_date) as summary_year_month 
                '))
            ->orderBy('summary_year_month', 'desc')
            ->orderBy('order_number')
            ->where('sales_person_id', '=', $rep_id)
            ->where('state', 'like', 'paid')
            ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
            ->where('commission_paid_at', '!=', NULL)
            ->get();

        $paids = [];
        foreach ($commissions_paids as $query) {
            $month = date("F", mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));

            array_push($paids, [
                'month' => $month . ' ' . substr($query->summary_year_month, 0, 4),
                'invoice_date' => $query->invoice_date,
                'order_number' => $query->order_number,
                'name' => $query->name,
                'quantity' => $query->quantity,
                'commission' => $query->commission,
                'unit_price' => $query->unit_price,
                'amount' => $query->amount,
            ]);
        }
        //  dd($commissions_paids);

        $commissions_unpaids = Salesline::select(DB::raw('*,
                EXTRACT(YEAR_MONTH FROM invoice_date) as summary_year_month 
                '))
            ->orderBy('summary_year_month', 'desc')
            ->orderBy('order_number', 'desc')
            ->where('sales_person_id', '=', $rep_id)
            ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
            ->where('state', 'like', 'paid')
            ->where('invoice_paid_at', '!=', NULL)
            ->where('commission_paid_at', '=', NULL)
            ->get();

//dd($commissions_unpaids->count());

        $unpaids = [];
        foreach ($commissions_unpaids as $query) {
            $month = date("F", mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
            array_push($unpaids, [
                'month' => $month . ' ' . substr($query->summary_year_month, 0, 4),
                'invoice_date' => $query->invoice_date,
                'order_number' => $query->order_number,
                'name' => $query->name,
                'quantity' => $query->quantity,
                'commission' => $query->commission,
                'unit_price' => $query->unit_price,
                'amount' => $query->amount,
            ]);
        }
        return (view('paid_unpaid', ['name' => $agent_name->name, 'paid' => json_encode($paids), 'unpaid' => json_encode($unpaids)]));


        //  return view('paid_unpaid', compact('commissions_paids', 'commissions_unpaids'));
    }

    public function list_paid_unpaid_commissions_work(Request $request)
    {
        $paidCommissionDateFrom = env('PAID_INVOICES_START_DATE', '2019-06-01');
        $dateTo = $request->get('today');
        $rep_id = $request->get('salesperson_id');
        //just for testing
        $dateTo = '2019-09-29';
        $rep_id = 35; //Matt Gutierrez
        $agent_name = SalesPerson::where('sales_person_id', $rep_id)->first();
//dd($agent_name->name);
        $saved_paid_commissions = $this->viewSavedPaidCommissions($rep_id, $paidCommissionDateFrom, $dateTo);
        $paid_subtotals_so = $this->paid_subtotals_so($rep_id, $paidCommissionDateFrom, $dateTo);
        $paid_subtotals_month = $this->paid_subtotals_month($rep_id, $paidCommissionDateFrom, $dateTo);
        $unpaid_subtotals_so = $this->unpaid_subtotals_so($rep_id, $paidCommissionDateFrom, $dateTo);
        $unpaid_subtotals_month = $this->unpaid_subtotals_month($rep_id, $paidCommissionDateFrom, $dateTo);
        //  dd( $unpaid_subtotals_month);
        $data = [];
        $commissions_paids = Salesline::select(DB::raw('*,
                EXTRACT(YEAR_MONTH FROM invoice_date) as summary_year_month 
                '))
            ->orderBy('summary_year_month', 'desc')
            ->orderBy('order_number')
            ->where('sales_person_id', '=', $rep_id)
            ->where('state', 'like', 'paid')
            ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
            ->where('commission_paid_at', '!=', NULL)
            ->get();

        $paids = [];
        foreach ($commissions_paids as $query) {
            $month = date("F", mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));

            array_push($paids, [
                'month' => $month . ' ' . substr($query->summary_year_month, 0, 4),
                'invoice_date' => $query->invoice_date,
                'order_number' => $query->order_number,
                'name' => $query->name,
                'quantity' => $query->quantity,
                'commission' => $query->commission,
                'unit_price' => $query->unit_price,
                'amount' => $query->amount,
            ]);
        }
      //  dd($commissions_paids);

        $commissions_unpaids = Salesline::select(DB::raw('*,
                EXTRACT(YEAR_MONTH FROM invoice_date) as summary_year_month 
                '))
            ->orderBy('summary_year_month', 'desc')
            ->orderBy('order_number', 'desc')
            ->where('sales_person_id', '=', $rep_id)
            ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
            ->where('state', 'like', 'paid')
            ->where('invoice_paid_at', '!=', NULL)
            ->where('commission_paid_at', '=', NULL)
            ->get();

//dd($commissions_unpaids->count());

        $unpaids = [];
        foreach ($commissions_unpaids as $query) {
            $month = date("F", mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
            array_push($unpaids, [
                'month' => $month . ' ' . substr($query->summary_year_month, 0, 4),
                'invoice_date' => $query->invoice_date,
                'order_number' => $query->order_number,
                'name' => $query->name,
                'quantity' => $query->quantity,
                'commission' => $query->commission,
                'unit_price' => $query->unit_price,
                'amount' => $query->amount,
            ]);
        }
        return (view('paid_unpaid', ['name' => $agent_name->name, 'paid' => json_encode($paids), 'unpaid' => json_encode($unpaids)]));


        //  return view('paid_unpaid', compact('commissions_paids', 'commissions_unpaids'));
    }

    public function paid_subtotals_so($rep_id, $paidCommissionDateFrom, $dateTo)
    {
        $queries = SalesLine::select(DB::raw('*,
                        sum(commission) as sum_commission,
                        sum(amount) as sum_volume,
                        avg(NULLIF(margin,0))as avg_margin,
                        EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month 
                        '))
            ->where('state', 'like', 'paid')
            ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
            ->where('sales_person_id', '=', $rep_id)
            ->where('commission_paid_at', '!=', NULL)
            ->groupBy('order_number')
            ->get();
//dd($queries)->toArray();
        $paid_commissions_by_so = [];
        foreach ($queries as $query) {
            array_push($paid_commissions_by_so, [
                'customer_name' => $query->customer_name,
                'order_number' => $query->order_number,
                'commission_per_so' => $query->sum_commission,
                'volume_per_so' => $query->sum_volume,
                'margin_per_so' => $query->avg_margin,
            ]);
        }

    //      dd($paid_commissions_by_so);
        return ($paid_commissions_by_so);
    }

    public function paid_subtotals_month($rep_id, $paidCommissionDateFrom, $dateTo)
    {
        $queries = SalesLine::select(DB::raw('*,
                        sum(commission) as sum_commission,
                        sum(amount) as sum_volume,
                        avg(NULLIF(margin,0))as avg_margin,
                        EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month 
                        '))
            ->where('state', 'like', 'paid')
            ->groupBy('summary_year_month')
            ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
            ->where('sales_person_id', '=', $rep_id)
            ->where('commission_paid_at', '!=', NULL)
            ->get();
        $paid_commissions_by_month = [];
        foreach ($queries as $query) {
            array_push($paid_commissions_by_month, [
                'month' => $query->summary_year_month,
                'commission_per_month' => $query->sum_commission,
                'volume_per_month' => $query->sum_volume,
                'margin_per_month' => $query->avg_margin,
            ]);
        }
        //     dd($paid_commissions_by_month);
        return ($paid_commissions_by_month);
    }

    public function unpaid_subtotals_so($rep_id, $paidCommissionDateFrom, $dateTo)
    {
        $queries = SalesLine::select(DB::raw('*,
                        sum(commission) as sum_commission,
                        sum(amount) as sum_volume,
                        avg(NULLIF(margin,0))as avg_margin,
                        EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month 
                        '))
            ->where('state', 'like', 'paid')
            ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
            ->where('sales_person_id', '=', $rep_id)
            ->where('invoice_paid_at', '!=', NULL)
            ->where('commission_paid_at', '=', NULL)
            ->groupBy('order_number')
            ->get();

        $unpaid_commissions_by_so = [];
        foreach ($queries as $query) {
            array_push($unpaid_commissions_by_so, [
                'order_number' => $query->order_number,
                'commission_per_so' => $query->sum_commission,
                'volume_per_so' => $query->sum_volume,
                'margin_per_so' => $query->avg_margin,
            ]);
        }

        //    dd($unpaid_commissions_by_so);
        return ($unpaid_commissions_by_so);
    }

    public function unpaid_subtotals_month($rep_id, $paidCommissionDateFrom, $dateTo)
    {
        $queries = SalesLine::select(DB::raw('*,
                        sum(commission) as sum_commission,
                        sum(amount) as sum_volume,
                        avg(NULLIF(margin,0))as avg_margin,
                        EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month 
                        '))
            ->where('state', 'like', 'paid')
            ->groupBy('summary_year_month')
            ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
            ->where('sales_person_id', '=', $rep_id)
            ->where('invoice_paid_at', '!=', NULL)
            ->where('commission_paid_at', '=', NULL)
            ->get();
        $unpaid_commissions_by_month = [];
        foreach ($queries as $query) {
            array_push($unpaid_commissions_by_month, [
                'month' => $query->summary_year_month,
                'commission_per_month' => $query->sum_commission,
                'volume_per_month' => $query->sum_volume,
                'margin_per_month' => $query->avg_margin,
            ]);
        }
        //     dd($unpaid_commissions_by_month);
        return ($unpaid_commissions_by_month);
    }

    public function calc_used_products()
    {

        $queries = InvoiceLine::select(DB::raw('*,
                count(product_id) as count_product,
                EXTRACT(YEAR_MONTH FROM invoice_date) as summary_year_month 
                '))
            ->groupBy('product_id')
            ->orderBy('count_product', 'desc')
            ->get();
//dd($queries->toArray());
        foreach ($queries as $query) {

            echo $query->product_id . "      -> " . $query->name . "      : " . $query->count_product . "<br>";
        }
        /*        $products = Margin::all()
                ->rightJoin('')*/

    }



}

