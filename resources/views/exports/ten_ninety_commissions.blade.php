<div class="container">
    <div class="card">
        <div class='card-header'>
            <h5>1099 Commissions </h5>
        </div>
        <div class="card card-body">
            <div id="div12">
            </div>
            <table id="accounts" class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th style="font-weight: normal" class="text-xl-left">Rep</th>
                    <th style="font-weight: normal" class="text-xl-right">Goal</th>
                    <th style="font-weight: normal" class="text-xl-right">Sales</th>
                    <th style="font-weight: normal" class="text-xl-right">Collected</th>
                    <th style="font-weight: normal" class="text-xl-right">Percent</th>
                    <th style="font-weight: normal" class="text-xl-right">Commission</th>
                </tr>
                </thead>
                <tbody>

                @foreach($ars as $ars_total)
                    <tr>
                        <td class="text-xl-left">{{$ars_total->rep->name}}</td>
                        <td class="text-xl-left">{{$ars_total->goal}}</td>
                        <td class="text-xl-right">{{ $ars_total->volume}}</td>
                        <td class="text-xl-right">{{ $ars_total->volume_collected}}</td>
                        <td class="text-xl-right">{{ $ars_total->percent}}</td>
                        <td class="text-xl-right">{{ $ars_total->commission}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

