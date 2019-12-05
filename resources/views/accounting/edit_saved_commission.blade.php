@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Accounting</h3>
                <h6>{{$saved_commission->name}}</h6>
                    <p>{{$saved_commission->created_by}}</p>
                    <p>{{$saved_commission->created_on}}</p>
                </div>

                <div class="card-body">
                    <h4 class="text-center">Edit saved commission</h4><br/>

                    <form method="post" action="{{action('CommissionPaidController@saveSavedCommission')}}">
                        @csrf
                        <input type="hidden" id="id" name="id" value="{{$saved_commission->id}}">

                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="form-group col-md-4">
                                <label for="description">Description:</label>
                                <input class="form-control" id="description" name="description" value="{{$saved_commission->description}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="form-group col-md-4">
                                <label for="comment">Description:</label>
                                <textarea class="form-control" id="comment" name="comment">{{$saved_commission->comment}}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="form-group col-md-4">
                                <button type="submit" name="display" value="display" class="btn btn-primary">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
