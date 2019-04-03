@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-right">
                        @can('isAdmin')
                            <h4 class="text-center">Administrator Statistics</h4><br/>
                        @elsecan('isSalesPerson')
                            <h4 class="text-center">Sales Statistics for {{$salesperson_name}}</h4><br/>
                        @endcan
                    </div>

                    <div class="card-body">


                        <div class="accordion" id="accordionExample">
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
                                        <form method="post" action="{{action('DevelopController@calcPerSalesPerson')}}">
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
                                <div class="card-header" id="headingTwo">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                                data-target="#collapseTwo" aria-expanded="false"
                                                aria-controls="collapseTwo">
                                            <h6 class="text-center">Sales per Region</h6>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                     data-parent="#accordionExample">
                                    <div class="card-body">
                                        <form method="post" action="{{action('DevelopController@calcRegions')}}">
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
                                        <form method="post" action="{{action('DevelopController@calcBrandsPerMonth')}}">
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
