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
                    <div class="card-header text-center"><h3>Commission Admin</h3>
                        <form method="post" action="{{action('CommissionPaidController@createSavedCommission')}}">
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
                                    <select class="form-control" name="months">
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
                                    <button type="submit" name="display" value="display"
                                            class="btn btn-primary">
                                        Save commissions
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        <h4 class="text-center">Saved Commissions</h4><br/>
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Description</th>
                                <th scope="col">Created by</th>
                                <th scope="col">Created On</th>
                                <th scope="col">Comment</th>
                                <th scope="col">Month</th>
                                <th scope="col">Year</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($commissions as $commission)
                                <tr>
                                    <td>{{ $commission->id }}</td>
                                    <td>{{ $commission->description }}</td>
                                    <td>{{ $commission->created_by }}</td>
                                    <td>{{ $commission->created_at }}</td>
                                    <td>{{ $commission->comment }}</td>
                                    <td>{{ $commission->month }}</td>
                                    <td>{{ $commission->year }}</td>
                                    <td>{{ $commission->is_commissions_paid ? "Paid" : "Open"}}</td>
                                    <td><a class="btn btn-primary"
                                           href="{{route('saved_commission',['id' => $commission->id])}}"
                                           role="button">
                                            View
                                        </a>
                                    </td>
                                    <td><a class="btn btn-primary"
                                           href="{{route('edit_saved_commission',['id' => $commission->id])}}"
                                           role="button">
                                            Edit
                                        </a>
                                    </td>
                                    <td><a class="btn btn-primary"
                                           href="{{route('pay_saved_commission',['id' => $commission->id])}}"
                                           role="button">
                                            Mark as paid
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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
