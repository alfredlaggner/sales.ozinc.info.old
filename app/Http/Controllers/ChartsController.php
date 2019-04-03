<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\BccRetailer;
use App\Customer;
use App\Traits\CommissionTrait;

class ChartsController extends Controller
{
    use CommissionTrait;

    public function unUsedCustomers(Request $request)
    {

        //	$sales_person_id = $request->get('sales_person_id');
        $sales_person_id = 14;
        $bccCustomers = BccRetailer::
        select(\DB::raw('city as "0",business_name as "1", phone as "2"'))
            //	->join('customers', 'customers.name', 'like', 'bcc_retailers.business_name')
            //	->join('saleinvoices', 'saleinvoices.customer_id', '!=', 'customers.ext_id')
            ->where('bcc_retailers.sales_person_id', $sales_person_id)
            ->where('bcc_retailers.is_odoo', '=', 0)
            ->get()->toArray();
        //		dd($bccCustomers);

        $dispensaries = \Lava::DataTable();

        $dispensaries->addStringColumn('City');
        $dispensaries->addStringColumn('Dispensary');
        $dispensaries->addStringColumn('Phone');
       $dispensaries->addRows($bccCustomers);

dd($dispensaries);
        \Lava::GeoChart('Dispensary', $dispensaries,
            ['displayMode' => 'markers', 'region' => 'US-CA',
                'resolution' => "provinces",
                'colorAxis' => ['colors' => ['red', 'green']],
            ]);

        $title = "Sales for " . $sales_person_id;

        return view('charts.bcc_customers', compact('title'));

    }

    public function geoChart()
    {

        $order_date = 12;
        $title = "Sales for " . $order_date;

        //     $data = BccRetailer::select("city as 0", "business_name as 1")->limit(20)->get()->toArray();
        $salesorders = Customer::
        select(\DB::raw('name as "0", avg(amount_total) as "1", count(customer_id) as "2"'))
            ->join('salesorders', 'salesorders.customer_id', '=', 'customers.ext_id')
            ->whereMonth('salesorders.order_date', $order_date)
            ->groupby('salesorders.customer_id')
            ->get()->toArray();
        $dispensaries = \Lava::DataTable();
        $dispensaries->addStringColumn('Dispensary')
            ->addnumberColumn(' Avg Sold')
            ->addnumberColumn('# Sales')
            ->addRows($salesorders);

        //	dd($dispensaries);

        \Lava::GeoChart('Dispensary', $dispensaries, ['displayMode' => 'markers', 'region' => 'US-CA',
            'resolution' => "provinces",
            'colorAxis' => ['colors' => ['red', 'green']],
        ]);


        return view('charts.bcc_customers', compact('title'));
    }


    public function geoChartBrands(Request $request)
    {
        $brand = '';
        $brand = $request->get('brand') ? $request->get('brand') : 0;
        $brand = strtoupper($brand);
        $month = $request->get('month') ? $request->get('month') : 0;
        $dispensaries = \Lava::DataTable();

        $saleslines = Customer::
        select(\DB::raw('customers.name as "0", sum(quantity) as "1", count(customer_id) as "2"'))
            ->join('saleinvoices', 'saleinvoices.customer_id', '=', 'customers.ext_id')
            ->when($brand, function ($query, $brand) {
                return $query->whereRaw("upper(saleinvoices.name) LIKE '%" . $brand . "%'");
            })
            ->when($month, function ($query, $month) {
                return $query->whereMonth('saleinvoices.created_at', $month);
            })
            ->groupby('saleinvoices.customer_id')
            ->get()->toArray();
        dd($saleslines);
        //    dd($data);
        $dispensaries->addStringColumn('Dispensary')
            ->addnumberColumn('Units Sold')
            ->addnumberColumn('# Sales')
            ->addRows($saleslines);

        \Lava::GeoChart('Dispensary', $dispensaries, ['displayMode' => 'markers', 'region' => 'US-CA',
            'resolution' => "provinces",
            'colorAxis' => ['colors' => ['red', 'green']],
        ]);

        $title = [["Sales of: " . $brand], ["Month: " . $month], ["Dispensaries: " . count($saleslines)]];
        return view('charts.geocharts.brands', ['title' => $title, 'months' => Month::all()]);

    }

}
