@extends('layouts.app')

@section('content')
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.2.3/dist/js/tabulator.min.js"></script>
    <link href="{{ URL::asset('css/dist/css/bootstrap/tabulator_bootstrap4.min.css') }}" rel="stylesheet">
    {{--
        <link href="{{ URL::asset('css/dist/css/tabulator_modern.min.css') }}" rel="stylesheet">
    --}}
    <script type="text/javascript" src="http://oss.sheetjs.com/js-xlsx/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.0.5/jspdf.plugin.autotable.js"></script>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center"><h3>Extra Commissions</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{action('TenNintyCommissionController@enter_month_schema')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <label for="year">Year:</label>
                                    <input class="form-control" name="year" value="{{$year}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <label for="month">Month:</label>
                                    <select class="form-control" name="month">
                                        @foreach($paidMonths as $sp)
                                            @if ($sp->month_id == $data['month'])
                                                <option value="{{$sp->month}}" selected>{{$sp->month}}-{{$sp->year}} </option>
                                            @else
                                                <option value="{{$sp->month}}">{{$sp->month}}-{{$sp->year}} </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button style="margin-right: 6px" type="submit" name="display" value="create" class="btn btn-primary">
                                   Create
                                </button>
                                <button  style="margin-right: 6px"  type="submit" name="display" value="edit" class="btn btn-primary">
                                    Edit
                                </button>
                                <button type="submit" name="display" value="list" class="btn btn-primary">
                                    List
                                </button>
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
@endsection
