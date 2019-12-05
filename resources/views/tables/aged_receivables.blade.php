@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
    {{--
        <link href="https://unpkg.com/tabulator-tables@4.2.3/dist/css/tabulator.min.css" rel="stylesheet">
    --}}
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.2.3/dist/js/tabulator.min.js"></script>
    <link href="{{ URL::asset('css/dist/css/bootstrap/tabulator_bootstrap4.min.css') }}" rel="stylesheet">
    {{--
        <link href="{{ URL::asset('css/dist/css/tabulator_modern.min.css') }}" rel="stylesheet">
    --}}
    <script type="text/javascript" src="http://oss.sheetjs.com/js-xlsx/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.0.5/jspdf.plugin.autotable.js"></script>

    <div class="container-fluid">
        <div class="card">
            <div class='card-header'>
                <h5>Aged Receivables</h5>
                @php
                    //  $is_expanded = true;
             //       $is_expanded = $is_expanded ? true : false;
            //      dd($is_expanded);
                @endphp
                @if($header)
                    @php
                        $dt = new DateTime($header->created_at);
                        $tz = new DateTimeZone('America/Los_Angeles'); // or whatever zone you're after
                        $dt->setTimezone($tz);
                    @endphp
                    <h5>Aged Receivables</h5>

                @endif

            </div>
            <div class="card card-body">
                <div class="'table-controls">
                    <button id="download-xlsx-2">Excel</button>
                    <button id="download-pdf-2">PDF</button>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <select type="text" class="form-control" id="filter-field" placeholder="field">
                            <option></option>
                            <option value="customer">Customer</option>
                            <option value="sales_order">Sales Order</option>
                            <option value="note">Note</option>
                            <option value="range1">1 - 7</option>
                            <option value="range2">8 - 14</option>
                            <option value="range3">15 - 30</option>
                            <option value="range4">31 - 60</option>
                            <option value="range5">60 - 90</option>
                            <option value="range6">91 - 120</option>
                            <option value="range7">over 121 </option>
                            <option value="range0">Not today</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <select type="text" class="form-control" id="filter-type" placeholder="filter type">
                            <option></option>
                            <option value="like">like</option>
                            <option value="=">=</option>
                            <option value="<"><</option>
                            <option value=">">></option>
                            <option value=">=">>=</option>
                            <option value="<="><=</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="filter-value" placeholder="value to filter">
                    </div>
                    <div class="form-group col-md-3">
                        <button id="filter-clear">Clear Filter</button>
                    </div>
                </div>
                <div id="example-table" class="table-sm table-bordered"></div>
            </div>

            <div id="aged_receivables" class="table-sm table-bordered"></div>
            </div>
            <script>
                function paramLookup(cell) {
                    return cell.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
                function updateFilter() {

                    var filter = $("#filter-field").val() == "function" ? customFilter : $("#filter-field").val();

                    if ($("#filter-field").val() == "function") {
                        $("#filter-type").prop("disabled", true);
                        $("#filter-value").prop("disabled", true);
                    } else {
                        $("#filter-type").prop("disabled", false);
                        $("#filter-value").prop("disabled", false);
                    }

                    table1.setFilter(filter, $("#filter-type").val(), $("#filter-value").val());
                }

                //Update filters on value change
                $("#filter-field, #filter-type").change(updateFilter);
                $("#filter-value").keyup(updateFilter);

                //Clear filters on "Clear Filters" button click
                $("#filter-clear").click(function () {
                    $("#filter-field").val("");
                    $("#filter-type").val("=");
                    $("#filter-value").val("");

                    table1.clearFilter();
                });


                function paramLookup(values, data) {
                    //values - array of column values
                    //data - all table data
                    //do some processing and return the param object
                    return {param1: "green"};
                }
                var table1 = new Tabulator("#aged_receivables", {
                    height: 600, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
                    data:{!! $data !!}, //assign data to table
                    persistenceID: "examplePerststance",
                    layout: "fitColumns", //fit columns to width of table (optional)
                    groupClosedShowCalcs: true,
                    groupToggleElement: "header",
                    groupStartOpen: [{{$is_expanded, true}}],
                    columnCalcs: 'both',
                    groupBy: {!! $grouping !!},
                    groupHeader: [
                        function (value, count, data) { //generate header contents for  groups
                            return value;
                        },
                    ],
                    columns: [

                        {
                            title: "Order",
                            width: 200,
                            field: "sales_order",
                            className: 'text-xl-left',
                            cellClick: function (e, cell) {
                                var cellValue = cell.getValue();
                                window.open("{!! url('/show_notes') !!}/" + cellValue, "_self");
                            },
                        },
                        {
                            title: "not today",
                            field: "range0",
                            align: "right",
                            bottomCalc: "sum",
                            formatterParams: "allowEmpty",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2},
                        },
                        {
                            title: "7",
                            field: "range1",
                            align: "center",
                            bottomCalc: "sum",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2}
                        },
                        {
                            title: "14",
                            field: "range2",
                            align: "right",
                            bottomCalc: "sum",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2}
                        },
                        {
                            title: "30",
                            field: "range3",
                            align: "right",
                            bottomCalc: "sum",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2}
                        },
                        {
                            title: "60",
                            field: "range4",
                            align: "right",
                            bottomCalc: "sum",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2}
                        },
                        {
                            title: "90",
                            field: "range5",
                            align: "center",
                            bottomCalc: "sum",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2}
                        },
                        {
                            title: "120",
                            field: "range6",
                            align: "right",
                            bottomCalc: "sum",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2}
                        },
                        {
                            title: "over 120",
                            field: "range7",
                            align: "right",
                            bottomCalc: "sum",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2}
                        },
                        {
                            title: "Total",
                            field: "sum_residual",
                            align: "right",
                            bottomCalc: "sum",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2},
                        },
                        {
                            title: "Collect",
                            field: "amount_to_collect",
                            align: "right",
                            bottomCalc: "sum",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2},
                        },
                        {
                            title: "Collected",
                            field: "amount_collected",
                            align: "right",
                            bottomCalc: "sum",
                            precision: 2,
                            formatter: "money",
                            bottomCalcParams: {precision: 2},
                        },
                        {
                            title: "Note",
                            field: "note",
                            width: 500,
                            formatter: "textarea",
                            editor: "textarea"
                        },

                        {
                            title: "",
                            field: "customer",
                            width: 1,
                        },

                    ],
                    /*
                                        rowClick: function (e, row) { //trigger an alert message when the row is clicked
                                            alert("Row " + row.getData().id + " Clicked!!!!");

                                        },
                    */
                    rowClick: function (e, row) {
                        var cells = row.getCells();
                        var so = cells[0];
                        var so_value = so.getValue();
                        if (so_value)
                        window.open("{!! url('/show_notes') !!}/" + so_value , "_self");
                    },

                });

                //trigger download of data.xlsx file
                $("#download-xlsx-2").click(function () {
                    table1.download("xlsx", "aged_reveivables.xlsx", {
                        sheetName: "Aged Receivables", orientation: "landscape"
                    });
                });
                //trigger download of data.pdf file
                $("#download-pdf-2").click(function () {
                    table1.download("pdf", "aged_reveivables.pdf", {
                        orientation: "landscape", //set page orientation to portrait
                        title: "Aged Reveivables", //add title to report
                        columnStyles: {6: {halign: 'center', fillColor: [0, 255, 0]}}, // Cells in first column centered and green
                    });
                })
            </script>
        </div>
    </div>
@endsection

