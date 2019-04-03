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
                <h5>All Invoice Lines</h5>
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
                    responsiveLayout: false,
                    layout: "fitData", //fit columns to width of table (optional)
                    groupBy: "month",
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
                            },
                            {
                                title: "Quantity",
                                field: "qty_invoiced",
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
                                topCalc: "sum",
                                formatter: "money",
                                formatterParams: "allowEmpty",
                                topCalcParams: {precision: 2},
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
                                formatterParams: "allowEmpty",
                                topCalcParams: {precision: 2},
                                topCalcFormatter: "money",
                                topCalcFormatterParams: {
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
                                topCalc: "sum",
                                formatter: "money",
                                formatterParams: "allowEmpty",
                                topCalcParams: {precision: 2},
                                topCalcFormatter: "money",
                                topCalcFormatterParams: {
                                    decimal: ".",
                                    thousand: ",",
                                    precision: 2,
                                }
                            },
                            {
                                title: "Untaxed",
                                field: "amount_untaxed",
                                align: "right",
                                topCalc: "sum",
                                formatter: "money",
                                formatterParams: "allowEmpty",
                                topCalcParams: {precision: 2},
                                topCalcFormatter: "money",
                                topCalcFormatterParams: {
                                    decimal: ".",
                                    thousand: ",",
                                    precision: 2,
                                }
                            },
                            {
                                title: "Total",
                                field: "amount_total",
                                align: "right",
                                topCalc: "sum",
                                formatter: "money",
                                formatterParams: "allowEmpty",
                                topCalcParams: {precision: 2},
                                topCalcFormatter: "money",
                                topCalcFormatterParams: {
                                    decimal: ".",
                                    thousand: ",",
                                    precision: 2,
                                }
                            },
                            {
                                title: "Tax",
                                field: "amount_tax",
                                align: "right",
                                topCalc: "sum",
                                formatter: "money",
                                formatterParams: "allowEmpty",
                                topCalcParams: {precision: 2},
                                topCalcFormatter: "money",
                                topCalcFormatterParams: {
                                    decimal: ".",
                                    thousand: ",",
                                    precision: 2,
                                }
                            },
                            {
                                title: "Customer",
                                field: "customer_name"
                            },

                            {
                                title: "Rep",
                                field: "rep"
                            },

                            {
                                title: "Order",
                                field: "order_number",
                            },
                            {
                                title: "Order Date",
                                field: "order_date"
                            },
                            {
                                title: "Invoice Date",
                                field: "invoice_date"
                            },
                            {
                                title: "SKU",
                                field: "sku"
                            },
                            {
                                title: "Brand",
                                field: "brand_name",
                            },
                            {
                                title: "Category",
                                field: "category"
                            }
                        ],
                    /*
                                        dataLoaded:function(data){ //freeze first row on data load
                                            var firstRow = this.getRows()[0];

                                            if(firstRow){
                                                firstRow.freeze();
                                            }
                                        }
                    */
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

