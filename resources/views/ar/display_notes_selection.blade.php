@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h6>Choose time range for AR notes</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="form-group col-md-6">
                                <form method="post"
                                      action="{{route('do_export')}}">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <button type="submit" name="selection" value="last_week"
                                                    class="btn btn-primary">
                                                Last 7 days
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="date_from">From</label>
                                            <input type="date" class="form-control" name="date_from">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">

                                            <label for="date_to">To</label>
                                            <input type="date" class="form-control" name="date_to">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <button type="submit" name="selection" value="selection"
                                                    class="btn btn-primary">
                                                Selection
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
