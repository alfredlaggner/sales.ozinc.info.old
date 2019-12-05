@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
    <div class="container">
        <div class="card">
            <div class='card-header'>
                <h5>Please choose </h5>
            </div>
            <div class="card card-body">
                <div id="div12">
                </div>
                <table id="accounts" class="table table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th class="text-xl-center">Id</th>
                        <th class="text-xl-center">Name</th>
                        <th class="text-xl-center">Email</th>
                        <th class="text-xl-center">Telephone</th>
                        <th class="text-xl-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $customer)
                        @if ($customer->has('sales_lines'))
                            <tr>
                                <td class="">{{$customer->ext_id}}</td>
                                <td class="">{{$customer->name}}</td>
                                <td class="">{{$customer->email}}</td>
                                <td class="">{{$customer->phone}}</td>
                                <td class=""><a href="{{route('customer_statement',[$customer->ext_id,$customer->name])}}"
                                                class="btn btn-success">Print Statement</a></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

