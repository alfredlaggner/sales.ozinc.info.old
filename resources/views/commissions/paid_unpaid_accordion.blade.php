@extends('layouts.app')
@section('title', 'Commissions Report')
@php
    $i=1;
@endphp
@section('content')
    <div class="container">
        <h3>{{$name}}</h3>
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item  dropdown show">
                <a class="nav-link active dropdown-toggle" id="pills-profile-tab" data-toggle="dropdown" role="button"
                   aria-haspopup="true" aria-expanded="false"
                   href="#">Paid Commissions</a>
                <div class="dropdown-menu show" role="menu">
                    @for ($k =0; $k < count($months); $k++)
                        @php
                            $paid_month=$months[$k]['month_name'];
                            $paid_date=str_replace("_", "-", substr($months[$k]['name'],14,10));

                        @endphp
                        <a class="dropdown-item"
                           href="{{ route ('paid_out',['month' => $months[$k]['name'],'rep' => $months[$k]['rep'],'description' => $paid_date])}}"><b>{{$paid_month}}</b>
                            Paid on: {{$paid_date}}</a>
                    @endfor
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab"
                   aria-controls="pills-profile" aria-selected="true">Uncollected Invoices</a>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                @include('commissions.paid',['status' => 'unpaid'])
            </div>
        </div>
    </div>
@endsection
