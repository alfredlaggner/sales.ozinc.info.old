@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.2.3/dist/js/tabulator.min.js"></script>
    <link href="{{ URL::asset('css/dist/css/bootstrap/tabulator_bootstrap4.min.css') }}" rel="stylesheet">
    {{--
        <link href="{{ URL::asset('css/dist/css/tabulator_modern.min.css') }}" rel="stylesheet">
    --}}
    <script type="text/javascript" src="http://oss.sheetjs.com/js-xlsx/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.0.5/jspdf.plugin.autotable.js"></script>

    <div class="container">
        <h1>{{$name}}</h1>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
                   aria-controls="nav-home" aria-selected="true">Commissions Paid</a>
                <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab"
                   aria-controls="nav-profile" aria-selected="false">Commissions Open</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <div class="card">
                    <div class="card">
                        <div class='card-header'>
                            <h5>Paid Invoices</h5>
                        </div>
                        <div class="card card-body">
                            <div class="'table-controls">
                                <button id="download-xlsx">Excel</button>
                                <button id="download-pdf">PDF</button>
                            </div>
                            <div id="example-table" class="table-sm table-bordered"></div>
                        </div>


                        <script>
                            function paramLookup(cell) {
                                return cell.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            }



                            var table = new Tabulator("#example-table", {
                                height: 600, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
                                data:{!! $paid !!}, //assign data to table
                                responsiveLayout: false,
                                layout: "fitColumns", //fit columns to width of table (optional)
                                groupBy: ['month', "order_number"],
                                groupStartOpen: [ false, false],
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
                                            align:"left",
                                            width:600
                                        },
                                        {
                                            title: "Quantity",
                                            field: "quantity",
                                            align: "right"
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
                                            title: "Commission",
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
            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <div class="card">
                    <div class="card">
                        <div class='card-header'>
                            <h5>Paid Invoices</h5>
                        </div>
                        <div class="card card-body">
                            <div class="'table-controls">
                                <button id="download-xlsx">Excel</button>
                                <button id="download-pdf">PDF</button>
                            </div>
                            <div id="example-table2" class="table-sm table-bordered"></div>
                        </div>2


                        <script>
                            function paramLookup(cell) {
                                return cell.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            }


                            var table = new Tabulator("#example-table2", {
                                height: 600, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
                                data:{!! $unpaid !!}, //assign data to table
                                responsiveLayout: false,
                                layout: "fitColumns", //fit columns to width of table (optional)
                                groupBy: ['month', "order_number"],
                                groupStartOpen: [ false, false],
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
                                            align:"left",
                                            width:600
                                        },
                                        {
                                            title: "Quantity",
                                            field: "quantity",
                                            align: "right"
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
                                            title: "Commission",
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
                                            title: "Month",
                                            field: "month",
                                            align: "left",
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
            </div>
        </div>
@endsection

