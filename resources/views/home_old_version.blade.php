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
                                    <input  readonly class="form-control" name="year"  value="{{$year}}">
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
