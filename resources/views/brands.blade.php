@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
{{--
   @php echo \Lava::render('DonutChart', 'Brand', 'div12') @endphp
--}}

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
                        <th>Brand</th>
                        <th class="text-xl-right">Sales $</th>
                        <th class="text-xl-right">Comm $</th>
                        <th class="text-xl-right">Avg. Margin</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($brands as $sl)
                        <tr>
                            <td class="text-xl-left">{{$sl->brand_name}}</td>
                            <td class="text-xl-right">{{number_format($sl->brand_volume,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->brand_commission,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->brand_margin,2)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="makebold">
                        <td>Totals</td>
                        <td class="text-xl-right">{{number_format($brandData['salesTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($brandData['commissionTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($brandData['avgMarginTotal'],2)}}</td>
                    </tr>
                    </tfoot>
                </table>

            </div>
            <script>
                $(function() {
                    var month = "<?= $brandData['month'] ?>";
                    $('#xaccounts').DataTable({
                        processing: true,
                        serverSide: true,
                        type: "POST",
                        ajax: '{!! url('brand_ajax/' .$brandData['month']) !!}',
                        columns: [
                            { data: 'brand_name', name: 'brand_name' , className: 'text-xl-left'},
                            { data: 'brand_volume', name: 'brand_volume', className: 'text-xl-right' },
                            { data: 'brand_margin', name: 'brand_margin' , className: 'text-xl-right'},
                            { data: 'brand_commission', name: 'brand_commission', className: 'text-xl-right' },
                        ]
                    });
                });
            </script>

{{--
            <script>
                $(function() {
                    $('#xtable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: '{!! url('index') !!}',
                        columns: [
                            { data: 'id', name: 'id' },
                            { data: 'name', name: 'name' },
                            { data: 'email', name: 'email' }
                        ]
                    });
                });
            </script>
--}}

        </div>
        </div>
    </div>
@endsection

