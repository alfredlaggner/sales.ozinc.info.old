@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
{{--
    @php echo \Lava::render('DonutChart', 'Customer', 'div12') @endphp
--}}

    <div class="container">
        <div class="card">
            <div class='card-header'>
                <h5>Customer Sales for {{$customerData['dMonth']}} </h5>
            </div>
            <div class="card card-body">
                <div id="div12">
                </div>
                <table id="xaccounts" class="table table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th>Customer</th>
                        <th class="text-xl-center">Sales $</th>
                        <th class="text-xl-center">Avg. Margin</th>
                        <th class="text-xl-center">Commission $</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr class="makebold">
                        <td>Totals</td>
                        <td class="text-xl-right">{{number_format($customerData['salesTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($customerData['avgMarginTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($customerData['commissionTotal'],2)}}</td>
                    </tr>
                    </tfoot>

                </table>
            </div>
            <script>
                $(function () {
                    $('#xaccounts').DataTable({
                        processing: true,
                        serverSide: false,
                        paging: false,
                        scroller: true,
                        scrollY: 400,
                        dom: 'Blrtip',
                        buttons: [
                            'copyHtml5',
                            'excelHtml5',
                            'csvHtml5',
                            'pdfHtml5'
                        ],
                        order: [[1, "desc"]],
                        ajax: '{!! secure_url('customer_ajax/' .$customerData['month']) !!}',
                        columns: [
                            {data: 'customer_name', name: 'customer_name', className: 'text-xl-left'},
                            {data: 'customer_volume', name: 'customer_volume', className: 'text-xl-right'},
                            {data: 'customer_margin', name: 'customer_margin', className: 'text-xl-right'},
                            {data: 'customer_commission', name: 'customer_commission', className: 'text-xl-right'},
                        ]
                    });
                });
            </script>

        </div>
    </div>
@endsection

