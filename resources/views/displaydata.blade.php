@extends('layouts.app')
@section('content')

<div class="container">
    <h5>Laravel DataTables Tutorial Example</h5>
    <table class="table table-bordered" id="xtable">
        <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
        </thead>
    </table>
</div>
<script>
    $(function() {
        $('#xtable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! url('index') !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' }
            ]
        });
    });
</script>
@endsection