<?php

namespace App\Http\Controllers;

    use App\Commission;
    use App\Customer;
    use App\Earning;
    use App\Month;
    use App\SaleInvoice;
    use App\Salesline;
    use App\SalesPerson;
    use App\SavedCommission;
    use App\Traits\CommissionTrait;
    use App\User;
    use Carbon\Carbon;
    use Gate;
    use Illuminate\Auth\Authenticatable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Lava;

    class Commissioncontroller3 extends Controller
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

            //dd($salesperson->toarray());

            $months = Month::all();
            $now = Carbon::now();
            $months = Month::where('month_id', '<=', $now->month)
                ->orderBy('month_id', 'desc')
                ->get();
            //	dd($months);
            $user = User::where('id', '=', auth()->id())->first();
            $salesperson_id = $user->sales_person_id;
            $salesperson_name = $user->name;

            return view('home', [
                'salesperson_name' => $salesperson_name,
                'salesperson_id' => $salesperson_id,
                'data' => $data,
                'months' => $months,
                'year' => $now->year,
                'allMonths' => Month::all(),
                'currentMonth' => Carbon::now()->month,
                'salesperson' => $salesperson,
            ]);
        }

        public function calcCommissions(Request $request)
        {
            $data = [
                'month' => $request->get('month'),
                'year' => $request->get('year'),
                'salesperson_id' => $request->get('salesperson_id'),
                'commission_version' => 1,
            ];

            session(['data' => $data]);
            $salesperson_id = 0;
            $user = User::where('id', '=', auth()->id())->first();
            if (! $user) {
                dd('rejected');
            }
            $salesperson_id = $user->sales_person_id;

            if (! Gate::allows('isAdmin')) {
                abort_if($data['salesperson_id'] != $salesperson_id, 403);
            }

            $salesperson_id = $data['salesperson_id'];
            if ($salesperson_id == null) {
                return view('noexist');
            }
            $paidCommissionItems = [];
            if ($data['year'] >= 2019 and $data['month'] >= 6) {
                $paidCommissionItems = $this->PayedCommissionsPerRep($data);
                //	  dd($paidCommissionItems);
            }
            $salesorderItems = $this->salesOrdersPerSalesPerson($data);
            $customerItems = $this->commissionsPerCustomer($data);
            //  $brandItems = $this->commissionsPerBrand($data);
            // dd($brandItems);

            $monthItems = $this->commissionsPerMonth($data);

            $lineItems = $this->salesOrdersLinesPerSalesOrder($data);
            //   dd($lineItems);
            /*            echo $customerItems[0]['customers']->count();
                        echo $customerItems[1]['customers']->count();*/
            //    echo $brandItems[0]['brands']->count();
            //  echo $brandItems[1]['brands']->count();
            //    dd("xxxx");

            if ((! $customerItems[0]['customers']->count() and ! $customerItems[1]['customers']->count())
            //    or (!$brandItems[0]['brands']->count() and !$brandItems[1]['brands']->count())
                or ! $monthItems->count()
                or ! $salesorderItems
                or ! $lineItems
                or ! $paidCommissionItems
            ) {
                return view('nodata');
            }
            $items = $lineItems[0]['items'];
            $so_items = $salesorderItems[0]['so_items'];

            return view('commissions', compact(
                'lineItems',
                'items',
                'salesorderItems',
                'so_items',
     //           'brandItems',
                'monthItems',
                'customerItems',
                'data',
                'paidCommissionItems'
            ));
        }

        public function PayedCommissionsPerRep($data)
        {
            $month = $data['month'];
            $year = $data['year'];
            $rep_id = $data['salesperson_id'];
            $is_current_month = $month == Carbon::now()->month;
            $paidCommissionDateFrom = env('PAID_INVOICES_START_DATE', '2019-06-01');

            $paidCommissionItems = [];
            $paidCommissionItem = [];
            $month = $month + 1;

            for ($i = 0; $i < 3; $i++) {
                $month = $month - 1;
                if ($month <= 0) {
                    $month = 12;
                }

                $is_current_month = $month == Carbon::now()->month;

                if ($month < 6 and $year == 2019) {
                    array_push($paidCommissionItems, ['commission' => '']);
                } else {
                    $timeFrame = ['year' => 2019, 'months' => [Carbon::now()->month]];
                    $timeFrame = ['year' => $year, 'month' => [$month]];
                    $lastMonth = end($timeFrame['month']);
                    $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-01'), 0, 10);
                    $lastDay = date('t', strtotime($dateTo));
                    $dateTo = substr(new Carbon($timeFrame['year'].'-'.$lastMonth.'-'.$lastDay), 0, 10);

                    if ($is_current_month) {
                        $queries = SalesLine::select(DB::raw('*,
                        sum(commission) as sp_commission,
                        sum(amount) as sp_volume,
                        avg(NULLIF(margin,0))as sp_margin,
                        EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month 
                        '))
                            ->where('sales_person_id', $rep_id)
                            ->whereBetween('saleslines.invoice_date', [$paidCommissionDateFrom, $dateTo])
                            ->whereHas('invoice_paid', function ($query) {
                                $query->where('is_paid', false);
                            })
                            ->first();

                        array_push($paidCommissionItems, ['commission' => $queries->sp_commission]);
                    } else {
                        $saved_table = SavedCommission::orderby('created_at', 'desc')
                            ->where('month', $month)
                            ->where('is_commissions_paid', true)
                            ->first();
                        if (! $saved_table) {
                            $commission = 'not calculated yet';
                        } //  abort_if(!$saved_table, 403);
                        else {
                            $queries = DB::table($saved_table->name)->select(DB::raw('*,
                        sum(commission) as sp_commission,
                        sum(amount) as sp_volume,
                        avg(NULLIF(margin,0))as sp_margin,
                        EXTRACT(YEAR_MONTH FROM invoice_date) as summary_year_month 
                        '))
                                ->where('rep', 'like', 'Bill Satterfield')
                                ->whereBetween('invoice_date', [$paidCommissionDateFrom, $dateTo])
                                ->first();
                            $commission = $queries->sp_commission;
                        }
                        array_push($paidCommissionItems, ['commission' => $commission]);
                        //	dd($paidCommissionItems);
                    }
                }
            }

            return $paidCommissionItems;
        }

        public function salesOrdersPerSalesPerson($data)
        {
            $commission_version = $data['commission_version'];
            $returnValues = [];
            $month = $data['month'];

            $salesperson_id = $data['salesperson_id'];
            $month = $month + 1;
            for ($i = 0; $i < 3; $i++) {
                $month = $month - 1;
                if ($month <= 0) {
                    $month = 12;
                }
                $so_items = SaleInvoice::
                select(DB::raw('customer_id,invoice_number,invoice_state,sales_person_id, order_date, margin,  
				count(order_id) as salesorders,	
				sum(commission) as order_commission, 
				sum(price_subtotal) as order_total,
				
				avg(NULLIF(margin,0)) as margin_average,
				customers.name as customer_name
			'))
                    ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                    ->where('sales_person_id', '=', $salesperson_id)
                    //   ->where('invoice_state', '=', 'paid') // only for test remove after
                    ->whereRaw('MONTH(invoice_lines.invoice_date) = ?', ($month))
                    ->orderby('customers.name')
                    ->groupBy('order_id')
                    ->get();
                $salesperson_name = '';
                $empty = 0;
                $salesorder_count = 0;
                $margin_count = 0;
                $average_margin_sum = 0;
                foreach ($so_items as $so_item) {
                    if ($so_item->salesorders > 0) {
                        $salesorder_count++;
                    } else {
                        $empty += 1;
                    }

                    if ($so_item->order_total > 0) {
                        $margin_count++;
                        $average_margin_sum += $so_item->margin_average;
                    }
                    $salesperson_name = $so_item->salesperson->name;
                }

                $av_margin = 0;
                if ($margin_count) {
                    $av_margin = $average_margin_sum / $margin_count;
                }
                $data = ['so_items' => $so_items, 'salesorder_count' => $salesorder_count, 'margin_average' => $av_margin, 'salesperson_name' => $salesperson_name];
                $data['dMonth'] = date('F', mktime(0, 0, 0, $month, 1));
                $data['margin_average'] = number_format($data['margin_average'], 2).' %';
                array_push($returnValues, $data);
            }

            return $returnValues;
        }

        public function salesOrdersLinesPerSalesOrder($data)
        {
            $commission_version = $data['commission_version'];
            $returnValues = [];
            $month = $data['month'];
            $month = $month + 1;

            //	echo "month_in 2= " . $month . "<br>";

            $salesperson_id = $data['salesperson_id'];
            for ($i = 0; $i < 3; $i++) {
                $month = $month - 1;
                if ($month <= 0) {
                    $month = 12;
                }
                $items = SaleInvoice::
                when($salesperson_id, function ($query, $salesperson_id) {
                    return $query->where('sales_person_id', '=', $salesperson_id);
                })
                    ->whereRaw('MONTH(invoice_lines.invoice_date) =?', ($month))
                    ->orderby('name', 'desc')
                    ->get();
                //dd($items->toarray());
                if ($items->toArray()) {
                    $total_commission = 0;
                    $total_sales = 0;
                    foreach ($items as $item) {
                        $commission_percent = $this->getCommission(round($item->margin, 0, PHP_ROUND_HALF_DOWN), $item->salesperson->region, $commission_version, $item->sales_person_id, $item->invoice_date);
                        $commission = $item->price_subtotal * $commission_percent;
                        $total_commission += $commission;
                        $total_sales += $item->amt_invoiced;
                        $total_sales += $item->amt_to_invoice;
                        $development = true;
                        if ($development) {
                            $si = SaleInvoice::find($item->id);
                            $si->commission = $commission;
                            $si->comm_percent = $commission_percent;
                            $si->save();
                        }
                    }

                    $data = ['month' => $month, 'items' => $items, 'commission_percent' => $commission_percent, 'commission' => $commission, 'total_commission' => $total_commission, 'total_sales' => $total_sales];
                    $data['total_commission'] = '$'.number_format($data['total_commission'], 2, '.', ',');
                    $data['total_sales'] = '$'.number_format($data['total_sales'], 2, '.', ',');
                    $data['items_count'] = $data['items']->count();
                    array_push($returnValues, $data);
                } else {
                    $data = ['month' => $month, 'items' => [], 'commission_percent' => 0, 'commission' => 0, 'total_commission' => 0, 'total_sales' => 0];
                    $data['total_commission'] = '0';
                    $data['items_count'] = 0;
                    array_push($returnValues, $data);
                }
            }
            //	dd($returnValues);
            return $returnValues;
        }

        public function commissionsPerCustomer($data)
        {
            $commission_version = 1;
            $commission_version = $data['commission_version'];
            $month = $data['month'];
            //	echo "month_in 3= " . $month . "<br>";
            $month = $month + 1;

            $returnValues = [];
            $salesperson_id = $data['salesperson_id'];

            for ($i = 0; $i < 2; $i++) {
                $month = $month - 1;
                if ($month <= 0) {
                    $month = 12;
                }
                //		echo "month3= " . $month . "<br>";
                $dMonth = date('F', mktime(0, 0, 0, $month, 1));

                $customerItems = SaleInvoice::select(DB::raw('customer_id,customers.name as customer_name,
                count(distinct(invoice_number)) as customer_count,
                sum(commission) as customer_commission,
                sum(price_subtotal) as customer_volume,
                avg(NULLIF(margin,0))as customer_margin
                '))
                    ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                    ->where('sales_person_id', '=', $salesperson_id)
                    ->whereRaw('MONTH(invoice_lines.invoice_date) = ?', ($month))
                    ->orderBy('customer_name', 'asc')
                    ->groupBy('customer_id')->get();

                //             dd($customerItems);

                $chartItems = SaleInvoice::select(DB::raw('
					customers.name as "0",
					sum(commission) as "1",
/*					avg(NULLIF(margin,0)) as "2",*/
					count(distinct(invoice_number)) "3"
					'))
                    ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                    ->where('sales_person_id', '=', $salesperson_id)
                    ->whereRaw('MONTH(invoice_lines.invoice_date) = ? ', ($month))
                    ->orderBy('1', 'desc')
                    ->groupBy('customer_id')
                    ->get()->toArray();

                $customers = Lava::DataTable();
                $title = 'Sales for '.$salesperson_id;
                $customers->addStringColumn('Customer');
                $customers->addnumberColumn('Commission $');
                /*				$customers->addnumberColumn('Avg. Margin');*/
                $customers->addnumberColumn('Sales #');

                if ($chartItems) {
                    $customers->addRows($chartItems);
                }
                Lava::BarChart('Customer'.$i, $customers, [
                    'title' => 'Sales per Customer in '.$dMonth,
                    'height' => 600,
                    'width' => 450,
                    'isStacked' => true,
                    'is3D' => false,
                    'bar' => ['groupWidth' => '50%'],
                    'vAxis' => ['textPosition' => 'none'],
                ]);

                $salesTotal = 0;
                $commissionTotal = 0;
                $marginTotal = 0;
                $averageMarginTotal = 0;
                $margin_count = 0;
                //dd($customerItems->toArray());
                if ($customerItems->count()) {
                    for ($j = 0; $j < $customerItems->count(); $j++) {
                        $salesTotal += ($customerItems->toArray()[$j]['customer_volume']);
                        $commissionTotal += ($customerItems->toArray()[$j]['customer_commission']);
                        if ($customerItems->toArray()[$j]['customer_volume'] > 0) {
                            $margin_count++;
                            $marginTotal += ($customerItems->toArray()[$j]['customer_margin']);
                        }
                    }
                    if ($margin_count) {
                        $averageMarginTotal = $marginTotal / $margin_count;
                    }
                }
                $data = [
                    'month' => $month,
                    'dMonth' => $dMonth,
                    'customers' => $customerItems,
                    'salesTotal' => $salesTotal,
                    'commissionTotal' => $commissionTotal,
                    'averageMarginTotal' => $averageMarginTotal,
                ];
                array_push($returnValues, $data);

                //				echo $salesTotal . "<br>";
            }
            //		dd($returnValues);
            return $returnValues;
        }

        public function commissionsPerBrand($data)
        {
            $commission_version = 1;
            $salesperson_id = $data['salesperson_id'];
            $month = $data['month'];

            $title = 'Sales for '.$salesperson_id;
            $month = $data['month'];
            $month = $month + 1;

            $returnValues = [];
            $salesperson_id = $data['salesperson_id'];
            for ($i = 0; $i < 2; $i++) {
                $month = $month - 1;
                if ($month <= 0) {
                    $month = 12;
                }
                $dMonth = date('F', mktime(0, 0, 0, $month, 1));
                $brandItems = SaleInvoice::select(DB::raw('brands.name as brand_name,
					avg(NULLIF(margin,0)) as brand_margin,
					sum(commission) as brand_commission,
					count(brand_id) as brand_count,
					sum(price_subtotal) as brand_volume
					'))
                    ->leftJoin('brands', 'brands.ext_id', '=', 'invoice_lines.brand_id')
                    ->where('brands.is_active', '=', true)
                    ->where('sales_person_id', '=', $salesperson_id)
                    ->whereRaw('MONTH(invoice_lines.invoice_date) = ? ', ($month))
                    ->orderBy('brand_commission', 'desc')
                    ->groupBy('brand_id')
                    ->get();

                $chartItems = SaleInvoice::select(DB::raw('brands.name as "0",
					sum(commission) as "1",
/*					avg(NULLIF(margin,0)) as "2",*/
					count(brand_id) "3"
					'))
                    ->leftJoin('brands', 'brands.ext_id', '=', 'invoice_lines.brand_id')
                    ->where('brands.is_active', '=', true)
                    ->where('sales_person_id', '=', $salesperson_id)
                    ->where('sales_person_id', '=', $salesperson_id)
                    ->whereRaw(
                        'MONTH(invoice_lines.invoice_date) = ? ', ($month))
                    ->orderBy('1', 'desc')
                    ->groupBy('brand_id')
                    ->get()->toArray();

                $brands = Lava::DataTable();
                $brands->addStringColumn('Brand');
                $brands->addnumberColumn(' Commission $');
                /*				$brands->addnumberColumn(' Avg. Margin');*/
                $brands->addnumberColumn(' Sales #');
                if (count($chartItems)) {
                    $brands->addRows($chartItems);
                }
                Lava::BarChart('Brand'.$i, $brands, [
                    'title' => 'Sales per Brand in '.$dMonth,
                    'height' => 900,
                    'width' => 450,
                    'isStacked' => true,
                    'bar' => ['groupWidth' => '50%'],
                    'vAxis' => ['textPosition' => 'none'],
                ]);

                $salesTotal = 0;
                $commissionTotal = 0;
                $marginTotal = 0;
                $averageMarginTotal = 0;
                $margin_count = 0;
                //dd($customerItems->toArray());
                if ($brandItems->count()) {
                    for ($j = 0; $j < $brandItems->count(); $j++) {
                        $salesTotal += ($brandItems->toArray()[$j]['brand_volume']);
                        $commissionTotal += ($brandItems->toArray()[$j]['brand_commission']);
                        if ($brandItems->toArray()[$j]['brand_volume'] > 0) {
                            $margin_count++;
                            $marginTotal += ($brandItems->toArray()[$j]['brand_margin']);
                        }
                    }
                    if ($margin_count) {
                        $averageMarginTotal = $marginTotal / $margin_count;
                    }
                }

                $data = ['month' => $month,
                    'dMonth' => $dMonth,
                    'brands' => $brandItems,
                    'salesTotal' => $salesTotal,
                    'commissionTotal' => $commissionTotal,
                    'averageMarginTotal' => $averageMarginTotal, ];

                array_push($returnValues, $data);
            }
            //		dd($returnValues);
            return $returnValues;
        }

        public function commissionsPerMonth($data)
        {
            $months = Lava::DataTable();
            $title = 'Sales per month';
            $month = $data['month'];

            $salesperson_id = $data['salesperson_id'];
            $monthItems = SaleInvoice::select(DB::raw('sales_person_id,
				sum(price_subtotal) as month_sale,
				sum(commission) as month_commission,
				avg(NULLIF(margin,0)) as month_margin,
				count(distinct(invoice_number)) as month_sold,
				MONTH(invoice_lines.invoice_date) as month, 
				YEAR(invoice_date) as year'))
                //		->has('salesperson')
                ->where('sales_person_id', '=', $salesperson_id)
                ->orderBy('invoice_date', 'asc')
                ->groupBy('month')
                ->groupBy('sales_person_id')
                ->get();

            //      dd($monthItems);
            $monthChartItems = SaleInvoice::select(DB::raw('
            MONTH(invoice_lines.invoice_date) as "0", 
            sum(commission) as "1",
/*            avg(NULLIF(margin,0)) as "2",*/
            count(distinct(invoice_number)) as "3"
        '))
                ->has('salesperson')
                ->where('sales_person_id', '=', $salesperson_id)
                ->orderBy('invoice_date', 'asc')
                ->groupBy('0')
                ->get()->toArray();

            $months->addStringColumn('Month');
            $months->addnumberColumn(' Commission $');
            /*			$months->addnumberColumn(' Avg. Margin');*/
            $months->addnumberColumn(' Sales #');
            if (count($monthChartItems)) {
                $months->addRows($monthChartItems);
            }
            Lava::ColumnChart('Months', $months, [
                'title' => 'Sales per Month',
                'height' => 600,
                'width' => 780,
                'bar' => ['groupWidth' => '50%'],
            ]);

            return $monthItems;
        }

        public function commissionsPerCustomerBrand($customer_id, $customer_name, $salesperson_id, $month)
        {
            $commission_version = 1;

            $customerBrandItems = SaleInvoice::select(DB::raw('brands.name as "0",sum(commission) as "1"'))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->leftJoin('brands', 'brands.ext_id', '=', 'invoice_lines.brand_id')
                ->where('sales_person_id', '=', $salesperson_id)
                ->whereRaw('MONTH(invoice_lines.invoice_date) = ? ', ($month))
                ->where('customer_id', '=', $customer_id)
                ->orderBy('customer_id')
                ->orderBy('brand_id')
                ->groupBy('brand_id')
                ->get()->toArray();

            $customerBrands = Lava::DataTable();
            $title = 'Sales for '.$month;
            $customerBrands->addStringColumn('Customer');
            $customerBrands->addnumberColumn('Brands');
            if ($customerBrandItems) {
                $customerBrands->addRows($customerBrandItems);
            }
            $dmonth = date('F', mktime(0, 0, 0, $month, 1));

            Lava::DonutChart('CustomerBrand', $customerBrands, [
                'title' => 'Brands for '.$dmonth,
            ]);

            $prevMonth = $month - 1;
            if ($prevMonth <= 0) {
                $prevMonth = 12;
            }

            $customerBrandItems2 = SaleInvoice::select(DB::raw('brands.name as "0",sum(commission) as "1"'))
                ->leftJoin('customers', 'customers.ext_id', '=', 'invoice_lines.customer_id')
                ->leftJoin('brands', 'brands.ext_id', '=', 'invoice_lines.brand_id')
                ->where('sales_person_id', '=', $salesperson_id)
                ->whereRaw('MONTH(invoice_lines.invoice_date) = ? ', ($prevMonth))
                ->where('customer_id', '=', $customer_id)
                ->orderBy('customer_id')
                ->orderBy('brand_id')
                ->groupBy('brand_id')
                ->get()->toArray();
            $customerBrands2 = Lava::DataTable();
            $title = 'Sales for '.$prevMonth;

            $customerBrands2->addStringColumn('Customer');
            $customerBrands2->addnumberColumn('Brands');
            if (empty($customerBrandItems2)) {
                $customerBrands2->addRows([['Not found'], [0]]);
            } else {
                $customerBrands2->addRows($customerBrandItems2);
            }
            $dprevMonth = date('F', mktime(0, 0, 0, $prevMonth, 1));
            Lava::DonutChart('CustomerBrand2', $customerBrands2, [
                'title' => 'Brands for '.$dprevMonth,
            ]);

            return view('customer_donut', compact('customer_name'));
        }

        public function geoChart()
        {
            $dispensaries = Lava::DataTable();
            $order_date = 12;
            $title = 'Sales for '.$order_date;

            //     $data = BccRetailer::select("city as 0", "business_name as 1")->limit(20)->get()->toArray();
            $salesorders = Customer::
            select(\DB::raw('name as "0", avg(amount_total) as "1", count(customer_id) as "2"'))
                ->leftJoin('salesorders', 'salesorders.customer_id', '=', 'ext_id')
                ->whereMonth('salesorders.order_date', $order_date)
                ->groupby('salesorders.customer_id')
                ->get()->toArray();
            $dispensaries->addStringColumn('Dispensary')
                ->addnumberColumn(' Avg Sold')
                ->addnumberColumn('# Sales')
                ->addRows($salesorders);
            Lava::GeoChart('Dispensary', $dispensaries, ['displayMode' => 'markers', 'region' => 'US-CA',
                'resolution' => 'metros',
                'colorAxis' => ['colors' => ['red', 'green']],
                //         'sizeAxis' => ['minValue' => 0, 'maxValue'=> 20000]
            ]);

            return view('charts.geocharts.sales', compact('title'));
        }

        public function testchart()
        {
            $finances = Lava::DataTable(); // See note below for Laravel

            $finances->addDateColumn('Year')
                ->addNumberColumn('Genre')
                ->addNumberColumn('Fantasy & Sci Fi')
                ->addNumberColumn('Romance')
                ->addNumberColumn('General')
                ->addNumberColumn('Western')
                ->addNumberColumn('Literature')
                ->addNumberColumn('Mystery?Crime')
                ->setDateTimeFormat('Y')
                ->addRow(['2010', 10, 24, 20, 32, 18, 5])
                ->addRow(['2020', 16, 22, 23, 30, 16, 9])
                ->addRow(['2030', 28, 19, 29, 30, 12, 13]);

            Lava::ColumnChart('Finances', $finances, [
                'title' => 'Company Performance',
                'titleTextStyle' => [
                    'color' => '#eb6b2c',
                    'fontSize' => 14,
                ],
                'isStacked' => true,
            ]);

            return $finances;
        }
    }
