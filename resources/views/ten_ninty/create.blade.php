@extends('layouts.app')

@section('content')
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.2.3/dist/js/tabulator.min.js"></script>
    <link href="{{ URL::asset('css/dist/css/bootstrap/tabulator_bootstrap4.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="http://oss.sheetjs.com/js-xlsx/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.0.5/jspdf.plugin.autotable.js"></script>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>1090 Commission Admin</h3>
                        <h6>For {{$month}} {{$year}}</h6>
                    </div>
                    <div class="card-body">
                        <form method="post"
                              action="{{route('ten_ninty_create')}}">
                            @method('PUT')
                            @csrf
                            <input hidden name="rep_count" value="{{$rep_count}}">
                            <input hidden name="month" value="{{$month}}">
                            <input hidden name="year" value="{{$year}}">
                            @foreach($reps as $rep)
                                <div class="form-group row">

                                    @php
                                        $goal_val = 100000;
                                        if($rep->is_ten_ninety) $goal_val = 150000;
                                    @endphp
                                    <input hidden name="rep_name_{{$rep->sales_person_id}}" value="{{$rep->name}}">
                                    <input hidden name="rep_id_{{$rep->sales_person_id}}"
                                           value="{{$rep->sales_person_id}}">
                                    <label class="col-sm-4 col-form-label" for="expected_volume">{{$rep->name}}
                                        : </label>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="expected_volume_{{$rep->sales_person_id}}"
                                               value="{{$goal_val}}">
                                    </div>
                                </div>
                            @endforeach
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <button type="submit" name="display" value="display"
                                            class="btn btn-primary">
                                        Create monthly set
                                    </button>
                                </div>
                            </div>
                        </form>
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
