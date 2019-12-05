@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
    <div class="container">
        <div class="card">
            <div class='card-header'>
                <h5>Commissions for {{$month}} {{$year}} </h5>
            </div>
            <div class="card card-body">
                <div id="div12">
                </div>
                <form method="get">
                    @csrf

                    <div class="form-row align-items-center mb-2">
                        <div class="col-auto">
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-primary btn-small" href="{{route('export_commissions')}}">
                                Export Commissions
                            </a>
                        </div>
                    </div>
                </form>

                <table id="accounts" class="table table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th class="text-xl-left">Rep Name</th>
                        <th class="text-xl-right">Goal $</th>
                        <th class="text-xl-right">Sales $</th>
                        <th class="text-xl-right">Collected $</th>
                        <th class="text-xl-right">Percent</th>
                        <th class="text-xl-right">Commission</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ten_ninety_goals as $sl)
                        <tr>
                            <td class="text-xl-left">{{$sl->rep_name}}</td>
                            <td class="text-xl-right">{{$sl->goal}}</td>
                            <td class="text-xl-right">{{number_format($sl->volume,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->volume_collected,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->percent,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->commission,2)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
{{--
                    <tr class="makebold">
                        <td>Totals</td>
                        <td class="text-xl-right">{{number_format($brandData['salesTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($brandData['commissionTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($brandData['avgMarginTotal'],2)}}</td>
                    </tr>
--}}
                    </tfoot>
                </table>

            </div>
            <script>
            </script>

            <script>
            </script>

        </div>
        </div>
    </div>
@endsection

