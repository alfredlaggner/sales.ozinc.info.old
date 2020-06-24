<?php

namespace App\Http\Controllers;

use App\Salesline;
use App\SalesPerson;
use App\SavedCommission;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewCommissionController extends Controller
{
    public function list_paid_unpaid_commissions(Request $request)
    {
        $paidCommissionDateFrom = env('PAID_INVOICES_START_DATE', '2019-06-01');
        $dateTo = $request->get('today');
        $rep_id = $request->get('salesperson_id');
        //     dd($rep_id);
        //just for testing
        // $dateTo = '2019-10-29';
        //    $rep_id = 35; //Matt Gutierrez
        $agent = SalesPerson::where('sales_person_id', $rep_id)->first();
        //dd($agent->name);
        $months = $this->listSavedPaidCommissions($agent->name);
        $paid_subtotals_so = $this->paid_subtotals_so($rep_id, $paidCommissionDateFrom, $dateTo);
        $paid_subtotals_month = $this->paid_subtotals_month($rep_id, $paidCommissionDateFrom, $dateTo);
        //       dd($paid_subtotals_month);
        $unpaid_subtotals_so = $this->unpaid_subtotals_so($rep_id, $paidCommissionDateFrom, $dateTo);
        $unpaid_subtotals_month = $this->unpaid_subtotals_month($rep_id, $paidCommissionDateFrom, $dateTo);
        //  dd( $unpaid_subtotals_month);
        $data = [];
        $commissions_paids = Salesline::select(DB::raw('*,
                EXTRACT(YEAR_MONTH FROM invoice_paid_at) as summary_year_month 
                '))
            ->orderBy('summary_year_month', 'desc')
            ->orderBy('order_number')
            ->where('sales_person_id', '=', $rep_id)
            ->where('state', 'like', 'paid')
            ->whereBetween('invoice_paid_at', [$paidCommissionDateFrom, $dateTo])
            ->where('commission_paid_at', '!=', null)
            ->get();

        $paids = [];
        foreach ($commissions_paids as $query) {
            $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));

            array_push($paids, [
                'month' => $month.' '.substr($query->summary_year_month, 0, 4),
                'invoice_paid_at' => $query->invoice_paid_at,
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
            ->where('state', '!=', 'paid')
            //         ->where('invoice_paid_at', '!=', NULL)
            //        ->where('commission_paid_at', '=', NULL)
            ->get();
        //      dd($commissions_unpaids->count());

        $unpaids = [];
        foreach ($commissions_unpaids as $query) {
            $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
            array_push($unpaids, [
                'month' => $month.' '.substr($query->summary_year_month, 0, 4),
                'invoice_paid_at' => $query->invoice_paid_at,
                'order_number' => $query->order_number,
                'name' => $query->name,
                'quantity' => $query->quantity,
                'margin' => $query->margin,
                'commission' => $query->commission,
                'unit_price' => $query->unit_price,
                'amount' => $query->amount,
            ]);
        }
        //dd($months);
        return view('commissions.paid_unpaid_accordion', [
            'name' => $agent->name,
            'paids' => $commissions_paids,
            'paid_subtotals_so' => $paid_subtotals_so,
            'paid_subtotals_month' => $paid_subtotals_month,
            'unpaids' => $commissions_unpaids,
            'unpaid_subtotals_so' => $unpaid_subtotals_so,
            'unpaid_subtotals_month' => $unpaid_subtotals_month,
            'months' => $months,
        ]);

        //  return view('paid_unpaid', compact('commissions_paids', 'commissions_unpaids'));
    }

    public function paid_subtotals_so($rep_id, $paidCommissionDateFrom, $dateTo)
    {
        $queries = SalesLine::select(DB::raw('*,
                        sum(commission) as sum_commission,
                        sum(amount) as sum_volume,
                        avg(NULLIF(margin,0))as avg_margin,
                        EXTRACT(YEAR_MONTH FROM saleslines.invoice_paid_at) as summary_year_month 
                        '))
            ->where('state', 'like', 'paid')
            ->whereBetween('invoice_paid_at', [$paidCommissionDateFrom, $dateTo])
            ->where('sales_person_id', '=', $rep_id)
            ->where('commission_paid_at', '!=', null)
            ->groupBy('order_number')
            ->orderBy('commission_paid_at', 'desc')
            ->get();

        $paid_commissions_by_so = [];
        foreach ($queries as $query) {
            array_push($paid_commissions_by_so, [
                'order_number' => $query->order_number,
                'commission_per_so' => $query->sum_commission,
                'volume_per_so' => $query->sum_volume,
                'margin_per_so' => $query->avg_margin,
                'invoice_date_so' => date('m-d-Y', strtotime(substr($query->invoice_date, 0, 10))),
                'invoice_paid_at_so' => date('m-d-Y', strtotime(substr($query->invoice_paid_at, 0, 10))),
                'commission_paid_at_so' => date('m-d-Y', strtotime(substr($query->commission_paid_at, 0, 10))),
            ]);
        }

        //    dd($paid_commissions_by_so);
        return $paid_commissions_by_so;
    }

    public function paid_subtotals_month($rep_id, $paidCommissionDateFrom, $dateTo)
    {
        $queries = SalesLine::select(DB::raw('*,
                        sum(commission) as sum_commission,
                        sum(amount) as sum_volume,
                        avg(NULLIF(margin,0))as avg_margin,
                        EXTRACT(YEAR_MONTH FROM saleslines.invoice_paid_at) as summary_year_month 
                        '))
            ->where('state', 'like', 'paid')
            ->groupBy('summary_year_month')
            ->whereBetween('invoice_paid_at', [$paidCommissionDateFrom, $dateTo])
            ->where('sales_person_id', '=', $rep_id)
            ->where('commission_paid_at', '!=', null)
            ->orderBy('summary_year_month', 'desc')
            ->get();

        $paid_commissions_by_month = [];
        foreach ($queries as $query) {
            $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
            array_push($paid_commissions_by_month, [
                'month' => $month,
                'commission_per_month' => $query->sum_commission,
                'volume_per_month' => $query->sum_volume,
                'margin_per_month' => $query->avg_margin,
                'invoice_date_so' => date('m-d-Y', strtotime(substr($query->invoice_date, 0, 10))),
                'invoice_paid_at_so' => date('m-d-Y', strtotime(substr($query->invoice_paid_at, 0, 10))),
                'commission_paid_at_so' => date('m-d-Y', strtotime(substr($query->commission_paid_at, 0, 10))),
            ]);
        }
        //     dd($paid_commissions_by_month);
        return $paid_commissions_by_month;
    }

    public function unpaid_subtotals_so($rep_id, $paidCommissionDateFrom, $dateTo)
    {
        //  dd($rep_id);
        $queries = SalesLine::select(DB::raw('*,
                        sum(commission) as sum_commission,
                        sum(amount) as sum_volume,
                        avg(NULLIF(margin,0))as avg_margin,
                        EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month,
                        EXTRACT(MONTH FROM saleslines.invoice_date) as so_month 
                       '))
            ->where('state', '!=', 'paid')
            ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
            ->where('sales_person_id', '=', $rep_id)
            //  ->where('invoice_paid_at', '!=', NULL)
            //  ->where('commission_paid_at', '=', NULL)
            ->groupBy('order_number')
            ->orderBy('summary_year_month', 'desc')
            ->get();
        //dd($queries);
        $unpaid_commissions_by_so = [];
        foreach ($queries as $query) {
            $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
            array_push($unpaid_commissions_by_so, [
                'months' => $queries,
                'month_number' => intval(substr($query->summary_year_month, 4, 2)),
                'order_number' => $query->order_number,
                'commission_per_so' => $query->sum_commission,
                'volume_per_so' => $query->sum_volume,
                'margin_per_so' => $query->avg_margin,
                'invoice_date_so' => date('m-d-Y', strtotime(substr($query->invoice_date, 0, 10))),
                'invoice_paid_at_so' => date('m-d-Y', strtotime(substr($query->invoice_paid_at, 0, 10))),
                'commission_paid_at_so' => '',
            ]);
        }

        //          dd($unpaid_commissions_by_so);
        return $unpaid_commissions_by_so;
    }

    public function unpaid_subtotals_month($rep_id, $paidCommissionDateFrom, $dateTo)
    {
        $queries = SalesLine::select(DB::raw('*,
                        sum(commission) as sum_commission,
                        sum(amount) as sum_volume,
                        avg(NULLIF(margin,0))as avg_margin,
                        EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month 
                        '))
            ->where('state', '!=', 'paid')
            ->groupBy('summary_year_month')
            ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
            ->where('sales_person_id', '=', $rep_id)
            //      ->where('invoice_paid_at', '!=', NULL)
            //      ->where('commission_paid_at', '=', NULL)
            ->orderBy('summary_year_month', 'desc')
            ->get();
        //dd($queries);
        $unpaid_commissions_by_month = [];
        $total_uncollected = 0.00;
        foreach ($queries as $query) {
            $total_uncollected += $query->sum_volume;
            $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
            array_push($unpaid_commissions_by_month, [
                'month' => $month,
                'month_number' => intval(substr($query->summary_year_month, 4, 2)),
                'commission_per_month' => $query->sum_commission,
                'volume_per_month' => $query->sum_volume,
                'margin_per_month' => $query->avg_margin,
                'invoice_date_so' => date('m-d-Y', strtotime(substr($query->invoice_date, 0, 10))),
                'invoice_paid_at_so' => date('m-d-Y', strtotime(substr($query->invoice_paid_at, 0, 10))),
                'commission_paid_at_so' => '',
            ]);
        }
        $returnArray = [];
        $month_total = ['total_uncollected' => $total_uncollected];
        array_push($returnArray, $unpaid_commissions_by_month);
        array_push($returnArray, $month_total);
        //  dd($returnArray);
        return $returnArray;
    }

    public function listSavedPaidCommissions($agent_name)
    {
        //	$name = $request->get('saved_name');
        $data = [];
        $line = [];
        $returnArray = [];
        $paid_commissions_by_month = [];

        $savedCommission = SavedCommission::where('month', '>=', 6)
            ->where('is_commissions_paid', '>', 0)
            ->orderby('created_at', 'desc')
            ->get();
        $months = [];
        foreach ($savedCommission as $sc) {
            array_push($months, [
                'month_name' => (DateTime::createFromFormat('!m', $sc->month))->format('F'),
                'description' => $sc->description,
                'name' => $sc->name,
                'month' => $sc->month,
                'rep' => $agent_name,
            ]);
        }
        foreach ($months as $month) {
            $q = DB::table($month['name'])->where('rep', 'like', $month['rep']);
            /*    echo $q->count() . "<br>";

            }
                    dd($months);*/
        }

        return $months;
    }

    public function viewSavedPaidCommissions($table_name, $rep, $description)
    {
        $paids = [];

        //dd($table_name);
        if (! Schema::hasTable($table_name)) {
            return view('nodata');
        } else {
            $paids = DB::table($table_name)
                ->orderBy('customer_name', 'asc')
                ->orderBy('order_number', 'desc')
                ->get();

            //     array_push($paids, $query);
        }

        $queries = DB::table($table_name)->select(DB::table($table_name)->raw('*,
                sum(commission) as sum_commission,
                sum(amount) as sum_volume,
                avg(NULLIF(margin,0))as avg_margin,
                EXTRACT(YEAR_MONTH FROM '.$table_name.'.invoice_date) as summary_year_month 
                '))
            //			->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', $table_name . '.sales_person_id')
            //			->where('sales_persons.region', '!=', null)
            ->orderBy('order_number', 'desc')
            ->where('rep', 'like', $rep)
            ->groupBy('rep')
            ->groupBy('summary_year_month')
            ->get();
        //dd($queries->toArray());
        $months = [];
        $paid_commissions_by_month = [];
        $total_volume = 0;
        $total_commission = 0;
        foreach ($queries as $query) {
            //          if ($query->sum_volume) {
            $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
            array_push($paid_commissions_by_month, [
                'month' => $month,
                'month_number' => intval(substr($query->summary_year_month, 4, 2)),
                'commission_per_month' => $query->sum_commission,
                'volume_per_month' => $query->sum_volume,
                'margin_per_month' => $query->avg_margin,
                'invoice_date_so' => date('m-d-Y', strtotime(substr($query->invoice_date, 0, 10))),
                //     'invoice_paid_at_so' => date("m-d-Y", strtotime(substr($query->invoice_paid_at, 0, 10))),
                'invoice_paid_at_so' => '',
                'commission_paid_at_so' => '',
            ]);
            //               }
        }
        $total_volume += $query->sum_volume;
        $total_commission += $query->sum_commission;

        // dd($paid_commissions_by_month);

        $queries = DB::table($table_name)->select(DB::table($table_name)->raw('*,
                        sum(commission) as sum_commission,
                        sum(amount) as sum_volume,
                        avg(NULLIF(margin,0))as avg_margin,
                EXTRACT(YEAR_MONTH FROM '.$table_name.'.invoice_date) as summary_year_month,
                EXTRACT(MONTH FROM '.$table_name.'.invoice_date) as so_month 
                        '))
            ->where('rep', 'like', $rep)
            ->groupBy('order_number')
            ->get();
        $so = [];
        foreach ($queries as $query) {
            array_push($so, [
                'months' => $queries,
                'month_number' => intval(substr($query->summary_year_month, 4, 2)),
                'order_number' => $query->order_number,
                'commission_per_so' => $query->sum_commission,
                'volume_per_so' => $query->sum_volume,
                'margin_per_so' => $query->avg_margin,
                'invoice_date_so' => date('m-d-Y', strtotime(substr($query->invoice_date, 0, 10))),
                'invoice_paid_at_so' => '',
                'commission_paid_at_so' => '',
            ]);
        }

        /*        $returnArray = [];
                array_push($returnArray, $paids);
                array_push($returnArray, $months);
                array_push($returnArray, $so);

                dd($returnArray);*/
        // dd(collect($paid_commissions_by_month));
        return view('commissions.paid_out_accordion',
            [
                'description' => $description,
                'name' => $query->rep,
                'paids' => $paids,
                'paid_subtotals_so' => $so,
                'paid_subtotals_month' => collect($paid_commissions_by_month),
                'total_volume' => $total_volume,
                'total_commission' => $total_commission,

            ]);
    }

    public function viewSavedPaidCommissionsbyRep(Request $request)
    {
        // $savedCommission = SavedCommission::where('month', '=', $month)->first();
        $timeFrame = [
            'months' => $request->get('months'),
            'year' => $request->get('year'), ];

        $data = [];

        foreach ($timeFrame['months'] as $month) {
            $savedCommission = SavedCommission::where('month', '=', $month)->first();
            if ($savedCommission) {
                $lastMonth = end($timeFrame['months']);
                $dateFrom = substr(new Carbon($timeFrame['year'].'-'.$timeFrame['months'][0].'-01'), 0, 10);
                $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-01'), 0, 10);
                $lastDay = date('t', strtotime($dateTo));
                $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-'.$lastDay), 0, 10);

                if (! Schema::hasTable($savedCommission->name)) {
                    return view('nodata');
                } else {
                    $queries = DB::table($savedCommission->name)->select(DB::raw('*,
                sum(commission) as sp_commission,
                sum(amount) as sp_volume,
                avg(NULLIF(margin,0))as sp_margin,
                EXTRACT(YEAR_MONTH FROM '.$savedCommission->name.'.invoice_date) as summary_year_month 
                '))
                        ->orderBy('rep')
                        ->groupBy('rep')
                        ->get();

                    foreach ($queries as $query) {
                        $monthNum = $month;
                        $dateObj = DateTime::createFromFormat('!m', $monthNum);
                        $monthName = $dateObj->format('F'); // March
                        $month_name = $monthName.' '.substr($timeFrame['year'], 0);
                        if ($query->sp_volume) {
                            array_push($data, [
                                'month' => $month_name,
                                'rep' => $query->rep,
                                'commission' => $query->sp_commission,
                                'volume' => $query->sp_volume,
                                'margin' => $query->sp_margin,
                            ]);
                        }
                    }
                }
            }
        }
        // dd($all_months);

        return view('tables.rep_totals_per_month', ['header' => '', 'overview' => json_encode($data)]);
    }
}
