<div class="dropdown-menu">
    @for ($k =0; $k < count($months); $k++)
        @php
            $paid_month=$months[$k]['month_name'];
            $paid_date=str_replace("_", "-", substr($months[$k]['name'],14,10));

        @endphp
        <a class="dropdown-item"
           href="{{ route ('paid_out',['month' => $months[$k]['name'],'rep' => $months[$k]['rep']] )}}"><b>{{$paid_month}}</b>
            Paid on: {{$paid_date}}</a>
    @endfor
</div>


