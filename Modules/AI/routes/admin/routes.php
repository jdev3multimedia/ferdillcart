<?php


use Illuminate\Support\Facades\Route;
use Modules\AI\app\Http\Controllers\Admin\Web\Product\ProductAutoFillController;


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

Route::group(['prefix' => 'admin', 'as' => 'admin.','middleware' => ['module:item','current-module']], function () {
    Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
        Route::get('title-auto-fill', [ProductAutoFillController::class, 'titleAutoFill'])->name('title-auto-fill');
        Route::get('description-auto-fill', [ProductAutoFillController::class, 'descriptionAutoFill'])->name('description-auto-fill');
        Route::get('general-setup-auto-fill', [ProductAutoFillController::class, 'GeneralSetupAutoFill'])->name('general-setup-auto-fill');
        Route::get('price-others-auto-fill', [ProductAutoFillController::class, 'PriceOthersAutoFill'])->name('price-others-auto-fill');
        Route::get('seo-section-auto-fill', [ProductAutoFillController::class, 'seoSectionAutoFill'])->name('seo-section-auto-fill');
        Route::get('generate-other-variation-data', [ProductAutoFillController::class, 'getOtherVariationData'])->name('generate-other-variation-data');
        Route::get('variation-setup-auto-fill', [ProductAutoFillController::class, 'variationSetupAutoFill'])->name('variation-setup-auto-fill');
        Route::post('analyze-image-auto-fill', [ProductAutoFillController::class, 'analyzeImageAutoFill'])->name('analyze-image-auto-fill');
        Route::post('generate-title-suggestions', [ProductAutoFillController::class, 'generateTitleSuggestions'])->name('generate-title-suggestions');
    });

});
