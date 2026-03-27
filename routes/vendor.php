<?php

use App\Http\Controllers\Vendor\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\SubscriptionController;


Route::group(['namespace' => 'Vendor', 'as' => 'vendor.'], function () {

    Route::group(['middleware' => ['vendor', 'actch:admin_panel']], function () {

        Route::post('search-routing', 'SearchRoutingController@index')->name('search.routing');
        Route::get('recent-search', 'SearchRoutingController@recentSearch')->name('recent.search');
        Route::post('store-clicked-route', 'SearchRoutingController@storeClickedRoute')->name('store.clicked.route');

        Route::get('lang/{locale}', 'LanguageController@lang')->name('lang');
        Route::get('/', 'DashboardController@dashboard')->name('dashboard');
        Route::get('/get-store-data', 'DashboardController@store_data')->name('get-store-data');
        Route::post('/store-token', 'DashboardController@updateDeviceToken')->name('store.token');

        Route::group(['middleware' => ['module:reviews' ,'subscription:reviews']], function () {
            Route::get('/reviews', 'ReviewController@index')->name('reviews');
            Route::get('/reviews_export', 'ReviewController@reviewsExport')->name('reviewsExport');
            Route::post('/store-reply/{id}', 'ReviewController@update_reply')->name('review-reply');
        });
        Route::get('site_direction', 'BusinessSettingsController@site_direction_vendor')->name('site_direction');


        Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
            Route::post('variant_price', 'POSController@variant_price')->name('variant_price');
            Route::group(['middleware' => ['module:pos','subscription:pos' ]], function () {
                Route::get('/', 'POSController@index')->name('index');
                Route::get('quick-view', 'POSController@quick_view')->name('quick-view');
                Route::get('quick-view-cart-item', 'POSController@quick_view_card_item')->name('quick-view-cart-item');
                Route::post('add-to-cart', 'POSController@addToCart')->name('add-to-cart');
                Route::post('add-delivery-info', 'POSController@addDeliveryInfo')->name('add-delivery-info');
                Route::post('remove-from-cart', 'POSController@removeFromCart')->name('remove-from-cart');
                Route::post('cart-items', 'POSController@cart_items')->name('cart_items');
                Route::post('update-quantity', 'POSController@updateQuantity')->name('updateQuantity');
                Route::post('empty-cart', 'POSController@emptyCart')->name('emptyCart');
                Route::post('tax', 'POSController@update_tax')->name('tax');
                Route::post('paid', 'POSController@update_paid')->name('paid');
                Route::post('discount', 'POSController@update_discount')->name('discount');
                Route::get('customers', 'POSController@get_customers')->name('customers');
                Route::post('order', 'POSController@place_order')->name('order');
                Route::post('customer-store', 'POSController@customer_store')->name('customer-store');
                Route::get('data', 'POSController@extra_charge')->name('extra_charge');
            });
        });


        Route::group(['prefix' => 'subscription' , 'as' => 'subscriptionackage.' , 'middleware' => ['module:business_plan']], function () {
            Route::get('/subscriber-detail',  [SubscriptionController::class, 'subscriberDetail'])->name('subscriberDetail');
            Route::get('/invoice/{id}',  [SubscriptionController::class, 'invoice'])->name('invoice');
            Route::post('/cancel-subscription/{id}',  [SubscriptionController::class, 'cancelSubscription'])->name('cancelSubscription');
            Route::post('/switch-to-commission/{id}',  [SubscriptionController::class, 'switchToCommission'])->name('switchToCommission');
            Route::get('/package-view/{id}/{store_id}',  [SubscriptionController::class, 'packageView'])->name('packageView');
            Route::get('/subscriber-transactions/{id}',  [SubscriptionController::class, 'subscriberTransactions'])->name('subscriberTransactions');
            Route::get('/subscriber-transaction-export',  [SubscriptionController::class, 'subscriberTransactionExport'])->name('subscriberTransactionExport');
            Route::get('/subscriber-wallet-transactions',  [SubscriptionController::class, 'subscriberWalletTransactions'])->name('subscriberWalletTransactions');

            Route::post('/package-buy',  [SubscriptionController::class, 'packageBuy'])->name('packageBuy');
            Route::post('/add-to-session',  [SubscriptionController::class, 'addToSession'])->name('addToSession');
        });


        Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
            Route::post('order-stats', 'DashboardController@order_stats')->name('order-stats');
        });

        Route::group(['prefix' => 'category', 'as' => 'category.', 'middleware' => ['module:category','subscription:category']], function () {
            Route::get('get-all', 'CategoryController@get_all')->name('get-all');
            Route::get('list', 'CategoryController@index')->name('add');
            Route::get('sub-category-list', 'CategoryController@sub_index')->name('add-sub-category');
            Route::get('export-categories', 'CategoryController@export_categories')->name('export-categories');
            Route::get('export-sub-categories', 'CategoryController@export_sub_categories')->name('export-sub-categories');
        });

        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware' => ['module:role' ,'subscription:role']], function () {
            Route::get('create', 'CustomRoleController@create')->name('create');
            Route::post('create', 'CustomRoleController@store')->name('store');
            Route::get('edit/{id}', 'CustomRoleController@edit')->name('edit');
            Route::post('update/{id}', 'CustomRoleController@update')->name('update');
            Route::delete('delete/{id}', 'CustomRoleController@distroy')->name('delete');
        });

        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.'], function () {

            Route::group(['middleware' => ['module:deliveryman' ,'subscription:deliveryman']], function () {

                Route::get('add', 'DeliveryManController@index')->name('add');
                Route::post('store', 'DeliveryManController@store')->name('store');
            });

            Route::group(['middleware' => ['module:deliveryman_list' ,'subscription:deliveryman_list']], function () {
                Route::get('preview/{id}/{tab?}', 'DeliveryManController@preview')->name('preview');
                Route::get('list', 'DeliveryManController@list')->name('list');
                Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
                    Route::get('list', 'DeliveryManController@reviews_list')->name('list');
                });
                Route::get('status/{id}/{status}', 'DeliveryManController@status')->name('status');
                Route::get('earning/{id}/{status}', 'DeliveryManController@earning')->name('earning');
                Route::get('edit/{id}', 'DeliveryManController@edit')->name('edit');
                Route::post('update/{id}', 'DeliveryManController@update')->name('update');
                Route::delete('delete/{id}', 'DeliveryManController@delete')->name('delete');
                Route::get('get-deliverymen', 'DeliveryManController@get_deliverymen')->name('get-deliverymen');
                Route::post('transation/search', 'DeliveryManController@transaction_search')->name('transaction-search');
            });
        });

        Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['module:employee' ,'subscription:employee']], function () {
            Route::get('add-new', 'EmployeeController@add_new')->name('add-new');
            Route::post('add-new', 'EmployeeController@store');
            Route::get('list', 'EmployeeController@list')->name('list');
            Route::get('edit/{id}', 'EmployeeController@edit')->name('edit');
            Route::post('update/{id}', 'EmployeeController@update')->name('update');
            Route::delete('delete/{id}', 'EmployeeController@distroy')->name('delete');
            Route::get('list-export', 'EmployeeController@list_export')->name('export-employee');
        });

        Route::group(['prefix' => 'item', 'as' => 'item.', 'middleware' => ['module:item' ,'subscription:item']], function () {
            Route::get('add-new', 'ItemController@index')->name('add-new');
            Route::post('variant-combination', 'ItemController@variant_combination')->name('variant-combination');
            Route::post('store', 'ItemController@store')->name('store');
            Route::get('edit/{id}', 'ItemController@edit')->name('edit');
            Route::post('update/{id}', 'ItemController@update')->name('update');
            Route::get('list', 'ItemController@list')->name('list');
            Route::delete('delete/{id}', 'ItemController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'ItemController@status')->name('status');
            Route::post('search', 'ItemController@search')->name('search');
            Route::get('view/{id}', 'ItemController@view')->name('view');
            Route::get('remove-image', 'ItemController@remove_image')->name('remove-image');
            Route::get('get-categories', 'ItemController@get_categories')->name('get-categories');
            Route::get('recommended/{id}/{status}', 'ItemController@recommended')->name('recommended');
            Route::get('pending/item/list', 'ItemController@pending_item_list')->name('pending_item_list');
            Route::get('requested/item/view/{id}', 'ItemController@requested_item_view')->name('requested_item_view');

            Route::get('product-gallery', 'ItemController@product_gallery')->name('product_gallery');


            //Mainul
            Route::get('get-variations', 'ItemController@get_variations')->name('get-variations');
            Route::get('stock-limit-list', 'ItemController@stock_limit_list')->name('stock-limit-list');
            Route::get('get-stock', 'ItemController@get_stock')->name('get_stock');
            Route::post('stock-update', 'ItemController@stock_update')->name('stock-update');

            Route::post('food-variation-generate', 'ItemController@food_variation_generator')->name('food-variation-generate');
            Route::post('variation-generate', 'ItemController@variation_generator')->name('variation-generate');

            //Import and export
            Route::get('bulk-import', 'ItemController@bulk_import_index')->name('bulk-import');
            Route::post('bulk-import', 'ItemController@bulk_import_data');
            Route::get('bulk-export', 'ItemController@bulk_export_index')->name('bulk-export-index');
            Route::post('bulk-export', 'ItemController@bulk_export_data')->name('bulk-export');
            Route::get('flash-sale', 'ItemController@flash_sale')->name('flash_sale');

             Route::get('get-brand-list', [ItemController::class, 'getBrandList'])->name('getBrandList');

        });

        Route::group(['prefix' => 'banner', 'as' => 'banner.', 'middleware' => ['module:banner','subscription:banner']], function () {
            Route::get('list', 'BannerController@list')->name('list');
            Route::post('store', 'BannerController@store')->name('store');
            Route::get('edit/{banner}', 'BannerController@edit')->name('edit');
            Route::post('update/{banner}', 'BannerController@update')->name('update');
            Route::get('status/{id}/{status}', 'BannerController@status_update')->name('status_update');
            Route::delete('delete/{banner}', 'BannerController@delete')->name('delete');
            Route::get('join_campaign/{id}/{status}', 'BannerController@status')->name('status');
        });

        Route::group(['prefix' => 'campaign', 'as' => 'campaign.', 'middleware' => ['module:campaign','subscription:campaign']], function () {
            Route::get('list', 'CampaignController@list')->name('list');
            Route::get('item/list', 'CampaignController@itemlist')->name('itemlist');
            Route::get('remove-store/{campaign}/{store}', 'CampaignController@remove_store')->name('remove-store');
            Route::get('add-store/{campaign}/{store}', 'CampaignController@addstore')->name('add-store');
            Route::post('search-item', 'CampaignController@searchItem')->name('searchItem');
        });

        Route::group(['prefix' => 'wallet', 'as' => 'wallet.', 'middleware' => ['module:wallet' ,'subscription:wallet']], function () {
            Route::get('/', 'WalletController@index')->name('index');
            Route::post('request', 'WalletController@w_request')->name('withdraw-request');
            Route::delete('close/{id}', 'WalletController@close_request')->name('close-request');
            Route::get('method-list', 'WalletController@method_list')->name('method-list');
            Route::post('make-collected-cash-payment', 'WalletController@make_payment')->name('make_payment');
            Route::post('make-wallet-adjustment', 'WalletController@make_wallet_adjustment')->name('make_wallet_adjustment');

            Route::get('wallet-payment-list', 'WalletController@wallet_payment_list')->name('wallet_payment_list');
            Route::get('disbursement-list', 'WalletController@getDisbursementList')->name('getDisbursementList');
            Route::get('export', 'WalletController@getDisbursementExport')->name('export');

        });

        Route::group(['prefix' => 'withdraw-method', 'as' => 'wallet-method.', 'middleware' => ['module:wallet_method' ,'subscription:wallet_method' ]], function () {
            Route::get('/', 'WalletMethodController@index')->name('index');
            Route::post('store/', 'WalletMethodController@store')->name('store');
            Route::get('default/{id}/{default}', 'WalletMethodController@default')->name('default');
            Route::delete('delete/{id}', 'WalletMethodController@delete')->name('delete');
        });

        Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'middleware' => ['module:coupon','subscription:coupon']], function () {
            Route::get('add-new', 'CouponController@add_new')->name('add-new');
            Route::post('store', 'CouponController@store')->name('store');
            Route::get('update/{id}', 'CouponController@edit')->name('update');
            Route::post('update/{id}', 'CouponController@update');
            Route::get('status/{id}/{status}', 'CouponController@status')->name('status');
            Route::delete('delete/{id}', 'CouponController@delete')->name('delete');
        });

        Route::group([ 'prefix' => 'advertisement', 'as' => 'advertisement.'], function () {
            Route::group(['middleware' => ['module:advertisement' ,'subscription:advertisement']], function () {
                Route::get('create/', 'AdvertisementController@create')->name('create');
                Route::get('/copy-advertisement/{advertisement}', 'AdvertisementController@copyAdd')->name('copyAdd');
                Route::post('/copy-add-post/{advertisement}', 'AdvertisementController@copyAddPost')->name('copyAddPost');
                Route::post('store', 'AdvertisementController@store')->name('store');
            });

            Route::group(['middleware' => ['module:advertisement_list' ,'subscription:advertisement_list']], function () {
                Route::get('/', 'AdvertisementController@index')->name('index');
                Route::get('details/{advertisement}', 'AdvertisementController@show')->name('show');
                Route::get('{advertisement}/edit', 'AdvertisementController@edit')->name('edit');
                Route::put('update/{advertisement}', 'AdvertisementController@update')->name('update');
                Route::delete('delete/{id}', 'AdvertisementController@destroy')->name('destroy');
                Route::get('/status', 'AdvertisementController@status')->name('status');
            });
        });

        Route::group(['prefix' => 'addon', 'as' => 'addon.', 'middleware' => ['module:addon','subscription:addon']], function () {
            Route::get('add-new', 'AddOnController@index')->name('add-new');
            Route::post('store', 'AddOnController@store')->name('store');
            Route::get('edit/{id}', 'AddOnController@edit')->name('edit');
            Route::post('update/{id}', 'AddOnController@update')->name('update');
            Route::delete('delete/{id}', 'AddOnController@delete')->name('delete');
        });

        Route::group(['prefix' => 'order', 'as' => 'order.' , 'middleware' => ['module:order']], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::put('status-update/{id}', 'OrderController@status')->name('status-update');
            Route::post('add-to-cart', 'OrderController@add_to_cart')->name('add-to-cart');
            Route::post('remove-from-cart', 'OrderController@remove_from_cart')->name('remove-from-cart');
            Route::get('update/{order}', 'OrderController@update')->name('update');
            Route::get('edit-order/{order}', 'OrderController@edit')->name('edit');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::get('status', 'OrderController@status')->name('status');
            Route::get('quick-view', 'OrderController@quick_view')->name('quick-view');
            Route::get('quick-view-cart-item', 'OrderController@quick_view_cart_item')->name('quick-view-cart-item');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', 'OrderController@add_payment_ref_code')->name('add-payment-ref-code');
            Route::post('update-order-amount', 'OrderController@edit_order_amount')->name('update-order-amount');
            Route::post('update-discount-amount', 'OrderController@edit_discount_amount')->name('update-discount-amount');
            Route::post('add-order-proof/{id}', 'OrderController@add_order_proof')->name('add-order-proof');
            Route::get('remove-proof-image', 'OrderController@remove_proof_image')->name('remove-proof-image');
            Route::get('export-orders/{file_type}/{status}/{type}', 'OrderController@export_orders')->name('export');

        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.'], function () {
            Route::group(['middleware' => ['module:store_setup' ,'subscription:store_setup']], function () {
                Route::get('store-setup', 'BusinessSettingsController@store_index')->name('store-setup');
                Route::post('add-schedule', 'BusinessSettingsController@add_schedule')->name('add-schedule');
                Route::get('remove-schedule/{store_schedule}', 'BusinessSettingsController@remove_schedule')->name('remove-schedule');
                Route::get('update-active-status', 'BusinessSettingsController@active_status')->name('update-active-status');
                Route::post('update-setup/{store}', 'BusinessSettingsController@store_setup')->name('update-setup');
                Route::post('update-meta-data/{store}', 'BusinessSettingsController@updateStoreMetaData')->name('update-meta-data');
                Route::get('toggle-settings-status/{store}/{status}/{menu}', 'BusinessSettingsController@store_status')->name('toggle-settings');
            });

            Route::group(['middleware' => ['module:notification_setup' ,'subscription:notification_setup']], function () {
                Route::get('notification-setup', 'BusinessSettingsController@notification_index')->name('notification-setup');
                Route::get('notification-status-change/{key}/{type}', 'BusinessSettingsController@notification_status_change')->name('notification_status_change');
            });
        });
        Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['module:profile' ,'subscription:profile']], function () {
            Route::get('view', 'ProfileController@view')->name('view');
            Route::post('update', 'ProfileController@update')->name('update');
            Route::post('settings-password', 'ProfileController@settings_password_update')->name('settings-password');
        });

        Route::group(['prefix' => 'store', 'as' => 'shop.', 'middleware' => ['module:my_shop' ,'subscription:my_shop']], function () {
            Route::get('view', 'RestaurantController@view')->name('view');
            Route::get('edit', 'RestaurantController@edit')->name('edit');
            Route::post('update', 'RestaurantController@update')->name('update');
            Route::post('update-message', 'RestaurantController@update_message')->name('update-message');
        });

        Route::group(['prefix' => 'message', 'as' => 'message.', 'middleware' => ['module:chat','subscription:chat']], function () {
            Route::get('list', 'ConversationController@list')->name('list');
            Route::post('store/{user_id}/{user_type}', 'ConversationController@store')->name('store');
            Route::get('view/{conversation_id}/{user_id}', 'ConversationController@view')->name('view');
        });

        Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
            Route::post('set-date', 'ReportController@set_date')->name('set-date');
                Route::group(['middleware' => ['module:expense_report' ,'subscription:expense_report']], function () {
                    Route::get('expense-report', 'ReportController@expense_report')->name('expense-report');
                    Route::get('expense-export', 'ReportController@expense_export')->name('expense-export');
                });
                Route::group(['middleware' => ['module:disbursement_report' ,'subscription:disbursement_report']], function () {
                    Route::get('disbursement-report', 'ReportController@disbursement_report')->name('disbursement-report');
                    Route::get('disbursement-report-export/{type}', 'ReportController@disbursement_report_export')->name('disbursement-report-export');
                });
                Route::group(['middleware' => ['module:vat_report' ,'subscription:vat_report']], function () {
                    Route::get('vendor-tax-report', 'VendorTaxReportController@vendorTax')->name('vendorTax');
                    Route::get('vendor-tax-export', 'VendorTaxReportController@vendorTaxExport')->name('vendorTaxExport');
                });
        });
    });
});
