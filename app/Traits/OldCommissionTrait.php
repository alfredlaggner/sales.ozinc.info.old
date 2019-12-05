<?php

    namespace App\Traits;

    use DB;
    use Illuminate\Http\Request;
    use App\OldCommission;
    use App\Commission;

    trait OldCommissionTrait
    {

        public function getCommission_old($margin, $region, $version, $sales_person_id = 0, $invoice_date = '')
        {
            $calc_date = date("m", strtotime($invoice_date));

            if ($calc_date <= 3) {
                $query = OldCommission::select(DB::raw('max(margin) as max_margin'))->where('region', '=', $region)
                    ->get();
            } else {
                $query = Commission::select(DB::raw('max(margin) as max_margin'))->where('region', '=', $region)
                    ->get();
            }
            foreach ($query as $q) {
                $max_margin = $q->max_margin;
            }
            if ($margin > $max_margin) {
                $margin = $max_margin;
            };
            if ($calc_date <= 3) {
                $comms = OldCommission::
                where('margin', '=', $margin)->
                where('region', '=', $region)->
                where('version', '=', $version)->
                limit(1)->get();
            } else {
                $comms = Commission::
                where('margin', '=', $margin)->
                where('region', '=', $region)->
                where('version', '=', $version)->
                limit(1)->get();
            }
            foreach ($comms as $comm) {
                return ($comm->commission);
            }

        }

    }
