@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    @can('isAdmin')
                        <div class="card-header text-center"><h3>Accounting Apps</h3></div>
                    @elsecan('isSalesPerson')
                        <div class="card-header text-center"><h3>Sales Statistics</h3></div>
                    @endcan

                    <div class="card-body">
                        @can('isAdmin')
                            <h4 class="text-center">Sales per Salesperson</h4><br/>
                        @elsecan('isSalesPerson')
                            <h4 class="text-center">Sales Statistics for {{$salesperson_name}}</h4><br/>
                        @endcan

                        <form method="post"
                              action="{{route('unpaid_paid')}}">
                            @csrf
                            @can('isAdmin')
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="form-group col-md-4">
                                        <label for="salesperson">Name:</label>
                                        <select class="form-control" name="salesperson_id">
                                            @foreach($salesperson as $sp)
                                                @if ($sp->sales_person_id == $data['salesperson_id'])
                                                    <option value="{{$sp->sales_person_id}}" selected>{{$sp->name}}
                                                    </option>
                                                @else
                                                    <option value="{{$sp->sales_person_id}}">{{$sp->name}}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @elsecan('isSalesPerson')
                                <input name="salesperson_id" type="hidden" value="{{$salesperson_id}}">
                            @endcan
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <button type="submit" name="display" value="display" class="btn btn-primary">
                                        Ready set go
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="accordion" id="accordionExample">
                            @canany(['isAdmin', 'isSalesPerson'])
                                <div class="card">
                                    <div class="card-header" id="headingTen">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link collapsed" type="button"
                                                    data-toggle="collapse"
                                                    data-target="#collapseTen" aria-expanded="false"
                                                    aria-controls="collapseTen">
                                                <h6>Aged Receivables</h6>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseTen" class="collapse" aria-labelledby="headingTen"
                                         data-parent="#accordionExample">
                                        <div class="card-body">
                                            <form method="get"
                                                  action="{{action('ArController@new_aged_receivables')}}">
                                                @csrf
                                                @can('isAdmin')

                                                @elsecan('isSalesPerson')
                                                    <input name="rep_id" type="hidden" value="{{$salesperson_id}}">
                                                @endcan

                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="form-group col-md-4">
                                                        <button type="submit" name="display" value="display"
                                                                class="btn btn-primary">
                                                            Ready set go
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endcanany

                            @can('isAdmin')

                                    <div class="card">
                                        <div class="card-header" id="headingCustomerStatement">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link collapsed" type="button"
                                                        data-toggle="collapse"
                                                        data-target="#collapseCustomerStatement" aria-expanded="false"
                                                        aria-controls="collapseCustomerStatement">
                                                    <h6>Customer Statements</h6>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseCustomerStatement" class="collapse" aria-labelledby="headingCustomerStatement"
                                             data-parent="#accordionExample">
                                            <div class="card-body">
                                                <form method="post"
                                                      action="{{route('notify_customer')}}">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-4"></div>
                                                        <div class="form-group col-md-4">
                                                            <button type="submit" name="display" value="display"
                                                                    class="btn btn-primary">
                                                                Ready set go
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="headingSixPlus">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link collapsed" type="button"
                                                        data-toggle="collapse"
                                                        data-target="#collapseSixPlus" aria-expanded="false"
                                                        aria-controls="collapseSixPlus">
                                                    <h6>Margin Commissions</h6>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseSixPlus" class="collapse" aria-labelledby="headingSixPlus"
                                             data-parent="#accordionExample">
                                            <div class="card-body">
                                                <form method="post"
                                                      action="{{action('CommissionPaidController@admin')}}">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-4"></div>
                                                        <div class="form-group col-md-4">
                                                            <button type="submit" name="display" value="display"
                                                                    class="btn btn-primary">
                                                                Ready set go
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="headingSevenPlus">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link collapsed" type="button"
                                                        data-toggle="collapse"
                                                        data-target="#collapseSevenPlus" aria-expanded="false"
                                                        aria-controls="collapseSevenPlus">
                                                    <h6>1099 Commissions</h6>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseSevenPlus" class="collapse" aria-labelledby="headingSevenPlus"
                                             data-parent="#accordionExample">
                                            <div class="card-body">
                                                <form method="post"
                                                      action="{{route('ten_ninty_main')}}">
                                                    @csrf
                                                    <input type="hidden" name="_method" value="PUT">"
                                                    <div class="row">
                                                        <div class="col-md-4"></div>
                                                        <div class="form-group col-md-4">
                                                            <button type="submit" name="display" value="display"
                                                                    class="btn btn-primary">
                                                                Ready set go
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                    <div class="card-header" id="headingOne">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link" type="button" data-toggle="collapse"
                                                    data-target="#collapseOne" aria-expanded="false"
                                                    aria-controls="collapseOne">
                                                <h6 class="text-center">Salespersons Totals</h6>
                                            </button>
                                        </h2>
                                    </div>

                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                         data-parent="#accordionExample">
                                        <div class="card-body">
                                            <form method="post"
                                                  action="{{action('NewCommissionController@viewSavedPaidCommissionsbyRep')}}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="form-group col-md-4">
                                                        <label for="months">Year:</label>
                                                        <input class="form-control" name="year" type="text"
                                                               value="{{$year}}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="form-group col-md-4">
                                                        <label for="months">Select months range:</label>
                                                        <select class="form-control" name="months[]" multiple>
                                                            @foreach($paidMonths as $sp)
                                                                @php
                                                                    $month_name = $sp->month . "-" . $sp->year;
                                                                @endphp
                                                                @if ($sp->month == $data['month'])
                                                                    <option value="{{$sp->month}}"
                                                                            selected>{{$month_name}} </option>
                                                                @else
                                                                    <option
                                                                        value="{{$sp->month}}">{{$month_name}} </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="form-group col-md-4">

                                                        <button type="submit" name="display" value="display"
                                                                class="btn btn-primary">
                                                            Ready set go
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header" id="headingEightPlus">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link collapsed" type="button"
                                                    data-toggle="collapse"
                                                    data-target="#collapseEightPlus" aria-expanded="false"
                                                    aria-controls="collapseEightPlus">
                                                <h6>Blowout Sales</h6>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseEightPlus" class="collapse" aria-labelledby="headingEightPlus"
                                         data-parent="#accordionExample">
                                        <div class="card-body">
                                            <form method="post"
                                                  action="{{action('DevelopController@cleanOutSales')}}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="form-group col-md-4">
                                                        <label for="months">Year:</label>
                                                        <input class="form-control" name="year" type="text"
                                                               value="{{$year}}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="form-group col-md-4">
                                                        <label for="months">Select months range:</label>
                                                        <select class="form-control" name="months[]" multiple>
                                                            @foreach($allMonths as $sp)
                                                                @if ($sp->month_id == $data['month'])
                                                                    <option value="{{$sp->month_id}}"
                                                                            selected>{{$sp->name}} </option>
                                                                @else
                                                                    <option
                                                                        value="{{$sp->month_id}}">{{$sp->name}} </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="form-group col-md-4">
                                                        <button type="submit" name="display" value="display"
                                                                class="btn btn-primary">
                                                            Ready set go
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <p class="text-muted text-center">&copy;
                            @php
                                $copyYear = 2018; // Set your website start date
                                $curYear = date('Y'); // Keeps the second year updated
                                echo $copyYear . (($copyYear != $curYear) ? '-' . $curYear : '');
                            @endphp
                            Oz Distribution, Inc.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
