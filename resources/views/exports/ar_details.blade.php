<div class="container">
    <div class="card">
        <div class='card-header'>
            <h5>Aged Receivables Salesorders </h5>
        </div>
        <div class="card card-body">
            <div id="div12">
            </div>
            <table id="accounts" class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th style="font-weight: normal" class="text-xl-left">Rep</th>
                    <th style="font-weight: normal" class="text-xl-left">Customer</th>
                    <th style="font-weight: normal" class="text-xl-left">SO</th>
                    <th style="font-weight: normal" class="text-xl-right">Not Today</th>
                    <th style="font-weight: normal" class="text-xl-right">7 days</th>
                    <th style="font-weight: normal" class="text-xl-right">14 days</th>
                    <th style="font-weight: normal" class="text-xl-right">30 days</th>
                    <th style="font-weight: normal" class="text-xl-right">60 days</th>
                    <th style="font-weight: normal" class="text-xl-right">90 days</th>
                    <th style="font-weight: normal" class="text-xl-right">120 days</th>
                    <th style="font-weight: normal" class="text-xl-right">Older</th>
                    <th style="font-weight: normal" class="text-xl-right">Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ars as $ar)
                    <tr>
                        <td class="text-xl-left">{{substr($ar->rep,0,20)}}</td>
                        <td class="text-xl-left"><b>{{substr($ar->customer,0,20)}}</b></td>
                        <td class="text-xl-left">{{$ar->sales_order}}</td>
                        <td class="text-xl-right">{{ $ar->range0 ? $ar->range0 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ar->range1 ? $ar->range1 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ar->range2 ? $ar->range2 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ar->range3 ? $ar->range3 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ar->range4 ? $ar->range4 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ar->range5 ? $ar->range5 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ar->range6 ? $ar->range6 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ar->range7 ? $ar->range7 : ''}}
                        </td>
                        <td class="text-xl-right">{{ $ar->residual ? $ar->customer_total
                            : ''}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

