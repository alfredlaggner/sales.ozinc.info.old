<div class="container">
    <div class="card">
        <div class='card-header'>
            <h5>Aged Receivables Notes: {{$notes[1][0]}} </h5>
            <p></p>
        </div>
        <div class="card card-body">
            <div id="div12">
            </div>
            <table id="accounts" class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th class="text-xl-right">Customer</th>
                    <th class="text-xl-right">Note</th>
                    <th class="text-xl-right">by</th>
                    <th class="text-xl-right">on</th>
                </tr>
                </thead>
                <tbody>
                @foreach($notes[0] as $note)
                    <tr>
                        <td class="text-xl-left">{{$note->customer_name}}</td>
                        <td class="text-xl-left">{{$note->note}}</td>
                        <td class="text-xl-left">{{$note->note_by}}</td>
                        <td class="text-xl-left">{{$note->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

