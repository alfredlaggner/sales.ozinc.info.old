<?php

namespace App\Http\Controllers;

use App\Commission;
use App\Customer;
use App\Earning;
use App\Earning2;
use App\SaleInvoice;
use App\SalesOrder;
use App\SalesPerson;
use App\Traits\CommissionTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
        $timeFrame = ['year' => 2019, 'months' => [1, 2, 3]];
        $dateFrom = substr(new Carbon($timeFrame['year'].'-'.$timeFrame['months'][0].'-01'), 0, 10);
        $lastMonth = substr(new Carbon($timeFrame['year'].'-'.end($timeFrame['months']).'-01'), 0, 10);
        $dateTo = date('Y-m-t', strtotime($lastMonth));

        $returnValues = [];
        $queries = SaleInvoice::select(DB::raw('saleinvoices.sales_person_id as salesperson_id,sales_persons.name as salesperson_name,
                sum(commission) as sp_commission,
                sum(amt_invoiced + amt_to_invoice) as sp_volume,
                avg(NULLIF(margin,0))as sp_margin,
                EXTRACT(YEAR_MONTH FROM saleinvoices.invoice_date) as summary_year_month 
                '))
            ->leftJoin('sales_persons', 'sales_persons.sales_person_id', '=', 'saleinvoices.sales_person_id')
            ->where('sales_persons.region', '!=', null)
            ->whereBetween('saleinvoices.invoice_date', [$dateFrom, $dateTo])
            ->groupBy('saleinvoices.sales_person_id')
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
}
