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
Route::get('/home', function () {
    return view('home');
});
Route::get('/welcome', function () {
    return view('welcome');
});

Auth::routes();

Route::get('new', 'ChartsController@unUsedCustomers')->name('new');

Route::get('/', 'CommissionController@index')->name('home');
Route::get('admin', 'DevelopController@index')->name('admin');
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

Route::get('test', 'TestController@index');
Route::post('calcPerSalesPerson', 'DevelopController@calcPerSalesPerson');
Route::post('calcPerCustomer', 'DevelopController@calcPerCustomer');
Route::post('calcPerBrand', 'DevelopController@calcPerBrand');
Route::post('calcPerProduct', 'DevelopController@calcPerProduct');
Route::post('totalDetails', 'DevelopController@totalDetails');

