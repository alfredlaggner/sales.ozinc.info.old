@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="form-group col-md-4">
                            <form method="post"
                                  action="{{route('invoice_notes.create')}}">
                                @csrf

                                <label for="name">New Note:</label>
                                <input class="form-control" name="name">
                                <label for="name">Amount to Collect:</label>
                                <input class="form-control" name="amount_to_collect">
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="form-group col-md-4">
                                        <button type="submit" name="display" value="display"
                                                class="btn btn-primary">
                                            Add Note
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
