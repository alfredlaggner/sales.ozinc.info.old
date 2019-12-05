@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Aged Receivables</h3>
                        <h6>Notes for {{$invoice->customer_name}}</h6>
                        <form method="post" action="{{action('InvoiceNoteController@store')}}">
                            @csrf
                            <input hidden name="sales_order" value="{{$invoice->sales_order}}">
                            <input hidden name="invoice_id" value="{{$invoice->iid}}">
                            <input hidden name="customer_id" value="{{$invoice->invoices_customer_id}}">
                            <input hidden name="residual" value="{{$residual}}">
                            {{  Form::hidden('url',URL::previous())  }}

                            <div class="form-group">
                                <input class="form-control" name="note" value="">
                            </div>
                            <div class="form-group">
                                <div class="row justify-content-md-center">
                                    <div class="col col-lg-3">
                                        <label for="amount_to_collect">Amount to Collect</label>
                                        <input class="form-control" id="amount_to_collect" name="amount_to_collect"
                                               value="{{$invoice->amount_to_collect}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="display" value="display"
                                        class="btn btn-primary">
                                    Add note
                                </button>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="return" value="return"
                                        class="btn btn-primary">
                                    Return to table
                                </button>
                            </div>

                        </form>


                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Note</th>
                                <th scope="col">By</th>
                                <th scope="col">On</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>{{$invoice->note}}</td>
                                    <td>{{$invoice->note_by}}</td>
                                    <td>{{$invoice->updated_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Open</th>
                                <th scope="col">Collect</th>
                                <th scope="col">Collected</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($amt_collects as $amt_collect)
                                <tr>
                                    <td>{{$amt_collect->updated_at}}</td>
                                    <td>{{$amt_collect->saved_residual}}</td>
                                    <td>{{$amt_collect->amount_to_collect}}</td>
                                    <td>{{$amt_collect->amt_collected}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-center">
                        <p class="text-muted text-center">&copy;
                            @php
                                $copyYear = 2018; // Set your website start date
                                $curYear = date('Y'); // Keeps the second year updated
                                echo $copyYear . (($copyYear != $curYear) ? '-' . $curYear : '');
                            @endphp
                            Oz Distribution, Inc.
                        </p>

                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
