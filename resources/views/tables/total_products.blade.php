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

    <div class="container">
        <div class="card">
            <div class='card-header'>
                <h5>Total Sales Per Product</h5>
            </div>
            <div class="card card-body">
                <div class="'table-controls">
                    <button id="download-xlsx">Excel</button>
                    <button id="download-pdf">PDF</button>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <select type="text" class="form-control" id="filter-field" placeholder="field">
                            <option></option>
                            <option value="product_name">Product</option>
                            <option value="volume">Volume</option>
                            <option value="margin">Margin</option>
                            <option value="commission">Commission</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <select type="text" class="form-control" id="filter-type" placeholder="filter type">
                            <option></option>
                            <option value="=">=</option>
                            <option value="<"><</option>
                            <option value=">">></option>
                            <option value=">=">>=</option>
                            <option value="<="><=</option>
                            <option value="like">like</option>
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
                    height: 600, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
                    data:{!! $data !!}, //assign data to table
                    responsiveLayout: true,
                    layout: "fitColumns", //fit columns to width of table (optional)
                    groupBy: "product_name",
                    groupHeader: [
                        function (value, count, data) { //generate header contents for gender groups
                            return value;
                        },
                    ], columns: [ //Define Table Columns
                        //    {title:"Name", field:"salesperson_name", width:150},
                        {title: "Month", field: "month", responsive: 1},
                        {
                            title: "Volume",
                            field: "volume",
                            align: "right",
                            responsive: 4,
                            formatter: "money",
                            topCalc: "sum",
                            topCalcFormatter: "money",
                            topCalcFormatterParams: {
                                decimal: ".",
                                thousand: ",",
                                precision: 2,

                            }
                        },
                        {
                            title: "Margin",
                            field: "margin",
                            align: "right",
                            topCalc: "avg",
                            formatter: "money",
                            topCalcParams: {precision: 2}
                        },
                        {
                            title: "Commission",
                            field: "commission",
                            align: "right",
                            topCalc: "sum",
                            formatter: "money",
                            topCalcParams: {precision: 2},
                            topCalcFormatter: "money",
                            topCalcFormatterParams: {
                                decimal: ".",
                                thousand: ",",
                                precision: 2,
                            }
                        },
                        {title: "Product", field: "product_name", responsive: 4, headerFilter: false},
                    ],
                });
                //trigger download of data.xlsx file
                $("#download-xlsx").click(function () {
                    table.download("xlsx", "data.xlsx", {sheetName: "All Salespersons"});
                });
                //trigger download of data.pdf file
                $("#download-pdf").click(function () {
                    table.download("pdf", "data.pdf", {
                        orientation: "portrait", //set page orientation to portrait
                        title: "Example Report", //add title to report
                    });
                })
                //Trigger setFilter function with correct parameters
            </script>
        </div>
    </div>
@endsection

