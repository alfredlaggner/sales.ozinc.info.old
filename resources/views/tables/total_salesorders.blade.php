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
    <script lang="javascript" src="/js/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.0.5/jspdf.plugin.autotable.js"></script>

    <div class="container-fluid">
        @if ($overview)
            <div class="card">
                <div class='card-header'>
                    <h5>Total Sales Per Rep</h5>
                    @if($header)
                        @php
                            $dt = new DateTime($header->created_at);
                            $tz = new DateTimeZone('America/Los_Angeles'); // or whatever zone you're after
                            $dt->setTimezone($tz);
                        @endphp
                        <h5> Saved Commission </h5>

                        <p>Description: {{$header->description }}<br>
                            Comment: {{$header->comment }}<br>
                            Created at: {{$dt->format('Y-m-d H:i:s') }}<br>
                            Created by: {{$header->created_by }}<br>
                            Table Name: {{$header->name }}<br>
                            Month: {{$header->month }}-{{$header->year}}</p>
                    @endif

                </div>
                <div class="card card-body">
                    <div class="'table-controls">
                        <button id="download-xlsx-2">Excel</button>
                        <button id="download-pdf-2">PDF</button>
                    </div>
                    <div id="salespersons" class="table-sm table-bordered"></div>
                </div>
                <script>
                    function paramLookup(cell) {
                        return cell.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }

                    var table1 = new Tabulator("#salespersons", {
                        height: 400, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
                        data:{!! $overview !!}, //assign data to table
                        responsiveLayout: true,
                        layout: "fitColumns", //fit columns to width of table (optional)
                        groupHeader: [
                            function (value, count, data) { //generate header contents for gender groups
                                return value;
                            },
                        ], columns: [ //Define Table Columns
                            //    {title:"Name", field:"salesperson_name", width:150},
                            /*
                                                    {title: "Salesperson", field: "salesperson_name", responsive:4, width: 150},
                            */
                            {title: "Rep", field: "rep", responsive: 1},
                            {
                                title: "Paid Invoices",
                                field: "volume",
                                align: "right",
                                responsive: 4,
                                formatter: "money",
                                bottomCalc: "sum",
                                bottomCalcFormatter: "money",
                                bottomCalcFormatterParams: {
                                    decimal: ".",
                                    thousand: ",",
                                    precision: 2,
                                }
                            },
                            {
                                title: "Avg. Margin",
                                field: "margin",
                                align: "right",
                                bottomCalc: "avg",
                                formatter: "money",
                                bottomCalcParams: {precision: 2}
                            },
                            {
                                title: "Commission",
                                field: "commission",
                                align: "right",
                                bottomCalc: "sum",
                                formatter: "money",
                                bottomCalcParams: {precision: 2},
                                bottomCalcFormatter: "money",
                                bottomCalcFormatterParams: {
                                    decimal: ".",
                                    thousand: ",",
                                    precision: 2,
                                }
                            }
                        ],
                        rowClick: function (e, row) { //trigger an alert message when the row is clicked
                            alert("Row " + row.getData().id + " Clicked!!!!");
                        },
                    });
                    //trigger download of data.xlsx file
                    $("#download-xlsx-2").click(function () {
                        table1.download("xlsx", "salespersons_commissions.xlsx", {sheetName: "All Salespersons"});
                    });
                    //trigger download of data.pdf file
                    $("#download-pdf-2").click(function () {
                        table1.download("pdf", "data.pdf", {
                            orientation: "portrait", //set page orientation to portrait
                            title: "Salespersons Commissions", //add title to report
                        });
                    })
                </script>
            </div>
        @endif

        <div class="card">
            <div class='card-header'>
                @if($header)
                    @php
                        $dt = new DateTime($header->created_at);
                        $tz = new DateTimeZone('America/Los_Angeles'); // or whatever zone you're after
                        $dt->setTimezone($tz);
                    @endphp
                    <h5> Saved Commission </h5>

                    <p>Description: {{$header->description }}<br>
                        Comment: {{$header->comment }}<br>
                        Created at: {{$dt->format('Y-m-d H:i:s') }}<br>
                        Created by: {{$header->created_by }}<br>
                        Table Name: {{$header->name }}<br>
                        Month: {{$header->month }}-{{$header->year}}</p>
                @else
                    <h5>By Salesorder</h5>
                @endif
            </div>
            <div class="card card-body">
                <div class="table-controls">
                    <button id="download-xlsx">Excel</button>
                    <button id="download-pdf">PDF</button>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <select type="text" class="form-control" id="filter-field" placeholder="field">
                            <option></option>
                            <option value="name">Product</option>
                            <option value="brand_name">Brand</option>
                            <option value="category">Category</option>
                            <option value="order_number">Order</option>
                            <option value="rep">Rep</option>
                            <option value="margin">Margin</option>
                            <option value="commission">Commission</option>
                            <option value="commission_percent">Commission Percent</option>
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

                    table.setFilter(filter, $("#filter-type").val(), $("#filter-value").val());
                }

                //Update filters on value change
                $("#filter-field, #filter-type").change(updateFilter);
                $("#filter-value").keyup(updateFilter);

                //Clear filters on "Clear Filters" button click
                $("#filter-clear").click(function () {
                    $("#filter-field").val("");
                    $("#filter-type").val("=");
                    $("#filter-value").val("");

                    table.clearFilter();
                });


                var table = new Tabulator("#example-table", {
                    height: 1200, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
                    data:{!! $data !!}, //assign data to table
                    responsiveLayout: false,
                    layout: "fitColumns", //fit columns to width of table (optional)
                    groupBy: ['rep','month', "order_number"],
                    orderBy: ['rep','month', "order_number"],
                    groupStartOpen: [true,true, true],
                    groupToggleElement:"header",
                    columnCalcs: "both",
                    groupHeader: [
                        function (value, count, data) { //generate header contents for gender groups
                            return value;
                        },
                    ], columns:
                        [
                            {
                                title: "Product",
                                field: "name",
                                frozen: false,
                                width: 400
                            },
                            {
                                title: "Quantity",
                                field: "quantity",
                                align: "right"
                            },
                            {
                                title: "Cost",
                                field: "cost",
                                align: "right",
                                formatter: "money",
                                formatterParams: "allowEmpty"
                            },

                            {
                                title: "Price",
                                field: "unit_price",
                                align: "right",
                                formatter: "money",
                                formatterParams: "allowEmpty",
                            },
                            {
                                title: "Amount",
                                field: "amount",
                                align: "right",
                                bottomCalc: "sum",
                                formatter: "money",
                                formatterParams: "allowEmpty",
                                bottomCalcParams: {precision: 2},
                                bottomCalcFormatter: "money",
                                bottomCalcFormatterParams: {
                                    decimal: ".",
                                    thousand: ",",
                                    precision: 2,
                                }
                            },
                            {
                                title: "Margin",
                                field: "margin",
                                align: "right",
                                bottomCalc: "avg",
                                formatter: "money",
                                formatterParams: "allowEmpty",
                                bottomCalcParams: {precision: 2},
                                bottomCalcFormatter: "money",
                                bottomCalcFormatterParams: {
                                    decimal: ".",
                                    thousand: ",",
                                    precision: 2,
                                }
                            },
                            {
                                title: "Comm %",
                                field: "commission_percent",
                                align: "right",
                                formatter: "money",
                                formatterParams: "allowEmpty"
                            },
                            {
                                title: "Comm",
                                field: "commission",
                                align: "right",
                                bottomCalc: "sum",
                                formatter: "money",
                                formatterParams: "allowEmpty",
                                bottomCalcParams: {precision: 2},
                                bottomCalcFormatter: "money",
                                bottomCalcFormatterParams: {
                                    decimal: ".",
                                    thousand: ",",
                                    precision: 2,
                                }
                            },

                        ],
                });
                //trigger download of data.xlsx file
                $("#download-xlsx").click(function () {
                    table.download("xlsx", "monthly_commissions.xlsx", {sheetName: "All Salespersons"});
                });
                //trigger download of data.pdf file
                $("#download-pdf").click(function () {
                    table.download("pdf", "monthly_commissions.pdf", {
                        orientation: "portrait", //set page orientation to portrait
                        title: "All Salespersons", //add title to report
                    });
                })
            </script>


        </div>
    </div>
@endsection

