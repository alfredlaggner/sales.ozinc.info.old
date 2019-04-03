@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
    <div class="card-header">{{$title}}</div>
    <div class="card-body">
            @php echo \Lava::render('GeoChart', 'Dispensary', 'pop_div') @endphp
            <div id="pop_div"></div>
        </div>

@endsection

