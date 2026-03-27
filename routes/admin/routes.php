<?php

use App\Enums\ViewPaths\Admin\Unit;
use App\Enums\ViewPaths\Admin\Zone;
use App\Enums\ViewPaths\Admin\Addon;
use App\Enums\ViewPaths\Admin\Brand;
use App\Enums\ViewPaths\Admin\Banner;
use App\Enums\ViewPaths\Admin\Coupon;
use App\Enums\ViewPaths\Admin\Module;
use Illuminate\Support\Facades\Route;
use App\Enums\ViewPaths\Admin\CashBack;
use App\Enums\ViewPaths\Admin\Category;
use App\Enums\ViewPaths\Admin\Employee;
use App\Enums\ViewPaths\Admin\Attribute;
use App\Enums\ViewPaths\Admin\DmVehicle;
use App\Enums\ViewPaths\Admin\CustomRole;
use App\Enums\ViewPaths\Admin\DeliveryMan;
use App\Enums\ViewPaths\Admin\WalletBonus;
use App\Enums\ViewPaths\Admin\Notification;
use App\Enums\ViewPaths\Admin\CommonCondition;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\Item\UnitController;
use App\Http\Controllers\Admin\Zone\ZoneController;
use App\Http\Controllers\Admin\Item\AddonController;
use App\Http\Controllers\Admin\Item\BrandController;
use App\Http\Controllers\Admin\Banner\BannerController;
use App\Http\Controllers\Admin\Coupon\CouponController;
use App\Http\Controllers\Admin\Item\CategoryController;
use App\Http\Controllers\Admin\Module\ModuleController;
use App\Http\Controllers\Admin\Item\AttributeController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Promotion\CashBackController;
use App\Http\Controllers\Admin\Employee\CustomRoleController;
use App\Http\Controllers\Admin\Customer\WalletBonusController;
use App\Http\Controllers\Admin\Item\CommonConditionController;
use App\Http\Controllers\Admin\DeliveryMan\DmVehicleController;
use App\Http\Controllers\Admin\DeliveryMan\DeliveryManController;
use App\Http\Controllers\Admin\Item\AddonCategoryController;
use App\Http\Controllers\Admin\Promotion\AdvertisementController;
use App\Http\Controllers\Admin\Notification\NotificationController;
use App\Http\Controllers\Admin\Subscription\SubscriptionController;
use App\Http\Controllers\Admin\SurgePriceController;

Route::group(['namespace' => 'Admin', 'as' => 'admin.'], function () {

    Route::get(Zone::GET_COORDINATES[URI] . '/{id}', [ZoneController::class, 'getCoordinates'])->name('zone.get-coordinates');
    Route::get(Zone::GET_ZONE[URI], [ZoneController::class, 'get_zone'])->name('zone.get-zone');
    Route::get('zone/check-location', [ZoneController::class, 'checkLocation'])->name('zone.check-location');

    Route::get(Zone::GET_ALL_ZONE_COORDINATES[URI] . '/{id?}', [ZoneController::class, 'getAllZoneCoordinates'])->name('zone.zoneCoordinates');

    Route::group(['middleware' => ['admin', 'current-module', 'actch:admin_panel']], function () {

        Route::post('search-routing', 'SearchRoutingController@index')->name('search.routing');
        Route::get('recent-search', 'SearchRoutingController@recentSearch')->name('recent.search');
        Route::post('store-clicked-route', 'SearchRoutingController@storeClickedRoute')->name('store.clicked.route');

        Route::get('store/get-store-ratings', [VendorController::class, 'get_store_ratings'])->name('store.get-store-ratings');
        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get(Category::NAME_LIST[URI], [CategoryController::class, 'getNameList'])->name('get-all');
            Route::group(['middleware' => ['module:category']], function () {
                Route::get(Category::ADD[URI], [CategoryController::class, 'index'])->name('add');
                Route::get(Category::UPDATE[URI] . '/{id}', [CategoryController::class, 'getUpdateView'])->name('edit');
                Route::post(Category::UPDATE[URI] . '/{id}', [CategoryController::class, 'update'])->name('update');
                Route::get(Category::PRIORITY[URI] . '/{category}', [CategoryController::class, 'updatePriority'])->name('priority');
                Route::post(Category::ADD[URI] . '/{position?}', [CategoryController::class, 'add'])->name('store');
                Route::get(Category::STATUS[URI] . '/{id}/{status}', [CategoryController::class, 'updateStatus'])->name('status');
                Route::get(Category::FEATURED[URI] . '/{id}/{featured}', [CategoryController::class, 'updateFeatured'])->name('featured');
                Route::delete(Category::DELETE[URI] . '/{id}', [CategoryController::class, 'delete'])->name('delete');
                Route::get(Category::EXPORT[URI], [CategoryController::class, 'exportList'])->name('export-categories');

                //Import and export
                Route::get(Category::BULK_IMPORT[URI], [CategoryController::class, 'getBulkImportView'])->name('bulk-import');
                Route::post(Category::BULK_IMPORT[URI], [CategoryController::class, 'importBulkData']);
                Route::post(Category::BULK_UPDATE[URI], [CategoryController::class, 'updateBulkData'])->name('bulk-update');
                Route::get(Category::BULK_EXPORT[URI], [CategoryController::class, 'getBulkExportView'])->name('bulk-export-index');
                Route::post(Category::BULK_EXPORT[URI], [CategoryController::class, 'exportBulkData'])->name('bulk-export');
            });
        });

        Route::group(['prefix' => 'attribute', 'as' => 'attribute.', 'middleware' => ['module:attribute']], function () {
            Route::get(Attribute::INDEX[URI], [AttributeController::class, 'index'])->name('add-new');
            Route::post(Attribute::ADD[URI], [AttributeController::class, 'add'])->name('store');
            Route::get(Attribute::UPDATE[URI] . '/{id}', [AttributeController::class, 'getUpdateView'])->name('edit');
            Route::post(Attribute::UPDATE[URI] . '/{id}', [AttributeController::class, 'update'])->name('update');
            Route::delete(Attribute::DELETE[URI] . '/{id}', [AttributeController::class, 'delete'])->name('delete');
            Route::get(Attribute::EXPORT[URI], [AttributeController::class, 'exportList'])->name('export-attributes');
        });

        Route::group(['prefix' => 'unit', 'as' => 'unit.', 'middleware' => ['module:unit']], function () {
            Route::get(Unit::INDEX[URI], [UnitController::class, 'index'])->name('index');
            Route::post(Unit::ADD[URI], [UnitController::class, 'add'])->name('store');
            Route::get(Unit::UPDATE[URI] . '/{id}', [UnitController::class, 'getUpdateView'])->name('edit');
            Route::put(Unit::UPDATE[URI] . '/{id}', [UnitController::class, 'update'])->name('update');
            Route::post(Unit::SEARCH[URI], [UnitController::class, 'search'])->name('search');
            Route::delete(Unit::DELETE[URI] . '/{id}', [UnitController::class, 'delete'])->name('destroy');
            Route::get(Unit::EXPORT[URI] . '/{type}', [UnitController::class, 'exportList'])->name('export');
        });

        Route::group(['prefix' => 'addon', 'as' => 'addon.', 'middleware' => ['module:addon']], function () {

            Route::get('addon-category', [AddonCategoryController::class, 'index'])->name('addon-category');
            Route::get('addon-status/{id}', [AddonCategoryController::class, 'status'])->name('addon-category-status');
            Route::get('addon-edit/{id}', [AddonCategoryController::class, 'edit'])->name('addon-category-edit');
            Route::put('addon-update/{id}', [AddonCategoryController::class, 'update'])->name('addon-category-update');
            Route::delete('addon-category/{id}', [AddonCategoryController::class, 'delete'])->name('addon-category-delete');
            Route::post('addon-category-store', [AddonCategoryController::class, 'store'])->name('addon-category-store');
            Route::get('addon-category-export', [AddonCategoryController::class, 'exportAddonCategories'])->name('addon-category-export');

            Route::get(Addon::INDEX[URI], [AddonController::class, 'index'])->name('add-new');
            Route::post(Addon::ADD[URI], [AddonController::class, 'add'])->name('store');
            Route::get(Addon::UPDATE[URI] . '/{id}', [AddonController::class, 'getUpdateView'])->name('edit');
            Route::post(Addon::UPDATE[URI] . '/{id}', [AddonController::class, 'update'])->name('update');
            Route::delete(Addon::DELETE[URI] . '/{id}', [AddonController::class, 'delete'])->name('delete');
            Route::get(Addon::EXPORT[URI], [AddonController::class, 'exportList'])->name('export');
            Route::get(Addon::UPDATE_STATUS[URI] . '/{id}/{status}', [AddonController::class, 'updateStatus'])->name('status');

            //Import and export
            Route::get(Addon::BULK_IMPORT[URI], [AddonController::class, 'getBulkImportView'])->name('bulk-import');
            Route::post(Addon::BULK_IMPORT[URI], [AddonController::class, 'importBulkData']);
            Route::post(Addon::BULK_UPDATE[URI], [AddonController::class, 'updateBulkData'])->name('bulk-update');
            Route::get(Addon::BULK_EXPORT[URI], [AddonController::class, 'getBulkExportView'])->name('bulk-export-index');
            Route::post(Addon::BULK_EXPORT[URI], [AddonController::class, 'exportBulkData'])->name('bulk-export');
        });

        Route::group(['prefix' => 'banner', 'as' => 'banner.', 'middleware' => ['module:banner']], function () {
            Route::get(Banner::INDEX[URI], [BannerController::class, 'index'])->name('add-new');
            Route::post(Banner::ADD[URI], [BannerController::class, 'add'])->name('store');
            Route::get(Banner::UPDATE[URI] . '/{id}', [BannerController::class, 'getUpdateView'])->name('edit');
            Route::post(Banner::UPDATE[URI] . '/{id}', [BannerController::class, 'update'])->name('update');
            Route::delete(Banner::DELETE[URI] . '/{id}', [BannerController::class, 'delete'])->name('delete');
            Route::get(Banner::UPDATE_STATUS[URI] . '/{id}/{status}', [BannerController::class, 'updateStatus'])->name('status');
            Route::get(Banner::UPDATE_FEATURED[URI] . '/{id}/{status}', [BannerController::class, 'updateFeatured'])->name('featured');
            Route::post(Banner::SEARCH[URI], [BannerController::class, 'getSearchList'])->name('search');
        });

        Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'middleware' => ['module:coupon']], function () {
            Route::get(Coupon::INDEX[URI], [CouponController::class, 'index'])->name('add-new');
            Route::post(Coupon::ADD[URI], [CouponController::class, 'add'])->name('store');
            Route::get(Coupon::UPDATE[URI] . '/{id}', [CouponController::class, 'getUpdateView'])->name('edit');
            Route::post(Coupon::UPDATE[URI] . '/{id}', [CouponController::class, 'update'])->name('update');
            Route::delete(Coupon::DELETE[URI] . '/{id}', [CouponController::class, 'delete'])->name('delete');
            Route::get(Coupon::STATUS[URI] . '/{id}/{status}', [CouponController::class, 'updateStatus'])->name('status');
            Route::get(Coupon::EXPORT[URI], [CouponController::class, 'exportList'])->name('coupon_export');
            Route::get('view/{id}', [CouponController::class, 'viewCoupon'])->name('viewCoupon');
        });

        Route::group(['prefix' => 'notification', 'as' => 'notification.', 'middleware' => ['module:notification']], function () {
            Route::get(Notification::INDEX[URI], [NotificationController::class, 'index'])->name('add-new');
            Route::post(Notification::ADD[URI], [NotificationController::class, 'add'])->name('store');
            Route::get(Notification::UPDATE[URI] . '/{id}', [NotificationController::class, 'getUpdateView'])->name('edit');
            Route::post(Notification::UPDATE[URI] . '/{id}', [NotificationController::class, 'update'])->name('update');
            Route::delete(Notification::DELETE[URI] . '/{id}', [NotificationController::class, 'delete'])->name('delete');
            Route::get(Notification::STATUS[URI] . '/{id}/{status}', [NotificationController::class, 'updateStatus'])->name('status');
            Route::get(Notification::EXPORT[URI], [NotificationController::class, 'exportList'])->name('export');
        });

        Route::group(['prefix' => 'common-condition', 'as' => 'common-condition.'], function () {
            Route::get(CommonCondition::DROPDOWN[URI], [CommonConditionController::class, 'getDropdownList'])->name('get-all');
            Route::get(CommonCondition::INDEX[URI], [CommonConditionController::class, 'index'])->name('add');
            Route::post(CommonCondition::ADD[URI], [CommonConditionController::class, 'add'])->name('store');
            Route::get(CommonCondition::UPDATE[URI] . '/{id}', [CommonConditionController::class, 'getUpdateView'])->name('edit');
            Route::post(CommonCondition::UPDATE[URI] . '/{id}', [CommonConditionController::class, 'update'])->name('update');
            Route::delete(CommonCondition::DELETE[URI] . '/{id}', [CommonConditionController::class, 'delete'])->name('delete');
            Route::get(CommonCondition::STATUS[URI] . '/{id}/{status}', [CommonConditionController::class, 'updateStatus'])->name('status');
        });

        Route::group(['prefix' => 'brand', 'as' => 'brand.'], function () {
            Route::get(Brand::DROPDOWN[URI], [BrandController::class, 'getDropdownList'])->name('get-all');
            Route::get(Brand::INDEX[URI], [BrandController::class, 'index'])->name('add');
            Route::post(Brand::ADD[URI], [BrandController::class, 'add'])->name('store');
            Route::post(Brand::UPDATE[URI] . '/{id}', [BrandController::class, 'update'])->name('update');
            Route::delete(Brand::DELETE[URI] . '/{id}', [BrandController::class, 'delete'])->name('delete');
            Route::get(Brand::STATUS[URI] . '/{id}/{status}', [BrandController::class, 'updateStatus'])->name('status');
            Route::post('module-upadte', [BrandController::class, 'moduleUpadte'])->name('moduleUpadte');
            Route::get('get-brand-data', [BrandController::class, 'getBrandData'])->name('getBrandData');
        });


        Route::group(['prefix' => 'advertisement', 'as' => 'advertisement.', 'middleware' => ['module:advertisement']], function () {

            Route::get('/', [AdvertisementController::class, 'index'])->name('index');
            Route::get('create/', [AdvertisementController::class, 'create'])->name('create');
            Route::get('details/{advertisement}', [AdvertisementController::class, 'show'])->name('show');
            Route::get('{advertisement}/edit', [AdvertisementController::class, 'edit'])->name('edit');
            Route::post('store', [AdvertisementController::class, 'store'])->name('store');
            Route::put('update/{advertisement}', [AdvertisementController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [AdvertisementController::class, 'destroy'])->name('destroy');

            Route::get('/status', [AdvertisementController::class, 'status'])->name('status');
            Route::get('/paidStatus', [AdvertisementController::class, 'paidStatus'])->name('paidStatus');
            Route::get('/priority', [AdvertisementController::class, 'priority'])->name('priority');
            Route::get('/requests', [AdvertisementController::class, 'requestList'])->name('requestList');
            Route::get('/copy-advertisement/{advertisement}', [AdvertisementController::class, 'copyAdd'])->name('copyAdd');
            Route::get('/updateDate/{advertisement}', [AdvertisementController::class, 'updateDate'])->name('updateDate');
            Route::post('/copy-add-post/{advertisement}', [AdvertisementController::class, 'copyAddPost'])->name('copyAddPost');

        });


        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.'], function () {

            Route::group(['prefix' => 'subscription', 'middleware' => ['module:subscription']], function () {

                Route::resource('subscriptionackage', SubscriptionController::class);
                Route::get('/status/{subscriptionackage}', [SubscriptionController::class, 'statusChange'])->name('subscriptionackage.status');
                Route::get('/overView/{subscriptionackage}', [SubscriptionController::class, 'overView'])->name('subscriptionackage.overView');
                Route::get('/transaction/{subscriptionackage}', [SubscriptionController::class, 'transaction'])->name('subscriptionackage.transaction');
                Route::get('/settings', [SubscriptionController::class, 'settings'])->name('subscriptionackage.settings');
                Route::get('/trial-status', [SubscriptionController::class, 'trialStatus'])->name('subscriptionackage.trialStatus');
                Route::post('/setting-update', [SubscriptionController::class, 'settingUpdate'])->name('subscriptionackage.settingUpdate');
                Route::get('/invoice/{id}', [SubscriptionController::class, 'invoice'])->name('subscriptionackage.invoice');
                Route::post('/switch-plan', [SubscriptionController::class, 'switchPlan'])->name('subscriptionackage.switchPlan');
                Route::get('/package-export', [SubscriptionController::class, 'packageExport'])->name('subscriptionackage.packageExport');
                Route::get('/transaction-export', [SubscriptionController::class, 'TransactionExport'])->name('subscriptionackage.TransactionExport');

                Route::get('/subscriber-list', [SubscriptionController::class, 'subscriberList'])->name('subscriptionackage.subscriberList');
                Route::get('/subscriber-list-export', [SubscriptionController::class, 'subscriberListExport'])->name('subscriptionackage.subscriberListExport');
                Route::get('/subscriber-transaction-export', [SubscriptionController::class, 'subscriberTransactionExport'])->name('subscriptionackage.subscriberTransactionExport');
                Route::post('/cancel-subscription/{id}', [SubscriptionController::class, 'cancelSubscription'])->name('subscriptionackage.cancelSubscription');
                Route::post('/switch-to-commission/{id}', [SubscriptionController::class, 'switchToCommission'])->name('subscriptionackage.switchToCommission');
                Route::get('/subscriber-detail/{id}', [SubscriptionController::class, 'subscriberDetail'])->name('subscriptionackage.subscriberDetail');
                Route::get('/package-view/{id}/{store_id}', [SubscriptionController::class, 'packageView'])->name('subscriptionackage.packageView');
                Route::get('/subscriber-transactions/{id}', [SubscriptionController::class, 'subscriberTransactions'])->name('subscriptionackage.subscriberTransactions');
                Route::get('/subscriber-wallet-transactions/{id}', [SubscriptionController::class, 'subscriberWalletTransactions'])->name('subscriptionackage.subscriberWalletTransactions');

                Route::post('/package-buy', [SubscriptionController::class, 'packageBuy'])->name('subscriptionackage.packageBuy');
            });




            Route::group(['prefix' => 'zone', 'as' => 'zone.', 'middleware' => ['module:zone']], function () {
                Route::get(Zone::INDEX[URI], [ZoneController::class, 'index'])->name('home');
                Route::post(Zone::ADD[URI], [ZoneController::class, 'add'])->name('store');
                Route::get(Zone::UPDATE[URI] . '/{id}', [ZoneController::class, 'getUpdateView'])->name('edit');
                Route::post(Zone::UPDATE[URI] . '/{id}', [ZoneController::class, 'update'])->name('update');
                Route::delete(Zone::DELETE[URI] . '/{id}', [ZoneController::class, 'delete'])->name('delete');
                Route::get(Zone::EXPORT[URI] . '/{type}', [ZoneController::class, 'exportList'])->name('export');
                Route::get(Zone::STATUS[URI] . '/{id}/{status}', [ZoneController::class, 'updateStatus'])->name('status');
                Route::get(Zone::ZONE_FILTER[URI] . '/{id}', [ZoneController::class, 'zoneFilter'])->name('zone-filter');
                Route::get(Zone::LATEST_MODULE_SETUP[URI], [ZoneController::class, 'getLatestModuleSetupView'])->name('go-module-setup');
                Route::get(Zone::MODULE_SETUP[URI] . '/{id?}', [ZoneController::class, 'getModuleSetupView'])->name('module-setup');
                Route::post(Zone::MODULE_UPDATE[URI] . '/{id}', [ZoneController::class, 'updateModuleSetup'])->name('module-update');
                Route::get(Zone::INSTRUCTION[URI], [ZoneController::class, 'getInstruction'])->name('instruction');
                Route::get(Zone::DIGITAL_PAYMENT[URI] . '/{id}/{digital_payment}', [ZoneController::class, 'updateDigitalPayment'])->name('digital-payment');
                Route::get(Zone::CASH_ON_DELIVERY[URI] . '/{id}/{cash_on_delivery}', [ZoneController::class, 'updateCashOnDelivery'])->name('cash-on-delivery');
                Route::get(Zone::OFFLINE_PAYMENT[URI] . '/{id}/{offline_payment}', [ZoneController::class, 'updateOfflinePayment'])->name('offline-payment');
                Route::get('default-status/{id}', [ZoneController::class, 'defaultStatus'])->name('default-status');

                Route::group(['prefix' => 'surge-price', 'as' => 'surge-price.', 'middleware' => ['module:zone']], function () {
                    Route::get('/{zone_id}', [SurgePriceController::class, 'index'])->name('list');
                    Route::get('create/{zone_id}', [SurgePriceController::class, 'create'])->name('create');
                    Route::post('store', [SurgePriceController::class, 'store'])->name('store');
                    Route::get('status/{id}/{status}', [SurgePriceController::class, 'status'])->name('status');
                    Route::get('edit/{id}', [SurgePriceController::class, 'edit'])->name('edit');
                    Route::post('update/{id}', [SurgePriceController::class, 'update'])->name('update');
                    Route::delete('delete/{id}', [SurgePriceController::class, 'destroy'])->name('delete');
                });
            });

            Route::group(['prefix' => 'module', 'as' => 'module.', 'middleware' => ['module:module']], function () {
                Route::get(Module::INDEX[URI], [ModuleController::class, 'index'])->name('index');
                Route::get(Module::ADD[URI], [ModuleController::class, 'getAddView'])->name('create');
                Route::post(Module::ADD[URI], [ModuleController::class, 'add'])->name('store');
                Route::get(Module::UPDATE[URI] . '/{id}', [ModuleController::class, 'getUpdateView'])->name('edit');
                Route::put(Module::UPDATE[URI] . '/{id}', [ModuleController::class, 'update'])->name('update');
                Route::get(Module::STATUS[URI] . '/{id}/{status}', [ModuleController::class, 'updateStatus'])->name('status');
                Route::get(Module::TYPE[URI], [ModuleController::class, 'getType'])->name('type');
                Route::post(Module::SEARCH[URI], [ModuleController::class, 'search'])->name('search');
                Route::get(Module::EXPORT[URI], [ModuleController::class, 'exportList'])->name('export');
                Route::get(Module::SHOW[URI] . '/{id}', [ModuleController::class, 'show'])->name('show')->withoutMiddleware('module:module');
            });
        });

        Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
            Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware' => ['module:custom_role']], function () {
                Route::get(CustomRole::ADD[URI], [CustomRoleController::class, 'index'])->name('create');
                Route::post(CustomRole::ADD[URI], [CustomRoleController::class, 'add'])->name('create');
                Route::get(CustomRole::EDIT[URI] . '/{id}', [CustomRoleController::class, 'getUpdateView'])->name('edit');
                Route::post(CustomRole::UPDATE[URI] . '/{id}', [CustomRoleController::class, 'update'])->name('update');
                Route::delete(CustomRole::DELETE[URI] . '/{id}', [CustomRoleController::class, 'delete'])->name('delete');
                Route::post(CustomRole::SEARCH[URI], [CustomRoleController::class, 'search'])->name('search');
            });

            Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['module:employee']], function () {
                Route::get(Employee::INDEX[URI], [EmployeeController::class, 'index'])->name('list');
                Route::get(Employee::ADD[URI], [EmployeeController::class, 'getAddView'])->name('add-new');
                Route::post(Employee::ADD[URI], [EmployeeController::class, 'add'])->name('add-new');
                Route::get(Employee::UPDATE[URI] . '/{id}', [EmployeeController::class, 'getUpdateView'])->name('edit');
                Route::post(Employee::UPDATE[URI] . '/{id}', [EmployeeController::class, 'update'])->name('update');
                Route::delete(Employee::DELETE[URI] . '/{id}', [EmployeeController::class, 'delete'])->name('delete');
                Route::post(Employee::SEARCH[URI], [EmployeeController::class, 'getSearchList'])->name('search');
                Route::get(Employee::EXPORT[URI], [EmployeeController::class, 'exportList'])->name('export');
            });

            // customer routes
            Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
                Route::group(['prefix' => 'wallet', 'as' => 'wallet.', 'middleware' => ['module:customer_management']], function () {
                    Route::group(['prefix' => 'bonus', 'as' => 'bonus.'], function () {
                        Route::get(WalletBonus::INDEX[URI], [WalletBonusController::class, 'index'])->name('add-new');
                        Route::post(WalletBonus::ADD[URI], [WalletBonusController::class, 'add'])->name('store');
                        Route::get(WalletBonus::UPDATE[URI] . '/{id}', [WalletBonusController::class, 'getUpdateView'])->name('edit');
                        Route::post(WalletBonus::UPDATE[URI] . '/{id}', [WalletBonusController::class, 'update'])->name('update');
                        Route::delete(WalletBonus::DELETE[URI] . '/{id}', [WalletBonusController::class, 'delete'])->name('delete');
                        Route::get(WalletBonus::UPDATE_STATUS[URI] . '/{id}/{status}', [WalletBonusController::class, 'updateStatus'])->name('status');
                        Route::post(WalletBonus::SEARCH[URI], [WalletBonusController::class, 'getSearchList'])->name('search');
                    });
                });
            });

            Route::group(['prefix' => 'cashback', 'as' => 'cashback.', 'middleware' => ['module:cashback']], function () {
                Route::get(CashBack::INDEX[URI], [CashBackController::class, 'index'])->name('add-new');
                Route::post(CashBack::ADD[URI], [CashBackController::class, 'add'])->name('store');
                Route::get(CashBack::UPDATE[URI] . '/{id}', [CashBackController::class, 'getUpdateView'])->name('edit');
                Route::post(CashBack::UPDATE[URI] . '/{id}', [CashBackController::class, 'update'])->name('update');
                Route::delete(CashBack::DELETE[URI] . '/{id}', [CashBackController::class, 'delete'])->name('delete');
                Route::get(CashBack::UPDATE_STATUS[URI] . '/{id}/{status}', [CashBackController::class, 'updateStatus'])->name('status');
                // Route::post(CashBack::SEARCH[URI], [CashBackController::class,'getSearchList'])->name('search');
            });

            // delivery man routes
            Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.'], function () {
                Route::get(DeliveryMan::DROPDOWN_LIST[URI], [DeliveryManController::class, 'getDropdownList'])->name('get-deliverymen');
                Route::get(DeliveryMan::ACCOUNT_DATA[URI] . '/{id}', [DeliveryManController::class, 'getAccountData'])->name('store-filter');

                Route::group(['middleware' => ['module:deliveryman']], function () {
                    Route::get(DeliveryMan::ADD[URI], [DeliveryManController::class, 'getAddView'])->name('add');
                    Route::post(DeliveryMan::ADD[URI], [DeliveryManController::class, 'add'])->name('store');
                    Route::get(DeliveryMan::LIST [URI], [DeliveryManController::class, 'index'])->name('list');
                    Route::get(DeliveryMan::NEW [URI], [DeliveryManController::class, 'getNewDeliveryManView'])->name('new');
                    Route::get(DeliveryMan::DENY[URI], [DeliveryManController::class, 'getDeniedDeliveryManView'])->name('deny');
                    Route::get(DeliveryMan::PREVIEW[URI] . '/{id}/{tab?}', [DeliveryManController::class, 'getPreview'])->name('preview');
                    Route::get(DeliveryMan::STATUS[URI] . '/{id}/{status}', [DeliveryManController::class, 'updateStatus'])->name('status');
                    Route::get(DeliveryMan::EARNING[URI] . '/{id}/{status}', [DeliveryManController::class, 'updateEarning'])->name('earning');
                    Route::get(DeliveryMan::UPDATE_APPLICATION[URI] . '/{id}/{status}', [DeliveryManController::class, 'updateApplication'])->name('application');
                    Route::get(DeliveryMan::UPDATE[URI] . '/{id}', [DeliveryManController::class, 'getUpdateView'])->name('edit');
                    Route::post(DeliveryMan::UPDATE[URI] . '/{id}', [DeliveryManController::class, 'update'])->name('update');
                    Route::delete(DeliveryMan::DELETE[URI] . '/{id}', [DeliveryManController::class, 'delete'])->name('delete');
                    Route::post(DeliveryMan::SEARCH[URI], [DeliveryManController::class, 'getSearchList'])->name('search');
                    Route::post(DeliveryMan::ACTIVE_SEARCH[URI], [DeliveryManController::class, 'getActiveSearchList'])->name('active-search');
                    Route::get(DeliveryMan::EXPORT[URI], [DeliveryManController::class, 'exportList'])->name('export');
                    Route::get(DeliveryMan::EARNING_EXPORT[URI], [DeliveryManController::class, 'getEarningListExport'])->name('earning-export');
                    Route::get(DeliveryMan::REVIEW_EXPORT[URI], [DeliveryManController::class, 'getReviewExportList'])->name('review-export');
                    Route::get(DeliveryMan::LOYALTY_POINT_EXPORT[URI], [DeliveryManController::class, 'getLoyaltyPointExportList'])->name('loyalty-point-export');
                    Route::get(DeliveryMan::REFERRAL_EXPORT[URI], [DeliveryManController::class, 'getReferralEarnExportList'])->name('referral-export');
                    Route::get('disbursement-export/{id}/{type}', [DeliveryManController::class, 'disbursement_export'])->name('disbursement-export');

                    Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
                        Route::get(DeliveryMan::REVIEW_LIST[URI], [DeliveryManController::class, 'getReviewListView'])->name('list');
                        Route::post(DeliveryMan::REVIEW_SEARCH_LIST[URI], [DeliveryManController::class, 'getReviewSearchList'])->name('search');
                        Route::get(DeliveryMan::REVIEW_STATUS[URI] . '/{id}/{status}', [DeliveryManController::class, 'updateReviewStatus'])->name('status');
                        Route::get(DeliveryMan::EXPORT[URI], [DeliveryManController::class, 'getAllReviewExportList'])->name('export');
                    });

                    // message
                    Route::get(DeliveryMan::CONVERSATION_VIEW[URI] . '/{conversation_id}/{user_id}', [DeliveryManController::class, 'getConversationView'])->name('message-view');
                    Route::get(DeliveryMan::CONVERSATION_DETAILS[URI], [DeliveryManController::class, 'getConversationList'])->name('message-list-search');

                    Route::group(['prefix' => 'vehicle', 'as' => 'vehicle.'], function () {
                        Route::get(DmVehicle::INDEX[URI], [DmVehicleController::class, 'index'])->name('list');
                        Route::get(DmVehicle::ADD[URI], [DmVehicleController::class, 'getAddView'])->name('create');
                        Route::post(DmVehicle::ADD[URI], [DmVehicleController::class, 'add'])->name('store');
                        Route::get(DmVehicle::UPDATE[URI] . '/{id}', [DmVehicleController::class, 'getUpdateView'])->name('edit');
                        Route::post(DmVehicle::UPDATE[URI] . '/{id}', [DmVehicleController::class, 'update'])->name('update');
                        Route::delete(DmVehicle::DELETE[URI] . '/{id}', [DmVehicleController::class, 'delete'])->name('delete');
                        Route::get(DmVehicle::UPDATE_STATUS[URI] . '/{id}/{status}', [DmVehicleController::class, 'updateStatus'])->name('status');
                        Route::get(DmVehicle::VIEW[URI] . '/{id}', [DmVehicleController::class, 'getDetailsView'])->name('view');
                    });
                });
            });
        });
    });
});
