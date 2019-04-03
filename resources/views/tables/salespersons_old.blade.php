@extends('layouts.app')
@section('title', 'Salespersons Report')
@section('content')
    <div class="container">
        <div class="card">
            <div class='card-header'>
                <h5>Total Sales Per Customer </h5>
            </div>
            <div class="card card-body">
                <div id="div12">
                </div>
                <table id="salesPersons" class="table table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th>SalesPerson</th>
                        <th class="text-xl-center">Sales $</th>
                        <th class="text-xl-center">Avg. Margin</th>
                        <th class="text-xl-center">Commission $</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($salespersons as $salesperson)
                        <tr><td>
                                {{$salesperson->salesperson_name}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="makebold">
                        <td>Totals</td>
                        <td class="text-xl-right">{{number_format($customerData['salesTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($customerData['avgMarginTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($customerData['commissionTotal'],2)}}</td>
                    </tr>
                    </tfoot>

                </table>
                <script>
                    $(function () {
                        $('#salesPersons').DataTable({
                            processing: true,
                            serverSide: false,
                            paging: false,
                            order: [[0, "desc"]],
                            rowGroup:{dataSrc: ['salesperson_name','summary_year_month' ]},
                            //columnDefs: [ {
                           //     targets: [ 'summary_year_month' ]
                          //     visible: false
                         //   } ],
                            ajax: '{!! secure_url('sp_ajax') !!}',
                            columns: [
                                {data: 'salesperson_name', name: 'salesperson_name', className: 'text-xl-left'},
                                {data: 'sp_volume', name: 'sp_volume', className: 'text-xl-right'},
                                {data: 'sp_margin', name: 'sp_margin', className: 'text-xl-right'},
                                {data: 'sp_commission', name: 'sp_commission', className: 'text-xl-right'},
                            ]
                        });
                    });
                </script>

            </div>
        </div>
    </div>
@endsection

