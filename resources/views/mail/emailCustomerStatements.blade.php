@component('mail::message')
    Dear {{$customer_name}},<br><br>
    This is a reminder that your account is currently past due.,<br><br>
    Please be aware, if you've made a recent payment that is not reflected below, our AR processing period is 3-4
        business days.,<br><br>
    Otherwise, if you have any questions or concerns, please call (831) 428-8013, and request ext. 108 for Alexander, regarding Oz AR Department.,<br><br>
    Thank you.
    @component('mail::subcopy')
        @component('mail::table')
            | Date       | Due Date         | Name  | Sales Order |  Debit $ | Credit $ | Paid On | Overdue $ |
            | :---------- |:----------|:-----------| :------ | ---------:| ---------:| ---------:| ---------:|
            @foreach($ledgers as $sl)
                | {{$sl['date']}}  | {{$sl['due']}} | {{$sl['name']}} | {{$sl['sales_order']}} | {{$sl['amount'] ? number_format($sl['amount'],2) : ''}} | {{$sl['payment_amount'] ? number_format($sl['payment_amount'],2) : ''}} | {{$sl['payment_date'] != '0000-00-00' ? $sl['payment_date'] : ''}}  | {{$sl['residual'] ? number_format($sl['residual'],2) : ''}}  |
            @endforeach
            | **Totals**| |  | | **{{number_format($total_amount,2)}}**| **{{number_format($total_payment,2)}}** | | **{{number_format($total_residual,2)}}** |
        @endcomponent
    @endcomponent
@endcomponent


