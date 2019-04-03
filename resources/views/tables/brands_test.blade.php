@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
   @php echo Lava::render('DonutChart', 'Brand', 'div12') @endphp

    <div class="container">
        <div class="card">
            <div class='card-header'>
                <h5>Brand Sales for {{$brandData['dMonth']}} </h5>
            </div>
            <div class="card card-body">
                <div id="div12">
                </div>
                <table id="accounts" class="table table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th class="text-xl-center">Brand</th>
                        <th class="text-xl-center">Amount $</th>
                        <th class="text-xl-center">Avg. Margin</th>
                        <th class="text-xl-center">Comm $</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr class="makebold">
                        <td>Totals</td>
                        <td class="text-xl-right">{{number_format($brandData['salesTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($brandData['avgMarginTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($brandData['commissionTotal'],2)}}</td>
                    </tr>
                    </tfoot>

                </table>
            <script>
                $(function() {
                    $('#accounts').DataTable({
                        processing: true,
                        serverSide: false,
                        paging: false,
                        dom: 'Blrtip',
                        scroller: true,
                        scrollY: 400,
                        buttons: [
                            'excelHtml5',
                            'csvHtml5',
                            'pdfHtml5'
                        ],
                        order: [[ 1, "desc" ]],
                        ajax: '{!! secure_url('brand_ajax/' .$brandData['month']) !!}',
                        columns: [
                            { data: 'brand_name', name: 'brand_name' , className: 'text-xl-left'},
                            { data: 'brand_volume', name: 'brand_volume', className: 'text-xl-right' },
                            { data: 'brand_margin', name: 'brand_margin' , className: 'text-xl-right'},
                            { data: 'brand_commission', name: 'brand_commission', className: 'text-xl-right' },
                        ]
                    });
                });
            </script>

        </div>
        </div>
    </div>
@endsection

