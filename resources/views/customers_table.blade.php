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
                        <th class="text-xl-center">Name</th>
                        <th class="text-xl-center">Street</th>
                        <th class="text-xl-center">City</th>
                        <th class="text-xl-center">Zip</th>
                        <th class="text-xl-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $customer)
                        @if ($customer->has('sales_lines'))
                            <tr>
                                <td class="">{{$customer->name}}</td>
                                <td class="">{{$customer->street}}</td>
                                <td class="">{{$customer->city}}</td>
                                <td class="">{{$customer->zip}}</td>
                                <td class=""><a href="{{action('DevelopController@calcOneCustomer',$customer->ext_id)}}"
                                                class="btn btn-success">Get Data</a></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

