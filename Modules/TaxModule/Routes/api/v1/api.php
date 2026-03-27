<?php

use Illuminate\Support\Facades\Route;

use Modules\TaxModule\Http\Controllers\Api\V1\TaxController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'taxvat', 'as' => 'taxvat.'], function () {
        Route::get('get-taxVat-list', [TaxController::class, 'getTaxVatList']);
        Route::put('get-calculated-tax', [TaxController::class, 'getCalculateTax']);
});
