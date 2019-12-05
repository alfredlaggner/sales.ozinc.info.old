<style>
    table, th, td {
        border: 1px solid darkgrey;
        border-collapse: collapse;
    }
    th, td {
        padding: 5px;
        font-size: 12px;
        width: 100%;
    }
</style>
<img src="oz_letterhead.jpg" alt="Italian Trulli" style="width:97px;height:80px;">
<p>Dear {{$customer_name}},</p>
<p>This is a reminder that your account is currently past due. </p>
<p>Please be aware, if you've made a recent payment that is not reflected below, our AR processing period is 3-4
    business days.</p>
<p>Otherwise, if you have any questions or concerns, please call (831) 428-8013, and request ext. 108 for Alexander, regarding Oz AR Department.</p>
<p>Thank you.</p>

<h5>{{$customer_name}} Account Statement as of {{date('Y-m-d')}}</h5>
<table style="table-layout: fixed; width: 100%" class="table table-bordered">
    <thead>
    <tr>
        <th class="text-xl-left">Date</th>
        <th class="text-xl-left">Due Date</th>
        <th class="text-xl-left" style="width: 130%;">Name</th>
        <th class="text-xl-left">Sales Order</th>
        <th class="text-xl-right">Debit $</th>
        <th class="text-xl-right">Credit $</th>
        <th class="text-xl-left">Paid On</th>
        <th class="text-xl-right">Due $</th>
    </tr>
    </thead>
    <tbody>
    @foreach($ledgers as $sl)
        @php
            //  dd($ledgers);
        @endphp
        <tr>
            <td class="text-xl-left">{{$sl['date']}}</td>
            <td class="text-xl-left">{{$sl['due']}}</td>
            <td class="text-xl-left">{{$sl['name']}}</td>
            <td class="text-xl-left">{{$sl['sales_order']}}</td>
            <td align="right">{{$sl['amount'] ? number_format($sl['amount'],2) : ''}}</td>
            <td align="right"">{{$sl['payment_amount'] ? number_format($sl['payment_amount'],2) : ''}}</td>
            <td class="text-xl-right">{{$sl['payment_date'] != '0000-00-00' ? $sl['payment_date'] : ''}}</td>
            <td align="right"><b>{{$sl['residual'] ? number_format($sl['residual'],2) : ''}}</b></td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr class="makebold">
        <td>Totals</td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right">{{number_format($total_amount,2)}}</td>
        <td align="right">{{number_format($total_payment,2)}}</td>
        <td></td>
        <td align="right">{{number_format($total_residual,2)}}</td>
    </tr>
    </tfoot>
</table>
