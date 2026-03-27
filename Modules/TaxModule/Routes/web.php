<?php
use Illuminate\Support\Facades\Route;


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

Route::group(['prefix' => 'taxvat', 'as' => 'taxvat.','middleware' =>['admin','current-module']], function () {
        Route::get('get-taxvat-data', 'TaxVatController@index')->name('index');
        Route::post('add-taxvat-data', 'TaxVatController@store')->name('store');
        Route::put('update-taxvat-data/{taxVat} ', 'TaxVatController@update')->name('update');
        Route::get('update-taxvat-status/{taxVat} ', 'TaxVatController@status')->name('status');
        Route::get('export-taxvat', 'TaxVatController@export')->name('export');

        Route::get('system-taxvat', 'SystemTaxVatSetupController@index')->name('systemTaxvat');
        Route::put('system-taxvat', 'SystemTaxVatSetupController@systemTaxVatStore')->name('systemTaxVatStore');
        Route::get('system-taxvat-vendor-status', 'SystemTaxVatSetupController@vendorStatus')->name('systemTaxVatVendorStatus');
});
