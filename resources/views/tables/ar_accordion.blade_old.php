@extends('layouts.app')
@section('title', 'Commissions Report')
@php
    $i=1;
@endphp
@section('content')
    <div class="container">

        <div class="accordion" id="accordionExample">
            @foreach ($ars_totals as $ars_total)

                <div class="card">
                    <div class="card-header" id="headingOne{{$i}}">
                        <p class="mb-0">
                        <table class="table table-bordered table-sm table-responsive-sm">
                            <thead>
                            <tr>
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
                                <th style="font-weight: normal" class="text-xl-center">Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-xl-left"><b>{{substr($ars_total->customer,0,30)}}</b></td>
                                <td class="text-xl-right">{{ $ars_total->range0 ? number_format($ars_total->range0,0) : ''}}</td>
                                <td class="text-xl-right">{{ $ars_total->range1 ? number_format($ars_total->range1,0) : ''}}</td>
                                <td class="text-xl-right">{{ $ars_total->range2 ? number_format($ars_total->range2,0) : ''}}</td>
                                <td class="text-xl-right">{{ $ars_total->range3 ? number_format($ars_total->range3,0) : ''}}</td>
                                <td class="text-xl-right">{{ $ars_total->range4 ? number_format($ars_total->range4,0) : ''}}</td>
                                <td class="text-xl-right">{{ $ars_total->range5 ? number_format($ars_total->range5,0) : ''}}</td>
                                <td class="text-xl-right">{{ $ars_total->range6 ? number_format($ars_total->range6,0) : ''}}</td>
                                <td class="text-xl-right">{{ $ars_total->range7 ? number_format($ars_total->range7,0) : ''}}</td>
                                <td class="text-xl-right">{{ $ars_total->customer_total ? number_format($ars_total->customer_total,0) : ''}}</td>
                                <td class="text-xl-right">
                                    <button class="btn btn-sm btn-success" type="button" data-toggle="collapse"
                                            data-target="#collapseOne{{$i}}"
                                            aria-expanded="true" aria-controls="collapseOne{{$i}}">
                                        Orders
                                    </button>
                                    <button class="btn btn-sm btn-success" type="button" data-toggle="collapse"
                                            data-target="#collapseNote{{$i}}"
                                            aria-expanded="true" aria-controls="collapseNote{{$i}}">
                                        Notes
                                    </button>
                                    <button class="btn btn-sm btn-success" type="button" data-toggle="collapse"
                                            data-target="#collapseCollect{{$i}}"
                                            aria-expanded="true" aria-controls="collapseCollect{{$i}}">
                                        Collect
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>


                        </p>
                    </div>

                    <div id="collapseOne{{$i}}" class="collapse" aria-labelledby="headingOne{{$i}}"
                         data-parent="#accordionExample">
                        <div class="card-body">
                            <table class="table table-bordered table-hover table-sm table-responsive-sm">
                                <thead>
                                <tr>
                                    <th class="text-xl-left">Order</th>
                                    <th class="text-xl-right">Not Today</th>
                                    <th class="text-xl-right">7 days</th>
                                    <th class="text-xl-right">14 days</th>
                                    <th class="text-xl-right">30 days</th>
                                    <th class="text-xl-right">60 days</th>
                                    <th class="text-xl-right">90 days</th>
                                    <th class="text-xl-right">120 days</th>
                                    <th class="text-xl-right">Older</th>
                                    <th class="text-xl-right">Residual</th>

                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $ols = $ars->where('customer_id','=',$ars_total->customer_id);
                                @endphp
                                @foreach ($ols as $sl)
                                    <tr>
                                        <td>{{substr($sl->sales_order,0,10)}}</td>
                                        <td class="text-xl-right">{{ $sl->range0 ? number_format($sl->range0,0) : ''}}</td>
                                        <td class="text-xl-right">{{$sl->range1 ? number_format($sl->range1,0) : ''}}</td>
                                        <td class="text-xl-right">{{$sl->range2 ? number_format($sl->range2,0) : ''}}</td>
                                        <td class="text-xl-right">{{$sl->range3 ? number_format($sl->range3,0) : ''}}</td>
                                        <td class="text-xl-right">{{$sl->range4 ? number_format($sl->range4,0) : ''}}</td>
                                        <td class="text-xl-right">{{$sl->range5 ? number_format($sl->range5,0) : ''}}</td>
                                        <td class="text-xl-right">{{$sl->range6 ? number_format($sl->range6,0) : ''}}</td>
                                        <td class="text-xl-right">{{$sl->range7 ? number_format($sl->range7,0) : ''}}</td>
                                        <td class="text-xl-right">{{$sl->residual ? number_format($sl->residual,0) : ''}}</td>
                                    </tr>



                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="collapseNote{{$i}}" class="collapse" aria-labelledby="headingNote{{$i}}"
                         data-parent="#accordionExample">
                        <div class="card-body">
                            <table class="table table-bordered table-hover table-sm table-responsive-sm">
                                <thead>
                                <tr>
                                    <th class="text-xl-center">Notes</th>
                                    <th class="text-xl-center">On</th>
                                    <th class="text-xl-center">By</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $customer_id = $ars_total->customer_id;
                                    echo $customer_id;
                                        if($customer_id = 3122){
                                            $notes = $notes->where('customer_id','=',$customer_id);
                                          // dd($notes->toArray());
                                        }
                                         $collects = $amt_collects->where('customer_id','=',$ars_total->customer_id);
                                @endphp

                                @if ($notes)
                                    @php
                                        if($customer_id = 3122){
                                         dd($notes->toArray());
                                        }
                                    @endphp
                                    @foreach ($notes as $note)
                                        <tr>
                                            <td>{{$note->note}}</td>
                                            <td> {{$note->updated_at}}</td>
                                            <td> {{$note->note_by}}</td>
                                        </tr>

                                    @endforeach

                                @endif
                                </tbody>
                            </table>
                            <form method="post" action="{{action('InvoiceNoteController@store')}}">
                                @csrf
                                <input hidden name="customer_id" value="{{$customer_id}}">
                                {{  Form::hidden('url',URL::previous())  }}

                                <div class="form-group">
                                    <label for="note"><b>Enter new note:</b></label>

                                    <input class="form-control" name="note" value="">
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="notes" value="notes"
                                            class="btn btn-success">
                                        Add note
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                    <div id="collapseCollect{{$i}}" class="collapse" aria-labelledby="headingCollect{{$i}}"
                         data-parent="#accordionExample">
                        <div class="card-body">
                            <table class="table table-bordered table-hover table-sm table-responsive-sm">
                                <thead>
                                <tr>
                                    <th class="text-xl-center">Date</th>
                                    <th class="text-xl-center">To Collect</th>
                                    <th class="text-xl-center">Collected</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    //  dd($collects->toArray());
                                @endphp
                                @foreach ($collects as $collect)
                                    <tr>
                                        <td> {{$collect->updated_at}}</td>
                                        <td>{{$collect->amount_to_collect}}</td>
                                        <td> {{$collect->amt_collected}}</td>
                                    </tr>
                                    @php
                                        $customer_id = $collect->customer_id;
                                    @endphp

                                @endforeach
                                </tbody>
                            </table>
                            <form method="post" action="{{action('InvoiceNoteController@store')}}">
                                @csrf
                                <input hidden name="customer_id" value="{{$customer_id}}">
                                {{  Form::hidden('url',URL::previous())  }}

                                <label for="amount_to_collect"><b>Enter new amount to collect:</b></label>
                                <input class="form-control" id="amount_to_collect" name="amount_to_collect">
                                <div class="form-group">
                                    <button type="submit" name="collect" value="collect"
                                            class="btn btn-success">
                                        Add amount
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>
                @php
                    $i++;
                @endphp
            @endforeach
        </div>
    </div>
@endsection