@php
    if($status == "paid"){
            $subtotals_month = $paid_subtotals_month;
            $subtotals_so = $paid_subtotals_so;
            $lines = $paids;
            $description = "All paid commissions";
            $total = 0;
            }
    elseif ($status == "unpaid"){
            $subtotals_month = $unpaid_subtotals_month[0];
            $subtotals_so = $unpaid_subtotals_so;
            $lines = $unpaids;
            $description = "Uncollected invoices. Total: $";
        //    dd($unpaid_subtotals_month);
            $total = $unpaid_subtotals_month[1]['total_uncollected'];

            }
    elseif ($status == "paid_out"){
            $subtotals_month = $paid_subtotals_month;
            $subtotals_so = $paid_subtotals_so;
            $lines = $paids;
            $description = "Paid on " . $description;
            $total = 0;
            }
 if(! count($subtotals_month))
 {$description = "Nothing found for " . $name;
 $name = ""; }
 $i = 1;
@endphp
<p> {{$description}}{{$total}}</p>
<div id="accordion">
    @foreach ($subtotals_month as $month_so)
        <div class="card">
            <div class="card-header" id="heading_{{$i}}">
                <h5 class="mb-0 d-inline">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse_{{$i}}"
                            aria-expanded="true" aria-controls="collapse_{{$i}}">
                        <table style=" table-layout: fixed ; width: 100% ;"
                               class="table table-sm table-hover table-bordered table-responsive-sm">
                            <td style=" width: 33% ;" align="left"><b>{{$month_so['month']}}</b></td>
                            <td style=" width: 33% ;" align="right">
                                ${{number_format($month_so['volume_per_month'],2)}}</td>
                            <td style=" width: 33% ;" align="right">
                                ${{number_format($month_so['commission_per_month'],2)}}</td>
                        </table>
                    </button>
                </h5>
            </div>
            <div id="collapse_{{$i}}" class="collapse show" aria-labelledby="heading_{{$i}}"
                 data-parent="#accordion">
                <div class="card-body" id="child_{{$i}}">
                    @php
                        $j = 0;
                    @endphp
                    @php
             //       echo $month_so['month_number'];
             //           dd($subtotals_so);

                      $so_months = $subtotals_so[0]['months'];
                //      dd($so_months);
                                 $subtotals_so_month = $so_months->where('so_month',$month_so['month_number']);
                //    dd($subtotals_so_month);
                    @endphp

                    @foreach ($subtotals_so_month as $so)
                        @php
                     //   dd($so);
                            //                        @endphp

                        <div class="card">
                            <div class="card-header">
                                <table style=" table-layout: fixed ; width: 100% ;"
                                       class="table table-sm table-hover table-bordered table-responsive-sm">
                                    <td style=" width: 33% ;" align="left">
                                        <a href="#"
                                           data-toggle="collapse"
                                           data-target="#collapse_{{$i}}_{{$j}}">
                                            {{$so->order_number}}
                                        </a>
                                    </td>
                                    <td style=" width: 33% ;" align="left">
                                        {{$so->customer_name}}</td>
                                    <td style=" width: 33% ;" align="right">
                                        ${{number_format($so->sum_volume,2)}}</td>
                                    <td style=" width: 33% ;" align="right">
                                        ${{number_format($so->sum_commission,2)}}</td>
                                    <td style=" width: 33% ;" align="right">
                                        Invoiced: {{$so->invoice_date}}</td>
                                </table>

                            </div>
                            <div class="card-body collapse" data-parent="#child_{{$i}}"
                                 id="collapse_{{$i}}_{{$j}}">
                                <table class="table table-bordered table-hover table-sm table-responsive-sm">
                                    <thead>
                                    <tr>
                                        <th class="text-xl-left">Name</th>
                                        <th class="text-xl-right">Cost</th>
                                        <th class="text-xl-right">Price</th>
                                        <th class="text-xl-right">Quantity</th>
                                        <th class="text-xl-right">Margin</th>
                                        <th class="text-xl-right">Total</th>
                                        <th class="text-xl-right">Commission</th>
                                    </tr>
                                    </thead>

                                    @php
                                        $paid_line = $lines->where('order_number','like',$so->order_number);
                                    @endphp
                                    <tbody>
                                    @foreach($paid_line as $sl)
                                        <tr>
                                            <td class="text-xl-left">{{ $sl->name }}</td>
                                            <td class="text-xl-right">{{$sl->cost}}</td>
                                            <td class="text-xl-right">{{$sl->unit_price}}</td>
                                            <td class="text-xl-right">{{$sl->quantity}}</td>
                                            <td class="text-xl-right">{{$sl->margin}}</td>
                                            <td class="text-xl-right">{{$sl->amount}}</td>
                                            <td class="text-xl-right">{{$sl->commission}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @php
                            $j++;
                        @endphp
                    @endforeach
                    @php
                        //           dd($so);
                    @endphp
                </div>
            </div>
        </div>
        @php
            $i++;
        @endphp
    @endforeach
</div>

