@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
    <div class="container">
        @include('commissions.paid',['status' => 'paid_out'])
    </div>
@endsection
