<div class="container">
    <div class="card">
        <div class='card-header'>
            <h5>Aged Receivables Totals </h5>
        </div>
        <div class="card card-body">
            <div id="div12">
            </div>
            <table id="accounts" class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th style="font-weight: normal" class="text-xl-left">Rep</th>
                    <th style="font-weight: normal" class="text-xl-left">Customer</th>
                    <th style="font-weight: normal" class="text-xl-right">Not Today</th>
                    <th style="font-weight: normal" class="text-xl-right">7 days</th>
                    <th style="font-weight: normal" class="text-xl-right">14 days</th>
                    <th style="font-weight: normal" class="text-xl-right">30 days</th>
                    <th style="font-weight: normal" class="text-xl-right">60 days</th>
                    <th style="font-weight: normal" class="text-xl-right">90 days</th>
                    <th style="font-weight: normal" class="text-xl-right">120 days</th>
                    <th style="font-weight: normal" class="text-xl-right">Older</th>
                    <th style="font-weight: normal" class="text-xl-right">Total</th>
                    <th style="font-weight: normal" class="text-xl-right">Felon</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ars_totals as $ars_total)
                    <tr>
                        <td class="text-xl-left">{{substr($ars_total->rep,0,20)}}</td>
                        <td class="text-xl-left"><b>{{substr($ars_total->customer,0,20)}}</b></td>
                        <td class="text-xl-right">{{ $ars_total->range0 ? $ars_total->range0 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ars_total->range1 ? $ars_total->range1 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ars_total->range2 ? $ars_total->range2 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ars_total->range3 ? $ars_total->range3 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ars_total->range4 ? $ars_total->range4 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ars_total->range5 ? $ars_total->range5 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ars_total->range6 ? $ars_total->range6 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ars_total->range7 ? $ars_total->range7 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ars_total->residual ? $ars_total->customer_total
                            : ''}}
                        <td class="text-xl-right">{{ $ars_total->is_felon}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

