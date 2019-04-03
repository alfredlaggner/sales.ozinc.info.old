@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
    <div class="container">
        <div class="card">
            <div class='card-header'>
                <h5>Southern Region: Monthly Sales, Commissions, Average Margin </h5>
            </div>

            <div class="card card-body">
                <div id="month_div12">
					<?= Lava::render('ComboChart', 'MonthsSouth', 'month_div12') ?>
                </div>
                <table id="all_south" class="table table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-xl-center">Sales $</th>
                        <th class="text-xl-center">Comm $</th>
                        <th class="text-xl-center">Avg. Margin $</th>
                        <th class="text-xl-center">SO #</th>
                    </tr>
                    </thead>
                    <tfoot>
{{--
                    @foreach($monthItemsSouth as $sl)
                        <tr>
                            <td>{{ date("F", mktime(0, 0, 0, substr($sl->summary_year_month,4), 1))   . " " . substr($sl->summary_year_month,0,4)}} </td>
                            <td class="text-xl-right">{{number_format($sl->month_sale,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->month_commission,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->month_margin,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->so_count)}}</td>
                        </tr>
                    @endforeach
--}}
                    <tr class="makebold">
                        <td>Totals</td>
                        <td class="text-xl-right">{{number_format($SouthernTotals['totalSales'],2)}}</td>
                        <td class="text-xl-right">{{number_format($SouthernTotals['totalCommission'],2)}}</td>
                        <td class="text-xl-right">{{number_format($SouthernTotals['AvMarginTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($SouthernTotals['totalSO'])}}</td>
                    </tr>

                    </tfoot>
                </table>
                <script>
                    $(function () {
                        $('#all_south').DataTable({
                            processing: true,
                            serverSide: false,
                            paging: false,
                            dom: 'Blrtip',
                            buttons: [
                                'excelHtml5',
                                'csvHtml5',
                                'pdfHtml5'
                            ],
                            order: [[1, "asc"]],
                            ajax: '{!! secure_url('ajax_region_months','S') !!}',
                            columns: [
                                {data: 'summary_year_month', name: 'summary_year_month', className: 'text-xl-left'},
                                {data: 'month_sale', name: 'month_sale', className: 'text-xl-right'},
                                {data: 'month_commission', name: 'month_commission', className: 'text-xl-right'},
                                {data: 'month_margin', name: 'month_margin', className: 'text-xl-right'},
                                {data: 'so_count', name: 'so_count', className: 'text-xl-right'},
                            ]
                        });
                    });
                </script>

            </div>
        </div>
    </div>
@endsection

