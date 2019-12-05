<?php

namespace App\Traits;
use DB;
use Illuminate\Http\Request;
use App\Commission;
trait CommissionTrait {

    public function getCommission($margin, $region, $version, $sales_person_id = 0, $invoice_date = '') {
        $query = Commission::select(DB::raw('max(margin) as max_margin'))->where('region', '=', $region)
            ->get();
        foreach ($query as $q) {
            $max_margin = $q->max_margin;
        }
        if ($margin > $max_margin) {
            $margin = $max_margin;
        };

        $comms = Commission::
        where('margin', '=', $margin)->
        where('region', '=', $region)->
        where('version', '=', $version)->
        limit(1)->get();
        foreach ($comms as $comm) {
			if ($comm->commission > 0 AND $sales_person_id == 14 AND $invoice_date >= date('Y-m-d', strtotime('2019-04-01'))) {
				$commission = 0.05;
				return $commission;
			} else {
				return ($comm->commission);
			}
        }

    }

}
