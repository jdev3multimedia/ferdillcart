<?php

use App\Http\Controllers\Admin\DeliveryMan\DeliveryManController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BusinessSettingsController;


Route::group(['namespace' => 'Admin', 'as' => 'admin.'], function () {

    Route::group(['middleware' => ['admin', 'current-module', 'actch:admin_panel']], function () {
        Route::get('/test', function () {
            // return view('admin-views.test.VendorPanel-tax-report');
            // return view('admin-views.test.surgeprice-setup.list');
            // return view('admin-views.test.surgeprice-setup.emty');
            // return view('admin-views.test.surgeprice-setup.daily-schedule');
            // return view('admin-views.test.components');

            // version-3.4
            // return view('admin-views.test.marketing-tools');
            // return view('admin-views.test.parcel-cancellation-setup');
            // return view('admin-views.test.deliveryman-withdraw-transaction');

            //React Landing New Page
            // return view('admin-views.test.React_Landing.trust-section');
            // return view('admin-views.test.React_Landing.popular-clients');
            // return view('admin-views.test.React_Landing.seller-app-download');
            // return view('admin-views.test.React_Landing.deliveryman-app-download');
            // return view('admin-views.test.React_Landing.banners-section');
            // return view('admin-views.test.React_Landing.gallery-section');
            // return view('admin-views.test.React_Landing.high-light-section');
            // return view('admin-views.test.React_Landing.faq-section');


            //DeliveryMan Details > Loyalty Poing & Refer & Earn
            // return view('admin-views.test.deliveryman-details.loyalty-point');
            // return view('admin-views.test.deliveryman-details.refer-earn');


            //SEO Settings Page
            // return view('admin-views.test.seo-setting.seo-setup-list');
            // return view('admin-views.test.seo-setting.seo-content-page');
            // return view('admin-views.test.business-setting.business-settins-payment');
            // return view('admin-views.test.business-setting.deliveryman-index');


        });
        Route::get('get-all-stores', 'VendorController@get_all_stores')->name('get_all_stores');
        Route::get('lang/{locale}', 'LanguageController@lang')->name('lang');
        Route::get('settings', 'SystemController@settings')->name('settings');
        Route::post('settings', 'SystemController@settings_update');
        Route::post('settings-password', 'SystemController@settings_password_update')->name('settings-password');
        Route::get('/get-store-data', 'SystemController@store_data')->name('get-store-data');
        Route::post('remove_image', 'BusinessSettingsController@remove_image')->name('remove_image');
        Route::get('system-currency', 'SystemController@system_currency')->name('system_currency');
        //dashboard
        Route::get('/', 'DashboardController@dashboard')->name('dashboard');

        Route::get('maintenance-mode', 'SystemController@maintenance_mode')->name('maintenance-mode');
        Route::get('landing-page', 'SystemController@landing_page')->name('landing-page');

        Route::group(['prefix' => 'parcel', 'as' => 'parcel.', 'middleware' => ['module:parcel']], function () {
            Route::get('category/status/{id}/{status}', 'ParcelCategoryController@status')->name('category.status');
            Route::resource('category', 'ParcelCategoryController');
            Route::get('orders/{status}', 'ParcelController@orders')->name('orders');
            Route::get('orders/export/{status}/{file_type}', 'ParcelController@parcel_orders_export')->name('parcel_orders_export');
            Route::get('details/{id}', 'ParcelController@order_details')->name('order.details');
            Route::get('settings', 'ParcelController@settings')->name('settings');
            Route::post('settings', 'ParcelController@update_settings')->name('update.settings');
            Route::get('dispatch/{status}', 'ParcelController@dispatch_list')->name('list');
            Route::post('instruction', 'ParcelController@instruction')->name('instruction');
            Route::get('/instruction/{id}/{status}', 'ParcelController@instruction_status')->name('instruction_status');
            Route::put('instruction_edit/', 'ParcelController@instruction_edit')->name('instruction_edit');
            Route::delete('instruction_delete/{id}', 'ParcelController@instruction_delete')->name('instruction_delete');

            Route::get('cancellation-settings', 'ParcelController@cancellationSettings')->name('cancellationSettings');
            Route::get('cancellation-settings-status', 'ParcelController@cancellationSettingsStatus')->name('cancellationSettingsStatus');
            Route::put('cancellation-settings-update', 'ParcelController@cancellationSettingsUpdate')->name('cancellationSettingsUpdate');
            Route::post('cancellation-reason', 'ParcelController@cancellationReason')->name('cancellationReason');
            Route::get('cancellation-reason-status/{reason}', 'ParcelController@cancellationReasonStatus')->name('cancellationReasonStatus');
            Route::get('cancellation-reason-edit/{reason}', 'ParcelController@cancellationReasonEdit')->name('cancellationReasonEdit');
            Route::put('cancellation-reason-update/{reason}', 'ParcelController@cancellationReasonUpdate')->name('cancellationReasonUpdate');
            Route::delete('cancellation-reason-delete/{reason}', 'ParcelController@cancellationReasonDelete')->name('cancellationReasonDelete');
            Route::get('cancellation-reason-export', 'ParcelController@cancellationReasonExport')->name('cancellationReasonExport');
        });


        Route::group(['prefix' => 'dashboard-stats', 'as' => 'dashboard-stats.'], function () {
            Route::post('order', 'DashboardController@order')->name('order');
            Route::post('zone', 'DashboardController@zone')->name('zone');
            Route::post('user-overview', 'DashboardController@user_overview')->name('user-overview');
            Route::post('commission-overview', 'DashboardController@commission_overview')->name('commission-overview');
            Route::post('business-overview', 'DashboardController@business_overview')->name('business-overview');
        });

        Route::post('item/variant-price', 'ItemController@variant_price')->name('item.variant-price');

        Route::group(['prefix' => 'item', 'as' => 'item.', 'middleware' => ['module:item']], function () {
            Route::get('add-new', 'ItemController@index')->name('add-new');
            Route::post('variant-combination', 'ItemController@variant_combination')->name('variant-combination');
            Route::post('store', 'ItemController@store')->name('store');
            Route::get('edit/{id}', 'ItemController@edit')->name('edit');
            Route::post('update/{id}', 'ItemController@update')->name('update');
            Route::get('list', 'ItemController@list')->name('list');
            Route::delete('delete/{id}', 'ItemController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'ItemController@status')->name('status');
            Route::get('review-status/{id}/{status}', 'ItemController@reviews_status')->name('reviews.status');
            Route::post('search', 'ItemController@search')->name('search');
            Route::post('store/{store_id}/search', 'ItemController@search_store')->name('store-search');
            Route::get('reviews', 'ItemController@review_list')->name('reviews');
            // Route::post('reviews/search', 'ItemController@review_search')->name('reviews.search');
            Route::get('remove-image', 'ItemController@remove_image')->name('remove-image');
            Route::get('view/{id}', 'ItemController@view')->name('view');
            Route::get('store-item-export', 'ItemController@store_item_export')->name('store-item-export');
            Route::get('reviews-export', 'ItemController@reviews_export')->name('reviews_export');
            Route::get('item-wise-reviews-export', 'ItemController@item_wise_reviews_export')->name('item_wise_reviews_export');

            Route::get('new/item/list', 'ItemController@approval_list')->name('approval_list');
            Route::get('approved', 'ItemController@approved')->name('approved');
            Route::get('product_denied', 'ItemController@deny')->name('deny');
            Route::get('requested/item/view/{id}', 'ItemController@requested_item_view')->name('requested_item_view');
            Route::get('product-gallery', 'ItemController@product_gallery')->name('product_gallery');

            //ajax request
            Route::get('get-categories', 'ItemController@get_categories')->name('get-categories');
            Route::get('get-items', 'ItemController@get_items')->name('getitems');
            Route::get('get-items-flashsale', 'ItemController@get_items_flashsale')->name('getitems-flashsale');
            Route::post('food-variation-generate', 'ItemController@food_variation_generator')->name('food-variation-generate');
            Route::post('variation-generate', 'ItemController@variation_generator')->name('variation-generate');


            Route::get('export', 'ItemController@export')->name('export');

            //Mainul
            Route::get('get-variations', 'ItemController@get_variations')->name('get-variations');
            Route::get('get-stock', 'ItemController@get_stock')->name('get_stock');
            Route::post('stock-update', 'ItemController@stock_update')->name('stock-update');

            //Import and export
            Route::get('bulk-import', 'ItemController@bulk_import_index')->name('bulk-import');
            Route::post('bulk-import', 'ItemController@bulk_import_data');
            Route::get('bulk-export', 'ItemController@bulk_export_index')->name('bulk-export-index');
            Route::post('bulk-export', 'ItemController@bulk_export_data')->name('bulk-export');
        });

        Route::group(['prefix' => 'promotional-banner', 'as' => 'promotional-banner.', 'middleware' => ['module:banner']], function () {
            Route::get('add-new', 'OtherBannerController@promotional_index')->name('add-new');
            Route::get('add-video', 'OtherBannerController@promotional_video')->name('add-video');
            Route::post('store', 'OtherBannerController@promotional_store')->name('store');
            Route::get('edit/{id}', 'OtherBannerController@promotional_edit')->name('edit');
            Route::post('update/{id}', 'OtherBannerController@promotional_update')->name('update');
            Route::get('update-status/{id}/{status}', 'OtherBannerController@promotional_status')->name('update-status');
            Route::delete('delete/{banner}', 'OtherBannerController@promotional_destroy')->name('delete');
            Route::get('add-why-choose', 'OtherBannerController@promotional_why_choose')->name('add-why-choose');
            Route::post('why-choose/store', 'OtherBannerController@why_choose_store')->name('why-choose-store');
            Route::get('why-choose/edit/{id}', 'OtherBannerController@why_choose_edit')->name('why-choose-edit');
            Route::post('why-choose/update/{id}', 'OtherBannerController@why_choose_update')->name('why-choose-update');
            Route::get('why-choose/update-status/{id}/{status}', 'OtherBannerController@why_choose_status')->name('why-choose-status-update');
            Route::delete('why-choose/delete/{banner}', 'OtherBannerController@why_choose_destroy')->name('why-choose-delete');
            Route::post('video-content/store', 'OtherBannerController@video_content_store')->name('video-content-store');
            Route::post('video-image/store', 'OtherBannerController@video_image_store')->name('video-image-store');
        });

        Route::group(['prefix' => 'campaign', 'as' => 'campaign.', 'middleware' => ['module:campaign']], function () {
            Route::get('{type}/add-new', 'CampaignController@index')->name('add-new');
            Route::post('store/basic', 'CampaignController@storeBasic')->name('store-basic');
            Route::post('store/item', 'CampaignController@storeItem')->name('store-item');
            Route::get('{type}/edit/{campaign}', 'CampaignController@edit')->name('edit');
            Route::get('{type}/view/{campaign}', 'CampaignController@view')->name('view');
            Route::post('basic/update/{campaign}', 'CampaignController@update')->name('update-basic');
            Route::post('item/update/{campaign}', 'CampaignController@updateItem')->name('update-item');
            Route::get('remove-store/{campaign}/{store}', 'CampaignController@remove_store')->name('remove-store');
            Route::post('add-store/{campaign}', 'CampaignController@addstore')->name('addstore');
            Route::get('{type}/list', 'CampaignController@list')->name('list');
            Route::get('status/{type}/{id}/{status}', 'CampaignController@status')->name('status');
            Route::delete('delete/{campaign}', 'CampaignController@delete')->name('delete');
            Route::delete('item/delete/{campaign}', 'CampaignController@delete_item')->name('delete-item');
            Route::post('basic-search', 'CampaignController@searchBasic')->name('searchBasic');
            Route::post('item-search', 'CampaignController@searchItem')->name('searchItem');
            Route::get('store-confirmation/{campaign}/{id}/{status}', 'CampaignController@store_confirmation')->name('store_confirmation');
            Route::get('basic-campaign-export', 'CampaignController@basic_campaign_export')->name('basic_campaign_export');
            Route::get('item-campaign-export', 'CampaignController@item_campaign_export')->name('item_campaign_export');

        });


        Route::group(['prefix' => 'flash-sale', 'as' => 'flash-sale.'], function () {
            Route::get('add-new', 'FlashSaleController@index')->name('add-new');
            Route::post('store', 'FlashSaleController@store')->name('store');
            Route::get('edit/{id}', 'FlashSaleController@edit')->name('edit');
            Route::post('update/{id}', 'FlashSaleController@update')->name('update');
            Route::get('publish/{id}/{publish}', 'FlashSaleController@publish')->name('publish');
            Route::delete('delete/{id}', 'FlashSaleController@delete')->name('delete');
            Route::get('add-product/{id}', 'FlashSaleController@add_product')->name('add-product');
            Route::post('store-product', 'FlashSaleController@store_product')->name('store-product');
            Route::delete('delete-product/{id}', 'FlashSaleController@delete_product')->name('delete-product');
            Route::get('status/{id}/{status}', 'FlashSaleController@status_product')->name('status-product');
        });

        Route::group(['prefix' => 'message', 'as' => 'message.'], function () {
            Route::get('list', 'ConversationController@list')->name('list');
            Route::post('store/{user_id}', 'ConversationController@store')->name('store');
            Route::get('view/{conversation_id}/{user_id}', 'ConversationController@view')->name('view');
        });


        Route::group(['prefix' => 'store', 'as' => 'store.'], function () {
            Route::get('get-stores-data/{store}', 'VendorController@get_store_data')->name('get-stores-data');
            Route::get('store-filter/{id}', 'VendorController@store_filter')->name('store-filter');
            Route::get('get-account-data/{store}', 'VendorController@get_account_data')->name('store-filter');
            Route::get('get-stores', 'VendorController@get_stores')->name('get-stores');
            Route::get('get-providers', 'VendorController@get_providers')->name('get-providers');
            Route::get('get-addons', 'VendorController@get_addons')->name('get_addons');
            Route::group(['middleware' => ['module:store']], function () {
                Route::get('update-application/{id}/{status}', 'VendorController@update_application')->name('application');
                Route::get('add', 'VendorController@index')->name('add');
                Route::post('store', 'VendorController@store')->name('store');
                Route::get('edit/{id}', 'VendorController@edit')->name('edit');
                Route::post('update/{store}', 'VendorController@update')->name('update');
                Route::post('discount/{store}', 'VendorController@discountSetup')->name('discount');
                Route::post('update-settings/{store}', 'VendorController@updateStoreSettings')->name('update-settings');
                Route::post('update-meta-data/{store}', 'VendorController@updateStoreMetaData')->name('update-meta-data');
                Route::delete('delete/{store}', 'VendorController@destroy')->name('delete');
                Route::delete('clear-discount/{store}', 'VendorController@cleardiscount')->name('clear-discount');
                // Route::get('view/{store}', 'VendorController@view')->name('view_tab');
                Route::get('disbursement-export/{id}/{type}', 'VendorController@disbursement_export')->name('disbursement-export');
                Route::get('view/{store}/{tab?}/{sub_tab?}', 'VendorController@view')->name('view');
                Route::get('list', 'VendorController@list')->name('list');
                Route::get('pending-requests', 'VendorController@pending_requests')->name('pending-requests');
                Route::get('deny-requests', 'VendorController@deny_requests')->name('deny-requests');
                Route::post('search', 'VendorController@search')->name('search');
                Route::get('export', 'VendorController@export')->name('export');
                Route::get('store-wise-reviwe-export', 'VendorController@store_wise_reviwe_export')->name('store_wise_reviwe_export');
                Route::get('export/cash/{type}/{store_id}', 'VendorController@cash_export')->name('cash_export');
                Route::get('export/order/{type}/{store_id}', 'VendorController@order_export')->name('order_export');
                Route::get('export/withdraw/{type}/{store_id}', 'VendorController@withdraw_trans_export')->name('withdraw_trans_export');
                Route::get('status/{store}/{status}', 'VendorController@status')->name('status');
                Route::get('featured/{store}/{status}', 'VendorController@featured')->name('featured');
                Route::get('toggle-settings-status/{store}/{status}/{menu}', 'VendorController@store_status')->name('toggle-settings');
                Route::post('status-filter', 'VendorController@status_filter')->name('status-filter');


                Route::get('recommended-store', 'VendorController@recommended_store')->name('recommended_store');
                Route::get('recommended-store-add', 'VendorController@recommended_store_add')->name('recommended_store_add');
                Route::get('recommended-store-status/{id}/{status}', 'VendorController@recommended_store_status')->name('recommended_store_status');
                Route::delete('recommended-store-remove/{id}', 'VendorController@recommended_store_remove')->name('recommended_store_remove');
                Route::get('shuffle-recommended-store/{status}', 'VendorController@shuffle_recommended_store')->name('shuffle_recommended_store');

                Route::get('selected-stores', 'VendorController@selected_stores')->name('selected_stores');


                //Import and export
                Route::get('bulk-import', 'VendorController@bulk_import_index')->name('bulk-import');
                Route::post('bulk-import', 'VendorController@bulk_import_data');
                Route::get('bulk-export', 'VendorController@bulk_export_index')->name('bulk-export-index');
                Route::post('bulk-export', 'VendorController@bulk_export_data')->name('bulk-export');
                //Store shcedule
                Route::post('add-schedule', 'VendorController@add_schedule')->name('add-schedule');
                Route::get('remove-schedule/{store_schedule}', 'VendorController@remove_schedule')->name('remove-schedule');
            });

            Route::group(['middleware' => ['module:withdraw_list']], function () {
                Route::post('withdraw-status/{id}', 'VendorController@withdrawStatus')->name('withdraw_status');
                Route::get('withdraw_list', 'VendorController@withdraw')->name('withdraw_list');
                Route::post('withdraw_search', 'VendorController@withdraw_search')->name('withdraw_search');
                Route::get('withdraw_export', 'VendorController@withdraw_export')->name('withdraw_export');
                Route::get('withdraw-view/{withdraw_id}/{seller_id}', 'VendorController@withdraw_view')->name('withdraw_view');
            });

            // message
            Route::get('message/{conversation_id}/{user_id}', 'VendorController@conversation_view')->name('message-view');
            Route::get('message/list', 'VendorController@conversation_list')->name('message-list');
        });


        Route::get('addon/system-addons', function () {
            return to_route('admin.system-addon.index');
        })->name('addon.index');

        Route::get('order/generate-invoice/{id}', 'OrderController@generate_invoice')->name('order.generate-invoice');
        Route::get('order/print-invoice/{id}', 'OrderController@print_invoice')->name('order.print-invoice');
        Route::get('order/status', 'OrderController@status')->name('order.status');
        Route::get('order/offline-payment', 'OrderController@offline_payment')->name('order.offline_payment');
        Route::group(['prefix' => 'order', 'as' => 'order.', 'middleware' => ['module:order']], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::get('all-details/{id}', 'OrderController@all_details')->name('all-details');

            // Route::put('status-update/{id}', 'OrderController@status')->name('status-update');
            Route::get('view/{id}', 'OrderController@view')->name('view');
            Route::post('update-shipping/{order}', 'OrderController@update_shipping')->name('update-shipping');
            Route::delete('delete/{id}', 'OrderController@delete')->name('delete');

            Route::get('add-delivery-man/{order_id}/{delivery_man_id}', 'OrderController@add_delivery_man')->name('add-delivery-man');
            Route::get('payment-status', 'OrderController@payment_status')->name('payment-status');

            Route::post('add-payment-ref-code/{id}', 'OrderController@add_payment_ref_code')->name('add-payment-ref-code');
            Route::post('add-order-proof/{id}', 'OrderController@add_order_proof')->name('add-order-proof');
            Route::get('remove-proof-image', 'OrderController@remove_proof_image')->name('remove-proof-image');
            Route::get('store-filter/{store_id}', 'OrderController@restaurnt_filter')->name('store-filter');
            Route::get('filter/reset', 'OrderController@filter_reset');
            Route::post('filter', 'OrderController@filter')->name('filter');
            Route::get('search', 'OrderController@search')->name('search');
            Route::post('store/search', 'OrderController@store_order_search')->name('store-search');
            Route::get('store/export', 'OrderController@store_order_export')->name('store-export');
            //order update
            Route::post('add-to-cart', 'OrderController@add_to_cart')->name('add-to-cart');
            Route::post('remove-from-cart', 'OrderController@remove_from_cart')->name('remove-from-cart');
            Route::get('update/{order}', 'OrderController@update')->name('update');
            Route::get('edit-order/{order}', 'OrderController@edit')->name('edit');
            Route::get('quick-view', 'OrderController@quick_view')->name('quick-view');
            Route::get('quick-view-cart-item', 'OrderController@quick_view_cart_item')->name('quick-view-cart-item');
            Route::get('export-orders/{file_type}/{status}/{type}', 'OrderController@export_orders')->name('export');
            Route::post('switch-to-cod/{order}', 'OrderController@switch_to_cod')->name('switch_to_cod');

            Route::get('offline/payment/list/{status}', 'OrderController@offline_verification_list')->name('offline_verification_list');
            Route::get('parcel-cancelation-reasons', 'OrderController@parcelCancellationReason')->name('parcelCancellationReason');
            Route::put('cancel-parcel', 'OrderController@CancelParcel')->name('CancelParcel');
            Route::put('parcel-refund', 'OrderController@parcelRefund')->name('parcelRefund');
            Route::get('parcel-return', 'OrderController@parcelReturn')->name('parcelReturn');

        });
        // Refund
        Route::group(['prefix' => 'refund', 'as' => 'refund.', 'middleware' => ['module:order']], function () {
            Route::get('refund_mode', 'OrderController@refund_mode')->name('refund_mode');
            Route::post('refund_reason', 'OrderController@refund_reason')->name('refund_reason');
            Route::get('/status/{id}/{status}', 'OrderController@reason_status')->name('reason_status');
            Route::put('reason-update/', 'OrderController@reasonUpdate')->name('reason-update');
            Route::get('reason-edit/{id}', 'OrderController@reasonEdit')->name('reason-edit');
            Route::delete('reason_delete/{id}', 'OrderController@reason_delete')->name('reason_delete');
            Route::put('order_refund_rejection/', 'OrderController@order_refund_rejection')->name('order_refund_rejection');
            Route::get('/{status}', 'OrderController@list')->name('refund_attr');
        });


        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.', 'middleware' => ['module:settings']], function () {
            Route::get('business-setup/{tab?}', 'BusinessSettingsController@business_index')->name('business-setup');
            Route::get('react-setup', 'BusinessSettingsController@react_setup')->name('react-setup');
            Route::post('react-update', 'BusinessSettingsController@react_update')->name('react-update');
            Route::post('update-setup', 'BusinessSettingsController@business_setup')->name('update-setup');
            Route::post('update-payment-setup', 'BusinessSettingsController@updatePaymentSetup')->name('update-payment-setup');
            Route::post('update-landing-setup', 'BusinessSettingsController@landing_page_settings_update')->name('update-landing-setup');
            Route::delete('delete-custom-landing-page', 'BusinessSettingsController@delete_custom_landing_page')->name('delete-custom-landing-page');
            Route::post('update-dm', 'BusinessSettingsController@update_dm')->name('update-dm');
            Route::post('update-disbursement', 'BusinessSettingsController@update_disbursement')->name('update-disbursement');
            Route::post('update-store', 'BusinessSettingsController@update_store')->name('update-store');
            Route::post('update-order', 'BusinessSettingsController@update_order')->name('update-order');
            Route::post('update-priority', 'BusinessSettingsController@update_priority')->name('update-priority');
            Route::get('app-settings', 'BusinessSettingsController@app_settings')->name('app-settings');
            Route::POST('app-settings', 'BusinessSettingsController@update_app_settings')->name('app-settings');
            Route::get('websocket', 'BusinessSettingsController@websocket')->name('websocket');
            Route::post('update-websocket', 'BusinessSettingsController@update_websocket')->name('update-websocket');
            Route::get('pages/admin-landing-page-settings/{tab?}', 'BusinessSettingsController@admin_landing_page_settings')->name('admin-landing-page-settings');
            Route::POST('pages/admin-landing-page-settings/{tab}', 'BusinessSettingsController@update_admin_landing_page_settings')->name('admin-landing-page-settings');
            Route::get('promotional-status/{id}/{status}', 'BusinessSettingsController@promotional_status')->name('promotional-status');
            Route::get('pages/admin-landing-page-settings/promotional-section/edit/{id}', 'BusinessSettingsController@promotional_edit')->name('promotional-edit');
            Route::post('promotional-section/update/{id}', 'BusinessSettingsController@promotional_update')->name('promotional-update');
            Route::delete('banner/delete/{banner}', 'BusinessSettingsController@promotional_destroy')->name('promotional-delete');
            Route::get('feature-status/{id}/{status}', 'BusinessSettingsController@feature_status')->name('feature-status');
            Route::get('pages/admin-landing-page-settings/feature-list/edit/{id}', 'BusinessSettingsController@feature_edit')->name('feature-edit');
            Route::post('feature-section/update/{id}', 'BusinessSettingsController@feature_update')->name('feature-update');
            Route::delete('feature/delete/{feature}', 'BusinessSettingsController@feature_destroy')->name('feature-delete');
            Route::get('criteria-status/{id}/{status}', 'BusinessSettingsController@criteria_status')->name('criteria-status');
            Route::get('pages/admin-landing-page-settings/why-choose-us/criteria-list/edit/{id}', 'BusinessSettingsController@criteria_edit')->name('criteria-edit');
            Route::post('criteria-section/update/{id}', 'BusinessSettingsController@criteria_update')->name('criteria-update');
            Route::delete('admin/criteria/delete/{criteria}', 'BusinessSettingsController@criteria_destroy')->name('criteria-delete');
            Route::get('review-status/{id}/{status}', 'BusinessSettingsController@review_status')->name('review-status');
            Route::get('pages/admin-landing-page-settings/testimonials/review-list/edit/{id}', 'BusinessSettingsController@review_edit')->name('review-edit');
            Route::post('review-section/update/{id}', 'BusinessSettingsController@review_update')->name('review-update');
            Route::delete('review/delete/{review}', 'BusinessSettingsController@review_destroy')->name('review-delete');
            Route::get('pages/react-landing-page-settings/{tab?}', 'BusinessSettingsController@react_landing_page_settings')->name('react-landing-page-settings');
            Route::POST('pages/react-landing-page-settings/{tab?}',
                'BusinessSettingsController@update_react_landing_page_settings')->name('react-landing-page-settings');
            Route::DELETE('react-landing-page-settings/{tab}/{key}', 'BusinessSettingsController@delete_react_landing_page_settings')->name('react-landing-page-settings-delete');
            Route::get('review-react-status/{id}/{status}', 'BusinessSettingsController@review_react_status')->name('review-react-status');
            Route::get('pages/react-landing-page-settings/testimonials/review-react-list/edit/{id}', 'BusinessSettingsController@review_react_edit')->name('review-react-edit');
            Route::get('status-update/{type}/{key}', [BusinessSettingsController::class, 'statusUpdate'])->name('statusUpdate');

            Route::post('pages/react-landing-page-settings/faq-store/', [BusinessSettingsController::class, 'reactFaqStore'])->name('reactFaqStore');
            Route::get('pages/react-landing-page-settings/faq-status/{id}/{status}', [BusinessSettingsController::class, 'reactfaqStatus'])->name('reactfaqStatus');
            Route::get('pages/react-landing-page-settings/faq/edit/{id}', [BusinessSettingsController::class, 'reactfaqEdit'])->name('reactfaqEdit');
            Route::post('pages/react-landing-page-settings/faq-data/update/{id}', [BusinessSettingsController::class, 'reactFaqUpdate'])->name('reactFaqUpdate');
            Route::delete('pages/react-landing-page-settings/faq/delete/{faq}', [BusinessSettingsController::class, 'reactfaqDestroy'])->name('reactfaqDestroy');

            //promotional banner
            Route::post('promotional-banner-store/', [BusinessSettingsController::class, 'react_promotional_banner_store'])->name('promotional-banner-store');
            Route::get('promotional-banner-status/{id}/{status}', [BusinessSettingsController::class, 'react_promotional_banner_status'])->name('promotional-banner-status');
            Route::post('promotional-banner/update/{id}', [BusinessSettingsController::class, 'react_promotional_banner_update'])->name('promotional-banner-update');
            Route::delete('promotional-banner/delete/{react_promotional_banner}', [BusinessSettingsController::class, 'react_promotional_banner_destroy'])->name('promotional-banner-delete');


            Route::post('review-react-section/update/{id}', 'BusinessSettingsController@review_react_update')->name('review-react-update');
            Route::delete('review-react/delete/{review}', 'BusinessSettingsController@review_react_destroy')->name('review-react-delete');
            Route::get('pages/flutter-landing-page-settings/{tab?}', 'BusinessSettingsController@flutter_landing_page_settings')->name('flutter-landing-page-settings');
            Route::POST('pages/flutter-landing-page-settings/{tab}', 'BusinessSettingsController@update_flutter_landing_page_settings')->name('flutter-landing-page-settings');
            Route::get('flutter-criteria-status/{id}/{status}', 'BusinessSettingsController@flutter_criteria_status')->name('flutter-criteria-status');
            Route::get('pages/flutter-landing-page-settings/special-criteria/edit/{id}', 'BusinessSettingsController@flutter_criteria_edit')->name('flutter-criteria-edit');
            Route::post('flutter-criteria-section/update/{id}', 'BusinessSettingsController@flutter_criteria_update')->name('flutter-criteria-update');
            Route::delete('flutter/criteria/delete/{criteria}', 'BusinessSettingsController@flutter_criteria_destroy')->name('flutter-criteria-delete');

            Route::group(['prefix' => 'marketing', 'as' => 'marketing.'], function () {
                Route::get('analytic-setup', 'Marketing\AnalyticScriptController@analyticSetup')->name('analytic');
                Route::post('analytic-setup-update', 'Marketing\AnalyticScriptController@analyticUpdate')->name('analyticUpdate');
                Route::get('analytic-status', 'Marketing\AnalyticScriptController@analyticStatus')->name('analyticStatus');
            });

            //openAI
            Route::get('open-ai', 'BusinessSettingsController@openAI')->name('openAI');
            Route::get('open-ai-settings', 'BusinessSettingsController@openAISettings')->name('openAISettings');
            Route::put('open-ai-settings-update', 'BusinessSettingsController@openAISettingsUpdate')->name('openAISettingsUpdate');
            Route::get('open-ai-config-status', 'BusinessSettingsController@openAIConfigStatus')->name('openAIConfigStatus');
            Route::post('openai-update', 'BusinessSettingsController@openAIConfigUpdate')->name('openAIConfigUpdate');

            // Page Meta Data
            Route::group(['prefix' => 'seo-settings', 'as' => 'seo-settings.'], function () {
                Route::get('/page-meta-data', 'BusinessSettingsController@pageMetaData')->name('pageMetaData');
                Route::post('/page-meta-data-update', 'BusinessSettingsController@pageMetaDataUpdate')->name('pageMetaDataUpdate');
            });
            // Centerlize login
            Route::group(['prefix' => 'login-settings', 'as' => 'login-settings.'], function () {
                Route::get('login-setup', 'BusinessSettingsController@login_settings')->name('index');
                Route::post('login-setup/update', 'BusinessSettingsController@login_settings_update')->name('update');
            });

            Route::group(['prefix' => 'addon-activation', 'as' => 'addon-activation.'], function () {
                Route::get('', 'AddonActivationController@index')->name('index');
                Route::post('activation', 'AddonActivationController@activation')->name('activation');
            });

            Route::get('login-url-setup', 'BusinessSettingsController@login_url_page')->name('login_url_page');
            Route::post('login-url-setup/update', 'BusinessSettingsController@login_url_page_update')->name('login_url_update');

            Route::get('email-setup/{type}/{tab?}', 'BusinessSettingsController@email_index')->name('email-setup');
            Route::POST('email-setup/{type}/{tab?}', 'BusinessSettingsController@update_email_index')->name('email-setup');
            Route::get('email-status/{type}/{tab}/{status}', 'BusinessSettingsController@update_email_status')->name('email-status');

            Route::get('toggle-settings/{key}/{value}', 'BusinessSettingsController@toggle_settings')->name('toggle-settings');
            Route::get('site_direction', 'BusinessSettingsController@site_direction')->name('site_direction');


            Route::get('fcm-index', 'BusinessSettingsController@fcm_index')->name('fcm-index');
            Route::get('fcm-config', 'BusinessSettingsController@fcm_config')->name('fcm-config');
            Route::post('update-fcm', 'BusinessSettingsController@update_fcm')->name('update-fcm');

            Route::post('update-fcm-messages', 'BusinessSettingsController@update_fcm_messages')->name('update-fcm-messages');
            Route::post('update-fcm-messages-rental', 'BusinessSettingsController@update_fcm_messages_rental')->name('update-fcm-messages-rental');

            Route::get('currency-add', 'BusinessSettingsController@currency_index')->name('currency-add');
            Route::post('currency-add', 'BusinessSettingsController@currency_store');
            Route::get('currency-update/{id}', 'BusinessSettingsController@currency_edit')->name('currency-update');
            Route::put('currency-update/{id}', 'BusinessSettingsController@currency_update');
            Route::delete('currency-delete/{id}', 'BusinessSettingsController@currency_delete')->name('currency-delete');

            Route::get('pages/business-page/terms-and-conditions', 'BusinessSettingsController@terms_and_conditions')->name('terms-and-conditions');
            Route::post('pages/business-page/terms-and-conditions', 'BusinessSettingsController@terms_and_conditions_update');

            Route::get('pages/business-page/privacy-policy', 'BusinessSettingsController@privacy_policy')->name('privacy-policy');
            Route::post('pages/business-page/privacy-policy', 'BusinessSettingsController@privacy_policy_update');

            Route::get('pages/business-page/about-us', 'BusinessSettingsController@about_us')->name('about-us');
            Route::post('pages/business-page/about-us', 'BusinessSettingsController@about_us_update');

            Route::get('pages/business-page/refund', 'BusinessSettingsController@refund_policy')->name('refund');
            Route::post('pages/business-page/refund', 'BusinessSettingsController@refund_update');
            Route::get('pages/refund-policy/{status}', 'BusinessSettingsController@refund_policy_status')->name('refund-policy-status');

            Route::get('pages/business-page/cancelation', 'BusinessSettingsController@cancellation_policy')->name('cancelation');
            Route::post('pages/business-page/cancelation', 'BusinessSettingsController@cancellation_policy_update');
            Route::get('pages/cancellation-policy/{status}', 'BusinessSettingsController@cancellation_policy_status')->name('cancellation-policy-status');

            Route::get('pages/business-page/shipping-policy', 'BusinessSettingsController@shipping_policy')->name('shipping-policy');
            Route::post('pages/business-page/shipping-policy', 'BusinessSettingsController@shipping_policy_update');
            Route::get('pages/shipping-policy/{status}', 'BusinessSettingsController@shipping_policy_status')->name('shipping-policy-status');
            // Social media
            Route::get('social-media/fetch', 'SocialMediaController@fetch')->name('social-media.fetch');
            Route::get('social-media/status-update', 'SocialMediaController@social_media_status_update')->name('social-media.status-update');
            Route::resource('pages/social-media', 'SocialMediaController');


            Route::get('notification-setup', 'BusinessSettingsController@notification_setup')->name('notification_setup');
            Route::get('notification-status-change/{key}/{user_type}/{type}', 'BusinessSettingsController@notification_status_change')->name('notification_status_change');


            Route::group(['prefix' => 'file-manager', 'as' => 'file-manager.'], function () {
                Route::get('/download/{file_name}/{storage?}', 'FileManagerController@download')->name('download');
                Route::get('/index/{folder_path?}/{storage?}', 'FileManagerController@index')->name('index');
                Route::post('/image-upload', 'FileManagerController@upload')->name('image-upload');
                Route::delete('/delete/{file_path}', 'FileManagerController@destroy')->name('destroy');
            });

            // Route::group(['prefix' => 'external-system', 'as' => 'external-system.'], function () {
            //     Route::get('drivemond-configuration', 'ExternalConfigurationController@index')->name('drivemond-configuration');
            //     Route::post('update-drivemond-configuration', 'ExternalConfigurationController@updateDrivemondConfiguration')->name('update-drivemond-configuration');
            // });
            Route::group(['prefix' => 'third-party', 'as' => 'third-party.'], function () {
                Route::get('sms-module', 'SMSModuleController@sms_index')->name('sms-module');
                Route::post('sms-module-update/{sms_module}', 'SMSModuleController@sms_update')->name('sms-module-update');
                Route::get('payment-method', 'BusinessSettingsController@payment_index')->name('payment-method');
                // Route::post('payment-method-update/{payment_method}', 'BusinessSettingsController@payment_update')->name('payment-method-update');
                Route::post('payment-method-update', 'BusinessSettingsController@payment_config_update')->name('payment-method-update');
                Route::get('config-setup', 'BusinessSettingsController@config_setup')->name('config-setup');
                Route::post('config-update', 'BusinessSettingsController@config_update')->name('config-update');
                Route::get('mail-config', 'BusinessSettingsController@mail_index')->name('mail-config');
                Route::get('test-mail', 'BusinessSettingsController@test_mail')->name('test');
                Route::post('mail-config', 'BusinessSettingsController@mail_config');
                Route::post('mail-config-status', 'BusinessSettingsController@mail_config_status')->name('mail-config-status');
                Route::get('send-mail', 'BusinessSettingsController@send_mail')->name('mail.send');
                // social media login
                Route::group(['prefix' => 'social-login', 'as' => 'social-login.'], function () {
                    Route::get('view', 'BusinessSettingsController@viewSocialLogin')->name('view');
                    Route::post('update/{service}', 'BusinessSettingsController@updateSocialLogin')->name('update');
                });
                //recaptcha
                Route::get('recaptcha', 'BusinessSettingsController@recaptcha_index')->name('recaptcha_index');
                Route::post('recaptcha-update', 'BusinessSettingsController@recaptcha_update')->name('recaptcha_update');
                //firebase-otp
                Route::get('firebase-otp', 'BusinessSettingsController@firebase_otp_index')->name('firebase_otp_index');
                Route::post('firebase-otp-update', 'BusinessSettingsController@firebase_otp_update')->name('firebase_otp_update');
                //file_system
                Route::get('storage-connection', 'BusinessSettingsController@storage_connection_index')->name('storage_connection_index');
                Route::post('storage-connection-update/{name}', 'BusinessSettingsController@storage_connection_update')->name('storage_connection_update');
            });
            // Offline payment Methods
            Route::get('/offline-payment', 'OfflinePaymentMethodController@index')->name('offline');
            Route::get('/offline-payment/new', 'OfflinePaymentMethodController@create')->name('offline.new');
            Route::post('/offline-payment/store', 'OfflinePaymentMethodController@store')->name('offline.store');
            Route::get('/offline-payment/edit/{id}', 'OfflinePaymentMethodController@edit')->name('offline.edit');
            Route::post('/offline-payment/update', 'OfflinePaymentMethodController@update')->name('offline.update');
            Route::post('/offline-payment/delete', 'OfflinePaymentMethodController@delete')->name('offline.delete');
            Route::get('/offline-payment/status/{id}', 'OfflinePaymentMethodController@status')->name('offline.status');


            //db clean
            Route::get('db-index', 'DatabaseSettingController@db_index')->name('db-index');
            Route::post('db-clean', 'DatabaseSettingController@clean_db')->name('clean-db');

            Route::group(['prefix' => 'language', 'as' => 'language.'], function () {
                Route::get('', 'LanguageController@index')->name('index');
                Route::post('add-new', 'LanguageController@store')->name('add-new');
                Route::get('update-status', 'LanguageController@update_status')->name('update-status');
                Route::get('update-default-status', 'LanguageController@update_default_status')->name('update-default-status');
                Route::post('update', 'LanguageController@update')->name('update');
                Route::get('translate/{lang}', 'LanguageController@translate')->name('translate');
                Route::post('translate-submit/{lang}', 'LanguageController@translate_submit')->name('translate-submit');
                Route::post('remove-key/{lang}', 'LanguageController@translate_key_remove')->name('remove-key');
                Route::get('delete/{lang}', 'LanguageController@delete')->name('delete');
                Route::any('auto-translate/{lang}', 'LanguageController@auto_translate')->name('auto-translate');
                Route::get('auto-translate-all/{lang}', 'LanguageController@auto_translate_all')->name('auto_translate_all');

            });

            Route::get('order-cancel-reasons/status/{id}/{status}', 'OrderCancelReasonController@status')->name('order-cancel-reasons.status');
            Route::get('order-cancel-reasons/edit/{id}', 'OrderCancelReasonController@edit')->name('order-cancel-reasons.edit');
            Route::post('order-cancel-reasons/store', 'OrderCancelReasonController@store')->name('order-cancel-reasons.store');
            Route::put('order-cancel-reasons/update', 'OrderCancelReasonController@update')->name('order-cancel-reasons.update');
            Route::delete('order-cancel-reasons/destroy/{id}', 'OrderCancelReasonController@destroy')->name('order-cancel-reasons.destroy');

            Route::post('automated-message/store', 'AutomatedMessageController@store')->name('automated_message.store');
            Route::put('automated-message/update', 'AutomatedMessageController@update')->name('automated_message.update');
            Route::get('automated-message/status/{id}/{status}', 'AutomatedMessageController@status')->name('automated_message.status');
            Route::delete('automated-message/destroy/{id}', 'AutomatedMessageController@destroy')->name('automated_message.destroy');
            Route::get('automated-message/edit/{id}', 'AutomatedMessageController@edit')->name('automated_message.edit');

            Route::group(['namespace' => 'System', 'prefix' => 'system-addon', 'as' => 'system-addon.', 'middleware' => ['module:user_management']], function () {
                Route::get('/', 'AddonController@index')->name('index');
                Route::post('publish', 'AddonController@publish')->name('publish');
                Route::post('activation', 'AddonController@activation')->name('activation');
                Route::post('upload', 'AddonController@upload')->name('upload');
                Route::post('delete', 'AddonController@delete_theme')->name('delete');
            });

        });

        // Subscribed customer Routes
        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {


            Route::group(['prefix' => 'wallet', 'as' => 'wallet.', 'middleware' => ['module:customer_wallet']], function () {
                Route::get('add-fund', 'CustomerWalletController@add_fund_view')->name('add-fund');
                Route::post('add-fund', 'CustomerWalletController@add_fund');
                Route::get('report', 'CustomerWalletController@report')->name('report');
            });
            Route::group(['middleware' => ['module:customer_management']], function () {

                // Subscribed customer Routes
                Route::get('subscribed', 'CustomerController@subscribedCustomers')->name('subscribed');
                // Route::post('subscriber-search', 'CustomerController@subscriberMailSearch')->name('subscriberMailSearch');
                Route::get('subscriber-search', 'CustomerController@subscribed_customer_export')->name('subscriber-export');

                Route::get('loyalty-point/report', 'LoyaltyPointController@report')->name('loyalty-point.report');
                Route::get('settings', 'CustomerController@settings')->name('settings');
                Route::post('update-settings', 'CustomerController@update_settings')->name('update-settings');
                Route::get('export', 'CustomerController@export')->name('export');
                Route::get('order-export', 'CustomerController@customer_order_export')->name('order-export');
                Route::get('trip-export', 'CustomerController@customer_trip_export')->name('trip-export');
            });
        });
        //Pos system
        Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
            Route::post('variant_price', 'POSController@variant_price')->name('variant_price');
            Route::group(['middleware' => ['module:pos']], function () {
                Route::get('/', 'POSController@index')->name('index');
                Route::get('quick-view', 'POSController@quick_view')->name('quick-view');
                Route::post('item-stock-view', 'POSController@item_stock_view')->name('item_stock_view');
                Route::post('item-stock-view-update', 'POSController@item_stock_view_update')->name('item_stock_view_update');
                Route::get('quick-view-cart-item', 'POSController@quick_view_card_item')->name('quick-view-cart-item');
                Route::post('add-to-cart', 'POSController@addToCart')->name('add-to-cart');
                Route::post('remove-from-cart', 'POSController@removeFromCart')->name('remove-from-cart');
                Route::post('cart-items', 'POSController@cart_items')->name('cart_items');
                Route::post('single-items', 'POSController@single_items')->name('single_items');
                Route::post('update-quantity', 'POSController@updateQuantity')->name('updateQuantity');
                Route::post('empty-cart', 'POSController@emptyCart')->name('emptyCart');
                Route::post('tax', 'POSController@update_tax')->name('tax');
                Route::post('discount', 'POSController@update_discount')->name('discount');
                Route::get('customers', 'POSController@get_customers')->name('customers');
                Route::post('order', 'POSController@place_order')->name('order');
                Route::get('invoice/{id}', 'POSController@generate_invoice');
                Route::post('customer-store', 'POSController@customer_store')->name('customer-store');
                Route::post('add-delivery-address', 'POSController@addDeliveryInfo')->name('add-delivery-address');
                Route::get('data', 'POSController@extra_charge')->name('extra_charge');
                Route::get('get-user-data', 'POSController@getUserData')->name('getUserData');
            });
        });

        Route::group(['prefix' => 'reviews', 'as' => 'reviews.', 'middleware' => ['module:customer_management']], function () {
            Route::get('list', 'ReviewsController@list')->name('list');
            Route::post('search', 'ReviewsController@search')->name('search');
        });

        Route::group(['prefix' => 'report', 'as' => 'report.', 'middleware' => ['module:report']], function () {
            Route::get('order', 'ReportController@order_index')->name('order');
            Route::get('transaction-report', 'ReportController@day_wise_report')->name('transaction-report');
            Route::get('item-wise-report', 'ReportController@item_wise_report')->name('item-wise-report');
            Route::get('item-wise-export', 'ReportController@item_wise_export')->name('item-wise-export');
            Route::post('item-wise-report-search', 'ReportController@item_search')->name('item-wise-report-search');
            Route::post('day-wise-report-search', 'ReportController@day_search')->name('day-wise-report-search');
            Route::get('day-wise-report-export', 'ReportController@day_wise_export')->name('day-wise-report-export');
            Route::get('order-transactions', 'ReportController@order_transaction')->name('order-transaction');
            Route::get('earning', 'ReportController@earning_index')->name('earning');
            Route::post('set-date', 'ReportController@set_date')->name('set-date');
            Route::get('stock-report', 'ReportController@stock_report')->name('stock-report');
            Route::post('stock-report', 'ReportController@stock_search')->name('stock-search');
            Route::get('stock-wise-report-search', 'ReportController@stock_wise_export')->name('stock-wise-report-export');
            Route::get('order-report', 'ReportController@order_report')->name('order-report');
            Route::post('order-report-search', 'ReportController@search_order_report')->name('search_order_report');
            Route::get('order-report-export', 'ReportController@order_report_export')->name('order-report-export');
            Route::get('store-wise-report', 'ReportController@store_summary_report')->name('store-summary-report');
            Route::post('store-summary-report-search', 'ReportController@store_summary_search')->name('store-summary-report-search');
            Route::get('store-summary-report-export', 'ReportController@store_summary_export')->name('store-summary-report-export');
            Route::get('store-wise-sales-report', 'ReportController@store_sales_report')->name('store-sales-report');
            Route::get('store-wise-sales-report-export', 'ReportController@store_sales_export')->name('store-sales-report-export');
            Route::get('store-wise-order-report', 'ReportController@store_order_report')->name('store-order-report');
            Route::post('store-wise-order-report-search', 'ReportController@store_order_search')->name('store-order-report-search');
            Route::get('store-wise-order-report-export', 'ReportController@store_order_export')->name('store-order-report-export');
            Route::get('expense-report', 'ReportController@expense_report')->name('expense-report');
            Route::get('expense-export', 'ReportController@expense_export')->name('expense-export');
            Route::post('expense-report-search', 'ReportController@expense_search')->name('expense-report-search');
            Route::get('generate-statement/{id}', 'ReportController@generate_statement')->name('generate-statement');
        });

        Route::get('customer/select-list', 'CustomerController@get_customers')->name('customer.select-list');


        Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['module:customer_management']], function () {
            Route::get('list', 'CustomerController@customer_list')->name('list');
            Route::get('view/{user_id}', 'CustomerController@view')->name('view');
            Route::post('search', 'CustomerController@search')->name('search');
            Route::get('status/{customer}/{status}', 'CustomerController@status')->name('status');
        });


        Route::group(['prefix' => 'file-manager', 'as' => 'file-manager.'], function () {
            Route::get('/download/{file_name}/{storage?}', 'FileManagerController@download')->name('download');
            Route::get('/index/{folder_path?}//{storage?}', 'FileManagerController@index')->name('index');
            Route::post('/image-upload', 'FileManagerController@upload')->name('image-upload');
            Route::delete('/delete/{file_path}', 'FileManagerController@destroy')->name('destroy');
        });

        // social media login
        Route::group(['prefix' => 'social-login', 'as' => 'social-login.', 'middleware' => ['module:business_settings']], function () {
            Route::get('view', 'BusinessSettingsController@viewSocialLogin')->name('view');
            Route::post('update/{service}', 'BusinessSettingsController@updateSocialLogin')->name('update');
        });
        Route::group(['prefix' => 'apple-login', 'as' => 'apple-login.'], function () {
            Route::post('update/{service}', 'BusinessSettingsController@updateAppleLogin')->name('update');
        });
        Route::get('store/report', function () {
            return view('store_report');
        });

        Route::group(['prefix' => 'dispatch', 'as' => 'dispatch.'], function () {
            Route::get('/', 'DashboardController@dispatch_dashboard')->name('dashboard');
            Route::group(['middleware' => ['module:order']], function () {
                Route::get('list/{module?}/{status?}', 'OrderController@dispatch_list')->name('list');
                Route::get('parcel/list/{module?}/{status?}', 'ParcelController@parcel_dispatch_list')->name('parcel.list');
                Route::get('order/details/{id}', 'OrderController@details')->name('order.details');
                Route::get('order/generate-invoice/{id}', 'OrderController@generate_invoice')->name('order.generate-invoice');
            });
        });

        Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
            Route::get('/', 'DashboardController@user_dashboard')->name('dashboard');
            // Route::get('disbursement-export/{id}/{type}', 'DeliveryManController@disbursement_export')->name('disbursement-export');
            // Route::get('export', 'DeliveryManController@export')->name('export');

            // Subscribed customer Routes
            Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {


                Route::group(['prefix' => 'wallet', 'as' => 'wallet.', 'middleware' => ['module:customer_management']], function () {
                    Route::get('add-fund', 'CustomerWalletController@add_fund_view')->name('add-fund');
                    Route::post('add-fund', 'CustomerWalletController@add_fund');
                    Route::post('set-date', 'CustomerWalletController@set_date')->name('set-date');
                    Route::get('report', 'CustomerWalletController@report')->name('report');
                    Route::get('export', 'CustomerWalletController@export')->name('export');
                });

                Route::group(['middleware' => ['module:customer_management']], function () {

                    // Subscribed customer Routes
                    Route::get('subscribed', 'CustomerController@subscribedCustomers')->name('subscribed');
                    // Route::post('subscriber-search', 'CustomerController@subscriberMailSearch')->name('subscriberMailSearch');
                    Route::get('subscriber-search', 'CustomerController@subscribed_customer_export')->name('subscriber-export');

                    Route::get('loyalty-point/report', 'LoyaltyPointController@report')->name('loyalty-point.report');
                    Route::get('loyalty-point/export', 'LoyaltyPointController@export')->name('loyalty-point.export');
                    Route::post('loyalty-point/set-date', 'LoyaltyPointController@set_date')->name('loyalty-point.set-date');
                    Route::get('settings', 'CustomerController@settings')->name('settings');
                    Route::post('update-settings', 'CustomerController@update_settings')->name('update-settings');
                    Route::get('export', 'CustomerController@export')->name('export');
                    Route::get('order-export', 'CustomerController@customer_order_export')->name('order-export');
                });
            });
            Route::get('customer/select-list', 'CustomerController@get_customers')->name('customer.select-list');

            Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['module:customer_management']], function () {
                Route::get('list', 'CustomerController@customer_list')->name('list');
                Route::get('rental-view/{user_id}', 'CustomerController@rentalView')->name('rental.view');
                Route::get('view/{user_id}', 'CustomerController@view')->name('view');
                Route::post('search', 'CustomerController@search')->name('search');
                Route::get('status/{customer}/{status}file-manager', 'CustomerController@status')->name('status');
            });
            Route::group(['prefix' => 'contact', 'as' => 'contact.', 'middleware' => ['module:customer_management']], function () {
                Route::get('contact-list', 'ContactController@list')->name('contact-list');
                Route::get('contact-list-export', 'ContactController@exportList')->name('exportList');
                Route::delete('contact-delete/{id}', 'ContactController@destroy')->name('contact-delete');
                Route::get('contact-view/{id}', 'ContactController@view')->name('contact-view');
                Route::post('contact-update/{id}', 'ContactController@update')->name('contact-update');
                Route::post('contact-send-mail/{id}', 'ContactController@send_mail')->name('contact-send-mail');
                Route::post('contact-search', 'ContactController@search')->name('contact-search');
            });


        });
        Route::group(['prefix' => 'transactions', 'as' => 'transactions.'], function () {
            Route::get('/', 'DashboardController@transaction_dashboard')->name('dashboard');
            Route::get('order/details/{id}', 'OrderController@details')->name('order.details');
            Route::get('parcel/order/details/{id}', 'ParcelController@order_details')->name('parcel.order.details');
            Route::get('order/generate-invoice/{id}', 'OrderController@generate_invoice')->name('order.generate-invoice');
            Route::get('customer/view/{user_id}', 'CustomerController@view')->name('customer.view');
            Route::get('item/view/{id}', 'ItemController@view')->name('item.view');
            Route::group(['prefix' => 'report', 'as' => 'report.', 'middleware' => ['module:report']], function () {
                Route::get('order', 'ReportController@order_index')->name('order');
                Route::get('day-wise-report', 'ReportController@day_wise_report')->name('day-wise-report');
                Route::get('item-wise-report', 'ReportController@item_wise_report')->name('item-wise-report');
                Route::get('item-wise-export', 'ReportController@item_wise_export')->name('item-wise-export');
                Route::post('item-wise-report-search', 'ReportController@item_search')->name('item-wise-report-search');
                Route::post('day-wise-report-search', 'ReportController@day_search')->name('day-wise-report-search');
                Route::get('day-wise-report-export', 'ReportController@day_wise_export')->name('day-wise-report-export');
                Route::get('order-transactions', 'ReportController@order_transaction')->name('order-transaction');
                Route::get('earning', 'ReportController@earning_index')->name('earning');
                Route::post('set-date', 'ReportController@set_date')->name('set-date');
                Route::get('stock-report', 'ReportController@stock_report')->name('stock-report');
                Route::post('stock-report', 'ReportController@stock_search')->name('stock-search');
                Route::get('stock-wise-report-search', 'ReportController@stock_wise_export')->name('stock-wise-report-export');
                Route::get('order-report', 'ReportController@order_report')->name('order-report');
                Route::post('order-report-search', 'ReportController@search_order_report')->name('search_order_report');
                Route::get('order-report-export', 'ReportController@order_report_export')->name('order-report-export');
                Route::get('store-wise-report', 'ReportController@store_summary_report')->name('store-summary-report');
                Route::post('store-summary-report-search', 'ReportController@store_summary_search')->name('store-summary-report-search');
                Route::get('store-summary-report-export', 'ReportController@store_summary_export')->name('store-summary-report-export');
                Route::get('store-wise-sales-report', 'ReportController@store_sales_report')->name('store-sales-report');
                Route::get('store-wise-sales-report-export', 'ReportController@store_sales_export')->name('store-sales-report-export');
                Route::get('store-wise-order-report', 'ReportController@store_order_report')->name('store-order-report');
                Route::post('store-wise-order-report-search', 'ReportController@store_order_search')->name('store-order-report-search');
                Route::get('store-wise-order-report-export', 'ReportController@store_order_export')->name('store-order-report-export');
                Route::get('expense-report', 'ReportController@expense_report')->name('expense-report');
                Route::get('expense-export', 'ReportController@expense_export')->name('expense-export');
                Route::post('expense-report-search', 'ReportController@expense_search')->name('expense-report-search');
                Route::get('low-stock-report', 'ReportController@low_stock_report')->name('low-stock-report');
                Route::post('low-stock-report', 'ReportController@low_stock_search')->name('low-stock-search');
                Route::get('low-stock-wise-report-search', 'ReportController@low_stock_wise_export')->name('low-stock-wise-report-export');
                Route::get('disbursement-report/{tab?}', 'ReportController@disbursement_report')->name('disbursement_report');
                Route::get('disbursement-report-export/{type}/{tab?}', 'ReportController@disbursement_report_export')->name('disbursement_report_export');

                Route::get('vendor-wise-taxes', 'VendorTaxReportController@vendorWiseTaxes')->name('vendorWiseTaxes');
                Route::get('vendor-wise-taxes-export', 'VendorTaxReportController@vendorWiseTaxExport')->name('vendorWiseTaxExport');
                Route::get('vendor-tax-report', 'VendorTaxReportController@vendorTax')->name('vendorTax');
                Route::get('vendor-tax-export', 'VendorTaxReportController@vendorTaxExport')->name('vendorTaxExport');

                Route::get('get-tax-export', 'AdminTaxReportController@getTaxReport')->name('getTaxReport');
                Route::get('get-tax-list', 'AdminTaxReportController@getTaxList')->name('getTaxList');
                Route::get('get-tax-details', 'AdminTaxReportController@getTaxDetails')->name('getTaxDetails');
                Route::get('tax-details-report-export', 'AdminTaxReportController@adminTaxDetailsExport')->name('getTaxDetailsExport');
                Route::get('admin-tax-report-export', 'AdminTaxReportController@adminTaxReportExport')->name('adminTaxReportExport');

                Route::get('parcel-wise-taxes', 'AdminTaxReportController@parcelWiseTaxes')->name('parcel-wise-taxes');
                Route::get('parcel-wise-taxes-export', 'AdminTaxReportController@parcelWiseTaxExport')->name('parcel-wise-tax-export');

            });

            Route::group(['prefix' => 'account-transaction', 'as' => 'account-transaction.', 'middleware' => ['module:collect_cash']], function () {
                Route::get('list', 'AccountTransactionController@index')->name('index');
                Route::post('store', 'AccountTransactionController@store')->name('store');
                Route::get('details/{id}', 'AccountTransactionController@show')->name('view');
                Route::delete('delete/{id}', 'AccountTransactionController@distroy')->name('delete');
                Route::post('search', 'EmployeeController@search')->name('search');
                Route::get('export', 'AccountTransactionController@export_account_transaction')->name('export');
                Route::post('search', 'AccountTransactionController@search_account_transaction')->name('search');
            });

            Route::resource('provide-deliveryman-earnings', 'ProvideDMEarningController')->middleware('module:provide_dm_earning');
            Route::get('export-deliveryman-earnings', 'ProvideDMEarningController@dm_earning_list_export')->name('export-deliveryman-earning');
            Route::post('deliveryman-earnings-search', 'ProvideDMEarningController@search_deliveryman_earning')->name('search-deliveryman-earning');

            Route::group(['prefix' => 'store', 'as' => 'store.'], function () {
                Route::get('view/{store}/{tab?}/{sub_tab?}', 'VendorController@view')->name('view');
                Route::post('status-filter', 'VendorController@status_filter')->name('status-filter');
                Route::post('withdraw-status/{id}', 'VendorController@withdrawStatus')->name('withdraw_status');
                Route::get('withdraw_list', 'VendorController@withdraw')->name('withdraw_list');
                Route::post('withdraw_search', 'VendorController@withdraw_search')->name('withdraw_search');
                Route::get('withdraw_export', 'VendorController@withdraw_export')->name('withdraw_export');
                Route::get('withdraw-view/{withdraw_id}/{seller_id}', 'VendorController@withdraw_view')->name('withdraw_view');
                Route::get('get-Withdraw-Details', 'VendorController@getWithdrawDetails')->name('getWithdrawDetails');

            });

            Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.'], function () {
                Route::post('status-filter', [DeliveryManController::class, 'status_filter'])->name('status-filter');
                Route::post('withdraw-status/{id}', [DeliveryManController::class, 'withdrawStatus'])->name('withdraw_status');
                Route::get('withdraw_list', [DeliveryManController::class, 'withdraw_list'])->name('withdraw_list');
                Route::post('withdraw_search', [DeliveryManController::class, 'withdraw_search'])->name('withdraw_search');
                Route::get('withdraw_export', [DeliveryManController::class, 'withdraw_export'])->name('withdraw_export');
                Route::get('withdraw-view/{withdraw_id}/{seller_id}', [DeliveryManController::class, 'withdraw_view'])->name('withdraw_view');
                Route::get('get-Withdraw-Details', [DeliveryManController::class, 'getWithdrawDetails'])->name('getWithdrawDetails');

            });

            Route::group(['prefix' => 'withdraw-method', 'as' => 'withdraw-method.'], function () {
                Route::get('list', 'WithdrawalMethodController@list')->name('list');
                Route::get('create', 'WithdrawalMethodController@create')->name('create');
                Route::post('store', 'WithdrawalMethodController@store')->name('store');
                Route::get('edit/{id}', 'WithdrawalMethodController@edit')->name('edit');
                Route::put('update', 'WithdrawalMethodController@update')->name('update');
                Route::delete('delete/{id}', 'WithdrawalMethodController@delete')->name('delete');
                Route::post('status-update', 'WithdrawalMethodController@status_update')->name('status-update');
                Route::post('default-status-update', 'WithdrawalMethodController@default_status_update')->name('default-status-update');
                Route::get('get-method-info', 'WithdrawalMethodController@getMethodInfo')->name('getMethodInfo');
            });

            Route::group(['prefix' => 'store-disbursement', 'as' => 'store-disbursement.', 'middleware' => ['module:account']], function () {
                Route::get('list', 'StoreDisbursementController@list')->name('list');
                Route::get('details/{id}', 'StoreDisbursementController@view')->name('view');
                Route::get('status', 'StoreDisbursementController@status')->name('status');
                Route::get('change-status/{id}/{status}', 'StoreDisbursementController@statusById')->name('change-status');
                Route::get('export/{id}/{type?}', 'StoreDisbursementController@export')->name('export');
            });
            Route::group(['prefix' => 'dm-disbursement', 'as' => 'dm-disbursement.', 'middleware' => ['module:account']], function () {
                Route::get('list', 'DeliveryManDisbursementController@list')->name('list');
                Route::get('details/{id}', 'DeliveryManDisbursementController@view')->name('view');
                Route::get('export/{id}/{type?}', 'DeliveryManDisbursementController@export')->name('export');
                Route::get('status', 'DeliveryManDisbursementController@status')->name('status');
                Route::get('change-status/{id}/{status}', 'DeliveryManDisbursementController@statusById')->name('change-status');
                Route::get('export/{id}/{type?}', 'DeliveryManDisbursementController@export')->name('export');
            });
        });
    });
});
