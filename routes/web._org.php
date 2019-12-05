<?php

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    if (App::environment('local')) {
        URL::forceScheme('http');
    }
    /*	Route::get('/home', function () {
            return view('home');
        });*/
    /*Route::get('/welcome', function () {
        return view('welcome');
    });*/

    Auth::routes();

    Route::get('new', 'ChartsController@unUsedCustomers')->name('new');

    Route::get('/', 'CommissionController@index')->name('home');
    Route::post('/commission_calc', 'CommissionController@calcCommissions');
    Route::post('/saleorder_calc', 'CommissionController@calcCommissionsPerSalesOrder');
    Route::post('/init_calc', 'DevelopController@calcCommissions');
    Route::get('go-home', array('as' => 'go-home', 'uses' => 'CommissionController@index'));
    Route::post('view-so/{order_id}', array('as' => 'view_so', 'uses' => 'CommissionController@displaySalesOrder'));

    Route::get('/init_calc', 'DevelopController@calcCommissions');
    Route::get('/comm', 'DevelopController@allCommissions');
    Route::get('donutchart/{customer_id}/{customer_name}/{salesperson}/{month}', array('as' => 'donutchart', 'uses' => 'CommissionController@commissionsPerCustomerBrand'));

    Route::post('/calc_region', 'DevelopController@calcRegions');

    Route::post('/calc_brands', 'DevelopController@calcBrandsPerMonth');
    Route::post('/calc_customers', 'DevelopController@calcCustomersPerMonth');
    Route::post('/get_customers', 'DevelopController@selectCustomer');
    Route::get('/calc_one_customer/{customer_id}', 'DevelopController@calcOneCustomer');

// Display view
    Route::get('datatable', 'DataTablesController@datatable');
// Get Data
    Route::get('datatable/getdata', 'DataTablesController@getPosts')->name('datatable/getdata');

    Route::get('index', 'DisplayDataController@index');
    Route::get('create', 'DisplayDataController@create');
    Route::get('brand_ajax/{month}', 'DevelopController@brand_ajax');
    Route::get('customer_ajax/{month}', 'DevelopController@customer_ajax');
    Route::get('ajax_all_months', 'DevelopController@ajax_all_months');
    Route::get('ajax_region_months/{region}', 'DevelopController@ajax_region_months');

    Route::get('tests', 'TestController@index');
    Route::post('calcPerSalesPerson', 'DevelopController@calcPerSalesPerson');
    Route::post('calcPerCustomer', 'DevelopController@calcPerCustomer');
    Route::post('calcPerBrand', 'DevelopController@calcPerBrand');
    Route::post('calcPerProduct', 'DevelopController@calcPerProduct');
    Route::post('totalDetails', 'DevelopController@totalDetails');
    Route::post('cleanOutSales', 'DevelopController@cleanOutSales')->name('cleanOutSales');
    Route::post('totalSalesorders', 'DevelopController@totalSalesorders');

    Route::post('create_saved_commissions', 'DevelopController@createSavedCommission');
    Route::get('pay_saved_commission/{id}/{table_name}', 'CommissionPaidController@paySavedCommission')->name('pay_saved_commission');

    Route::get('admin', 'CommissionPaidController@admin')->name('admin');
    Route::get('view_saved_commissions/{id}', 'CommissionPaidController@viewSavedCommission')->name('saved_commission');
    Route::get('edit_saved_commission/{id}', 'CommissionPaidController@editSavedCommission')->name('edit_saved_commission');
    Route::post('save_saved_commission', 'CommissionPaidController@saveSavedCommission')->name('save_saved_commission');
    Route::post('create_saved_commissions_paid', 'CommissionPaidController@createSavedCommission');

    Route::get('products', 'DevelopController@all_products')->name('products');
    Route::get('aged_receivables', 'DevelopController@aged_receivables')->name('aged_receivables');
    Route::get('aged_receivables1/{rep_id?}', 'DevelopController@new_aged_receivables')->name('aged_receivables1');
    Route::get('aged_receivables_search/{rep_id?}', 'DevelopController@new_aged_receivables')->name('aged_receivables_search');
    Route::get('show_notes/{so}', 'InvoiceNoteController@index')->name('show_notes');
    Route::get('list_notes/{customer_id}', 'InvoiceNoteController@list_notes')->name('list_notes');
    Route::resource('invoice_notes', 'InvoiceNoteController');


    Route::get('export', 'NoteImportController@export')->name('export');
    Route::get('export_aged_ar', 'DevelopController@export_aged_ar')->name('export_aged_ar');
    Route::get('export_aged_ar_detail', 'DevelopController@export_aged_ar_detail')->name('export_aged_ar_detail');
    Route::get('importExportView', 'NoteImportController@importExportView');
    Route::post('import', 'NoteImportController@import')->name('import');


    Route::get('view_unpaid_paid', 'CommissionPaidController@view_paid_unpaid_commissions')->name('view_paid_unpaid_commissions');
Route::post('unpaid_paid', 'CommissionPaidController@list_paid_unpaid_commissions')->name('unpaid_paid');
Route::get('unpaid_paid_work', 'CommissionPaidController@list_paid_unpaid_commissions_work')->name('unpaid_paid_work');

    Route::get('/test', function () {
        return view('tables.home');
    });
    Route::get('calc_product', 'CommissionPaidController@calc_used_products')->name('calc_product');
