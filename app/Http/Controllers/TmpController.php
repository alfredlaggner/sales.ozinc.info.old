<?php

namespace App\Http\Controllers;

use App\Salesline;
use App\SavedCommission;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TmpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createSavedCommission(Request $request)
    {
        $paidCommissionDateFrom = env('PAID_INVOICES_START_DATE', '2019-06-01');
        $dateTo = '2019-09-13';

        $queries = SalesLine::select(DB::raw(
            '*,EXTRACT(YEAR_MONTH FROM saleslines.invoice_date) as summary_year_month,
            commissions_paids.saved_commissions_id as cp_saved_commission_id,
            commissions_paids.created_at as cp_created_at
            '
        ))
            ->leftjoin('commissions_paids', 'saleslines.ext_id', 'commissions_paids.ext_id')
            ->whereBetween('commissions_paids.created_at', [$paidCommissionDateFrom, $dateTo])
            ->where('commissions_paids.saved_commissions_id', '=', 107)
            //  ->where('amount', '>=', 0)
            ->orderBy('summary_year_month')
            ->orderBy('customer_name', 'asc')
            ->orderBy('order_number', 'desc')
            ->get();

        //dd($queries);

        /*        foreach ($queries as $query) {
                    echo $query->invoice_paid->ext_id . "<br>";
                }*/
        // dd();
        //   dd($queries->count());
        $is_add = true;

        if ($is_add == true) {
            $now = Carbon::now();
            $currentTime = $now->format('_Y_m_d_h_i_s');
            $newtable = 'invoices_paid'.$currentTime;
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
            $sc->description = 'add description';
            $sc->name = $newtable;
            $sc->date_created = $now;
            $sc->month = $request->get('months');
            $sc->year = $request->get('year');
            $sc->created_by = Auth::user()->name;
            $sc->save();
        }

        foreach ($queries as $query) {
            $month = date('F', mktime(0, 0, 0, substr($query->summary_year_month, 4, 2), 1));
            $line = [
                'month' => $month.' '.substr($query->summary_year_month, 0, 4),
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
}
