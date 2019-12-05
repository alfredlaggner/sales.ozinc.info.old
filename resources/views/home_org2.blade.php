@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center"><h3>Sales Statistics</h3></div>

                    <div class="card-body">
                        @can('isAdmin')
                            <h4 class="text-center">Sales per Salesperson</h4><br/>
                        @elsecan('isSalesPerson')
                            <h4 class="text-center">Sales Statistics for {{$salesperson_name}}</h4><br/>
                        @endcan

                        <form method="post" action="{{action('CommissionController@calcCommissions')}}">
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
                                    <label for="salesperson">Month:</label>
                                    <select class="form-control" name="month">
                                        @foreach($months as $sp)
                                            @if ($sp->month_id == $data['month'])
                                                <option value="{{$sp->month_id}}" selected>{{$sp->name}} </option>
                                            @else
                                                <option value="{{$sp->month_id}}">{{$sp->name}} </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <label for="salesperson">Year:</label>
                                    <input class="form-control" name="year" readonly value="{{$year}}">
                                    </input>
                                </div>
                            </div>
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
                            @can('isAdmin')

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
                                                  action="{{action('DevelopController@calcPerSalesPerson')}}">
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
                                                                    <option value="{{$sp->month_id}}">{{$sp->name}} </option>
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
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingThree">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                            data-target="#collapseThree" aria-expanded="false"
                                            aria-controls="collapseThree">
                                        <h6>Customers by Month</h6>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                 data-parent="#accordionExample">
                                <div class="card-body">
                                    <form method="post"
                                          action="{{action('DevelopController@calcCustomersPerMonth')}}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="form-group col-md-4">
                                                <label for="salesperson">Month:</label>

                                                <select class="form-control" name="month">
                                                    @foreach($months as $sp)
                                                        @if ($sp->month_id == $data['month'])
                                                            <option value="{{$sp->month_id}}"
                                                                    selected>{{$sp->name}} </option>
                                                        @else
                                                            <option value="{{$sp->month_id}}">{{$sp->name}} </option>
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
                            <div class="card-header" id="headingFour">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                            data-target="#collapseFour" aria-expanded="false"
                                            aria-controls="collapseFour">
                                        <h6>Customers by Brand</h6>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                                 data-parent="#accordionExample">
                                <div class="card-body">
                                    <form method="post"
                                          action="{{action('DevelopController@calcBrandsPerMonth')}}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="form-group col-md-4">
                                                <label for="salesperson">Month:</label>
                                                <select class="form-control" name="month">
                                                    @foreach($months as $sp)
                                                        @if ($sp->month_id == $data['month'])
                                                            <option value="{{$sp->month_id}}"
                                                                    selected>{{$sp->name}} </option>
                                                        @else
                                                            <option value="{{$sp->month_id}}">{{$sp->name}} </option>
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
                            <div class="card-header" id="headingTwo">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button"
                                            data-toggle="collapse"
                                            data-target="#collapseTwo" aria-expanded="false"
                                            aria-controls="collapseTwo">
                                        <h6 class="text-center">Sales per Region</h6>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                 data-parent="#accordionExample">
                                <div class="card-body">
                                    <form method="post"
                                          action="{{action('DevelopController@calcRegions')}}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="form-group col-md-4">
                                                <label for="region">Region:</label>
                                                <select class="form-control" name="region">
                                                    <option value="0" selected>All</option>
                                                    <option value="N">North</option>
                                                    <option value="S">South</option>
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
                            <div class="card-header" id="headingFive">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button"
                                            data-toggle="collapse"
                                            data-target="#collapseFive" aria-expanded="false"
                                            aria-controls="collapseFive">
                                        <h6>Brand Totals</h6>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseFive" class="collapse" aria-labelledby="headingFive"
                                 data-parent="#accordionExample">
                                <div class="card-body">
                                    <form method="post"
                                          action="{{action('DevelopController@calcPerBrand')}}">
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
                                                            <option value="{{$sp->month_id}}">{{$sp->name}} </option>
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
                            <div class="card-header" id="headingSix">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button"
                                            data-toggle="collapse"
                                            data-target="#collapseSix" aria-expanded="false"
                                            aria-controls="collapseSix">
                                        <h6>Customer Totals</h6>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseSix" class="collapse" aria-labelledby="headingSix"
                                 data-parent="#accordionExample">
                                <div class="card-body">
                                    <form method="post"
                                          action="{{action('DevelopController@calcPerCustomer')}}">
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
                                                            <option value="{{$sp->month_id}}">{{$sp->name}} </option>
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
                            <div class="card-header" id="headingSeven">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button"
                                            data-toggle="collapse"
                                            data-target="#collapseSeven" aria-expanded="false"
                                            aria-controls="collapseSeven">
                                        <h6>Product Totals</h6>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven"
                                 data-parent="#accordionExample">
                                <div class="card-body">
                                    <form method="post"
                                          action="{{action('DevelopController@calcPerProduct')}}">
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
                                                            <option value="{{$sp->month_id}}">{{$sp->name}} </option>
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
                            <div class="card-header" id="headingEight">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button"
                                            data-toggle="collapse"
                                            data-target="#collapseEight" aria-expanded="false"
                                            aria-controls="collapseEight">
                                        <h6>Detail Totals</h6>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseEight" class="collapse" aria-labelledby="headingEight"
                                 data-parent="#accordionExample">
                                <div class="card-body">
                                    <form method="post"
                                          action="{{action('DevelopController@totalDetails')}}">
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
                                                            <option value="{{$sp->month_id}}">{{$sp->name}} </option>
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
                            <div class="card-header" id="headingNine">
                                <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button"
                                            data-toggle="collapse"
                                            data-target="#collapseNine" aria-expanded="false"
                                            aria-controls="collapseNine">
                                        <h6>Salesorder Totals</h6>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseNine" class="collapse" aria-labelledby="headingNine"
                                 data-parent="#accordionExample">
                                <div class="card-body">
                                    <form method="post"
                                          action="{{action('DevelopController@totalSalesorders')}}">
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
                                                            <option value="{{$sp->month_id}}">{{$sp->name}} </option>
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
                                            {{--                                           <div class="row">

                                                                                           <div class="col-md-4"></div>
                                                                                           <div class="form-check  col-md-4">
                                                                                               <input class="form-check-input" type="checkbox"
                                                                                                      name="is_expanded" id="is_expanded" checked>
                                                                                               <label class="form-check-label" for="defaultCheck1">
                                                                                                   View table expanded?
                                                                                               </label>
                                                                                           </div>
                                                                                       </div>
                                                                                       <div class="row">
                                                                                           <div class="col-md-4"></div>
                                                                                           <div class="form-check  col-md-4">
                                                                                               <input class="form-check-input" type="checkbox"
                                                                                                      name="is_grouped_by_reps" id="is_grouped_by_reps">
                                                                                               <label class="form-check-label" for="defaultCheck1">
                                                                                                   Grouped by reps?
                                                                                               </label>
                                                                                           </div>
                                                                                       </div>
                                                                                       <div class="row">
                                                                                           <div class="col-md-4"></div>
                                                                                           <div class="form-check  col-md-4">
                                                                                               <input class="form-check-input" type="checkbox"
                                                                                                      name="is_print" id="is_print">
                                                                                               <label class="form-check-label" for="defaultCheck1">
                                                                                                   Set for Excel print? (No grouping, expanded)
                                                                                               </label>
                                                                                           </div>
                                                                                       </div>
                                           --}}
                                            @can('isAdmin')
{{--
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="form-group col-md-4">
                                                        <label for="rep_id">Select rep:</label>
                                                        <select class="form-control" name="rep_id">
                                                            <option value="0">All</option>
                                                            @foreach($salesperson as $sp)
                                                                @if ($sp->sales_person_id == $data['salesperson_id'])
                                                                    <option value="{{$sp->sales_person_id}}"
                                                                            selected>{{$sp->name}}
                                                                    </option>
                                                                @else
                                                                    <option value="{{$sp->sales_person_id}}">{{$sp->name}}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
--}}

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

                        @canany(['isAdmin'])
                            <div class="card">
                                <div class="card-header" id="headingEleven">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button"
                                                data-toggle="collapse"
                                                data-target="#collapseEleven" aria-expanded="false"
                                                aria-controls="collapseEleven">
                                            <h6>Product Margins and Commissions</h6>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseEleven" class="collapse" aria-labelledby="headingEleven"
                                     data-parent="#accordionExample">
                                    <div class="card-body">
                                        <form method="get"
                                              action="{{action('DevelopController@all_products')}}">
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
                        @endcanany
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
    </div>
@endsection
