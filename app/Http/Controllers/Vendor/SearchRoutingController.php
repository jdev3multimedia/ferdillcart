<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Item;
use App\Models\AddOn;
use App\Models\Order;
use App\Models\Store;
use App\Models\Banner;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Expense;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\DeliveryMan;
use App\Models\TempProduct;
use Illuminate\Support\Str;
use App\Models\EmployeeRole;
use App\Models\ItemCampaign;
use App\Models\RecentSearch;
use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Models\FlashSaleItem;
use App\CentralLogics\Helpers;
use App\Models\VendorEmployee;
use App\Models\WithdrawRequest;
use App\Models\StoreSubscription;
use Illuminate\Http\JsonResponse;
use App\Models\AccountTransaction;
use Modules\Rental\Entities\Trips;
use App\Models\DisbursementDetails;
use App\Http\Controllers\Controller;
use Modules\Rental\Entities\Vehicle;
use Illuminate\Support\Facades\Route;
use App\Models\SubscriptionTransaction;
use Modules\Rental\Entities\VehicleBrand;
use Modules\Rental\Entities\VehicleDriver;
use Modules\Rental\Entities\VehicleReview;
use App\Models\DisbursementWithdrawalMethod;
use Modules\Rental\Entities\VehicleCategory;
use App\Models\SubscriptionBillingAndRefundHistory;

class SearchRoutingController extends Controller
{
    public function index(Request $request)
    {
        $store_data = Helpers::get_store_data();
        $vendor_id = $store_data->vendor_id;
        $store_id =  $store_data->id;
        $moduleType = $store_data->module_type;
        $module_id = $store_data->module_id;
        $searchKeyword = $request->input('search');
        $userType = auth('vendor')->check() ? 'vendor' : 'vendor-employee';

        session(['search_keyword' => $searchKeyword]);

        //1st layer
        $formattedRoutes = [];
        $jsonFilePath = public_path('vendor_formatted_routes.json');
        if (file_exists($jsonFilePath)) {
            $fileContents = file_get_contents($jsonFilePath);
            $routes = json_decode($fileContents, true);

            if (!addon_published_status('Rental')) {
                $routes = array_filter($routes, function ($route) {
                    return $route['moduleType'] !== 'rental';
                });
            }



            foreach ($routes as $route) {
                $uri = $route['URI'];
                if (Str::contains(strtolower($route['keywords']), strtolower($searchKeyword))) {
                    $hasParameters = preg_match('/\{(.*?)\}/', $uri);

                    $fullURL = $this->routeFullUrl($uri);

                    if (!$hasParameters) {
                        if ($moduleType === $route['moduleType'] || $route['moduleType'] === null) {

                            $routeName = $route['routeName'];
                            $routeName = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $routeName);
                            $routeName = trim(preg_replace('/\s+/', ' ', $routeName));

                            $formattedRoutes[] = [
                                'routeName' => ucwords($routeName),
                                'URI' => $uri,
                                'fullRoute' => $fullURL,
                            ];
                        }
                    }
                }
            }
        }

        //2nd layer
        $routes = Route::getRoutes();
        $storeRoutes = collect($routes->getRoutesByMethod()['GET'])->filter(function ($route) {
            return str_starts_with($route->uri(), 'vendor-panel');
        });
        $validRoutes = [];
        if (is_numeric($searchKeyword) &&   $searchKeyword > 0) {

            //order

            if ($moduleType !== 'rental') {

                $order = Order::where('store_id', $store_id)->Notpos()
                    ->NotDigitalOrder()->find($searchKeyword);

                if ($order) {
                    $orderRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'order/details') || str_contains($route->uri(), 'expense-report') && !str_contains($route->uri(), 'pos');
                    });
                    if (isset($orderRoutes)) {
                        foreach ($orderRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $order, route: $route, type: 'order', prefix: 'Order', name: $order->id, searchKeyword: $order->id);
                        }
                    }
                }


                //     category
                $category = Category::where(['module_id' => $module_id, 'position' => 0])->find($searchKeyword);
                if ($category) {
                    $categoryRoutes = $storeRoutes->filter(function ($route) {
                        return  !str_contains($route->uri(), '-category/list') &&  (str_contains($route->uri(), 'category/list') && !str_contains($route->uri(), 'category/sub-category-list'));
                    });

                    if (isset($categoryRoutes)) {
                        foreach ($categoryRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $category, route: $route, type: 'category', prefix: 'Category', name: $category->name, searchKeyword: $category->name);
                        }
                    }
                }
                //     Sub category
                $category = Category::where(['module_id' => $module_id, 'position' => 1])->find($searchKeyword);
                if ($category) {
                    $categoryRoutes = $storeRoutes->filter(function ($route) {
                        return   str_contains($route->uri(), 'category/sub-category-list');
                    });

                    if (isset($categoryRoutes)) {
                        foreach ($categoryRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $category, route: $route, type: 'category', prefix: 'Sub Category', name: $category->name, searchKeyword: $category->name);
                        }
                    }
                }


                if ($moduleType == 'food') {
                    //AddOn
                    $addOn = AddOn::where('store_id', $store_id)->find($searchKeyword);
                    if ($addOn) {
                        $addOnRoutes = $storeRoutes->filter(function ($route) {
                            return str_contains($route->uri(), 'addon/edit');
                        });

                        if (isset($addOnRoutes)) {
                            foreach ($addOnRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $addOn, route: $route, prefix: 'Addon', name: $addOn->name);
                            }
                        }
                    }
                }

                //food
                $Item = Item::where('store_id', $store_id)->find($searchKeyword);
                if ($Item) {
                    $ItemRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'item/view')
                            || str_contains($route->uri(), 'item/edit')) &&  !str_contains($route->uri(), 'item/requested');
                    });

                    if (isset($ItemRoutes)) {
                        foreach ($ItemRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $Item, route: $route, type: 'Item', prefix: 'Item', name: $Item->name);
                        }
                    }
                }

                //Temp food
                $Tempfood = TempProduct::where('store_id', $store_id)->find($searchKeyword);
                if ($Tempfood) {
                    $TempfoodRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'item/requested/item/view'));
                    });

                    if (isset($TempfoodRoutes)) {
                        foreach ($TempfoodRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $Tempfood, route: $route, type: 'tempitem', name: $Tempfood->name);
                        }
                    }
                }

                //item campaign
                $itemCampaign = ItemCampaign::where('store_id', $store_id)->find($searchKeyword);
                if ($itemCampaign) {
                    $itemCampaignRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'campaign/item/list');
                    });

                    if (isset($itemCampaignRoutes)) {
                        foreach ($itemCampaignRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $itemCampaign, route: $route, type: 'item-campaign', prefix: 'Campaign', name: $itemCampaign->title, searchKeyword: $itemCampaign->title);
                        }
                    }
                }

                //basic campaign
                $campaign = Campaign::running()->active()->module($module_id)

                    ->find($searchKeyword);
                if ($campaign) {
                    $campaignRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'campaign/list');
                    });

                    if (isset($campaignRoutes)) {
                        foreach ($campaignRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $campaign, route: $route, type: 'campaign', prefix: 'Campaign', name: $campaign->title, searchKeyword: $campaign->title);
                        }
                    }
                }


                if ($store_data->sub_self_delivery) {
                    //delivery man
                    $deliveryMan = DeliveryMan::where('store_id', $store_id)->find($searchKeyword);
                    if ($deliveryMan) {
                        $deliveryManRoutes = $storeRoutes->filter(function ($route) {
                            return str_contains($route->uri(), 'delivery-man')
                                && str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'preview');
                        });
                        if (isset($deliveryManRoutes)) {
                            foreach ($deliveryManRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $deliveryMan, route: $route, type: 'deliveryMan', name: $deliveryMan->f_name . ' ' . $deliveryMan->l_name, prefix: 'Delivery Man');
                            }
                        }
                    }
                }


                //reviews
                $review = Review::whereHas('item', function ($query) use ($store_id) {
                    return $query->where('store_id', $store_id);
                })->find($searchKeyword);

                if ($review) {
                    $reviewsRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'vendor-panel/review') && !str_contains($route->uri(), 'rental') && !str_contains($route->uri(), 'export');
                    });

                    if (isset($reviewsRoutes)) {
                        foreach ($reviewsRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $review, route: $route, prefix: 'reviews', searchKeyword: $review?->item?->name);
                        }
                    }
                }
            }


            //coupon
            $coupon = Coupon::where('created_by', 'vendor')->where('store_id', $store_id)->find($searchKeyword);
            if ($coupon) {

                if ($moduleType == 'rental') {

                    $couponRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental-coupon') && (!str_contains($route->uri(), 'status') && !str_contains($route->uri(), 'export'));
                    });
                } else {

                    $couponRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'coupon/add-new') || str_contains($route->uri(), 'coupon/update') && (!str_contains($route->uri(), 'status') && !str_contains($route->uri(), 'export') && !str_contains($route->uri(), 'rental'));
                    });
                }

                if (isset($couponRoutes)) {
                    foreach ($couponRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $coupon, route: $route, prefix: 'Coupon', name: $coupon->title, searchKeyword: $coupon->title);
                    }
                }
            }


            //banner
            $banner = Banner::where('data', $store_id)->where('created_by', 'store')->find($searchKeyword);
            if ($banner) {
                if ($moduleType == 'rental') {
                    $bannerRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'rental-banner') ||  str_contains($route->uri(), 'banner/edit')) && !str_contains($route->uri(), 'status') &&  str_contains($route->uri(), 'rental') && !str_contains($route->uri(), 'export');
                    });
                } else {
                    $bannerRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'banner/list') ||  str_contains($route->uri(), 'banner/edit')) && !str_contains($route->uri(), 'status') &&  !str_contains($route->uri(), 'rental') &&  !str_contains($route->uri(), 'export');
                    });
                }

                if (isset($bannerRoutes)) {
                    foreach ($bannerRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $banner, route: $route, prefix: 'Banner', name: $banner->title, searchKeyword: $banner->title);
                    }
                }
            }






            //Advertisement
            $ads = Advertisement::where('store_id', $store_id)->find($searchKeyword);
            if ($ads) {
                $adsRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'advertisement')
                        && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'detail'));
                });
                if (isset($adsRoutes)) {
                    foreach ($adsRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $ads, route: $route, prefix: 'Advertisement');
                    }
                }
            }






            //store disbursement
            $storeDisbursement = DisbursementDetails::where('store_id', $store_id)->where('disbursement_id', $searchKeyword)->first();
            if ($storeDisbursement) {
                $storeDisbursementRoutes = $storeRoutes->filter(function ($route) {
                    return (str_contains($route->uri(), 'disbursement-report') || str_contains($route->uri(), 'wallet/disbursement-list')) && !str_contains($route->uri(), 'disbursement-report-export');
                });

                if (isset($storeDisbursementRoutes)) {
                    foreach ($storeDisbursementRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $storeDisbursement, route: $route, name: $storeDisbursement->disbursement_id);
                    }
                }
            }




            //withdraw requests
            $withdrawRequest = WithdrawRequest::where('vendor_id', $vendor_id)->find($searchKeyword);
            if ($withdrawRequest) {
                $withdrawRequestRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'wallet') && !str_contains($route->uri(), 'disbursement-list')  && !str_contains($route->uri(), 'wallet-payment-list') && !str_contains($route->uri(), 'method-list') && !str_contains($route->uri(), 'export')  && !str_contains($route->uri(), 'subscription');
                });

                if (isset($withdrawRequestRoutes)) {
                    foreach ($withdrawRequestRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $withdrawRequest, route: $route, prefix: 'Withdraw Request');
                    }
                }
            }
            //Account transaction
            $accountTransaction = AccountTransaction::where('type', 'collected')
                ->where('created_by', 'store')
                ->where('from_id', $vendor_id)
                ->where('from_type', 'store')
                ->find($searchKeyword);
            if ($accountTransaction) {
                $accountTransactionRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'wallet-payment-list');
                });

                if (isset($accountTransactionRoutes)) {
                    foreach ($accountTransactionRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $accountTransaction, route: $route,);
                    }
                }
            }

            //withdraw method
            $withdrawalMethod = DisbursementWithdrawalMethod::where('store_id', $store_id)->find($searchKeyword);
            if ($withdrawalMethod) {
                $withdrawalMethodRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'withdraw-method') && !str_contains($route->uri(), 'default');
                });

                if (isset($withdrawalMethodRoutes)) {
                    foreach ($withdrawalMethodRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $withdrawalMethod, route: $route, prefix: 'Withdraw Method');
                    }
                }
            }




            if ($userType == 'vendor') {
                //Employee role
                $vendorRole = EmployeeRole::where('store_id', $store_id)->find($searchKeyword);
                if ($vendorRole) {
                    $vendorRoleRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'custom-role/create') && !str_contains($route->uri(), 'edit');
                    });

                    if (isset($vendorRoleRoutes)) {
                        foreach ($vendorRoleRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $vendorRole, route: $route, prefix: 'Employee Role', searchKeyword: $vendorRole->name);
                        }
                    }
                }

                //Employee
                $employee = VendorEmployee::where('store_id', $store_id)->find($searchKeyword);
                if ($employee) {
                    $employeeRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'employee/list') && !str_contains($route->uri(), 'update') && !str_contains($route->uri(), 'export');
                    });

                    if (isset($employeeRoutes)) {
                        foreach ($employeeRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $employee, route: $route, prefix: 'Employee', searchKeyword: $employee->f_name . ' ' . $employee->l_name);
                        }
                    }
                }
            }



            if ($store_data->store_business_model !== 'commission') {
                //subscriber
                $storeSubscription = StoreSubscription::where('store_id', $store_id)->find($searchKeyword);
                if ($storeSubscription) {
                    $storeSubscriptionRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'subscriber-detail');
                    });

                    if (isset($storeSubscriptionRoutes)) {
                        foreach ($storeSubscriptionRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $storeSubscription, route: $route, prefix: 'Subscription');
                        }
                    }
                }
                $SubscriptionTransaction = SubscriptionTransaction::where('store_id', $store_id)->find($searchKeyword);
                if ($SubscriptionTransaction) {
                    $SubscriptionTransactionRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'subscriber-transactions');
                    });

                    if (isset($SubscriptionTransactionRoutes)) {
                        foreach ($SubscriptionTransactionRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $SubscriptionTransaction, type: 'subscriber-transactions', route: $route, searchKeyword: $SubscriptionTransaction->id);
                        }
                    }
                }


                //subscriber
                $storeSubscriptionBillingAndRefundHistory = SubscriptionBillingAndRefundHistory::where('store_id', $store_id)->find($searchKeyword);
                if ($storeSubscriptionBillingAndRefundHistory) {
                    $storeSubscriptionBillingAndRefundHistoryRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'subscriber-wallet-transactions');
                    });

                    if (isset($storeSubscriptionBillingAndRefundHistoryRoutes)) {
                        foreach ($storeSubscriptionBillingAndRefundHistoryRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $storeSubscriptionBillingAndRefundHistory, route: $route, prefix: 'Subscription');
                        }
                    }
                }
            }
            //expense report
            $expense = Expense::where('store_id', $store_id)->where('created_by', 'vendor')->find($searchKeyword);
            if ($expense) {
                $expenseRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'expense-report');
                });

                if (isset($expenseRoutes)) {
                    foreach ($expenseRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $expense, route: $route, prefix: null);
                    }
                }
            }

            if (in_array($moduleType, ['grocery', 'ecommerce'])) {
                //item
                $FlashSaleItem = FlashSaleItem::wherehas('item', function ($query) use ($store_id) {
                    $query->where('store_id', $store_id);
                })->find($searchKeyword);
                if ($FlashSaleItem) {
                    $itemRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'item/flash-sale'));
                    });

                    if (isset($itemRoutes)) {
                        foreach ($itemRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $FlashSaleItem, route: $route, type: 'Flash Sale Item', prefix: 'FlashSale', name: $FlashSaleItem?->item?->name, searchKeyword: $FlashSaleItem?->item?->name);
                        }
                    }
                }
            }



            if ($moduleType == 'rental') {
                $trip = Trips::where('provider_id', $store_id)->find($searchKeyword);

                if ($trip) {
                    $tripRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'trip/details') || str_contains($route->uri(), 'trip-report')) && !str_contains($route->uri(), 'expense-report') && !str_contains($route->uri(), 'export');
                    });
                    if (isset($tripRoutes)) {
                        foreach ($tripRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $trip, route: $route, type: 'trip', prefix: 'Trip', name: $trip->id, searchKeyword: $trip->id);
                        }
                    }
                }


                //Vehicle
                $Vehicle = Vehicle::where('provider_id', $store_id)->find($searchKeyword);
                if ($Vehicle) {
                    $VehicleRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'vehicle/details') || str_contains($route->uri(), 'vehicle/update'));
                    });

                    if (isset($VehicleRoutes)) {
                        foreach ($VehicleRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $Vehicle, route: $route, type: 'Vehicle', prefix: 'Vehicle', name: $Vehicle->name);
                        }
                    }
                }

                $VehicleCategory = VehicleCategory::find($searchKeyword);
                if ($VehicleCategory) {
                    $VehicleCategoryRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'vehicle-category/list');
                    });

                    if (isset($VehicleCategoryRoutes)) {
                        foreach ($VehicleCategoryRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $VehicleCategory, route: $route, type: 'VehicleCategory', prefix: 'VehicleCategory', name: $VehicleCategory->name, searchKeyword: $VehicleCategory->name);
                        }
                    }
                }
                $VehicleBrand = VehicleBrand::find($searchKeyword);
                if ($VehicleBrand) {
                    $VehicleBrandRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'vehicle-brand/list');
                    });

                    if (isset($VehicleBrandRoutes)) {
                        foreach ($VehicleBrandRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $VehicleBrand, route: $route, type: 'VehicleBrand', prefix: 'VehicleBrand', name: $VehicleBrand->name, searchKeyword: $VehicleBrand->name);
                        }
                    }
                }
                $VehicleDriver = VehicleDriver::where('provider_id', $store_id)->find($searchKeyword);
                if ($VehicleDriver) {
                    $VehicleDriverRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'driver/details') || str_contains($route->uri(), 'driver/update');
                    });

                    if (isset($VehicleDriverRoutes)) {
                        foreach ($VehicleDriverRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $VehicleDriver, route: $route, type: 'VehicleDriver', prefix: 'VehicleDriver', name: $VehicleDriver->first_name, searchKeyword: $VehicleDriver->first_name);
                        }
                    }
                }
                $VehicleReview = VehicleReview::where('review_id', $searchKeyword)->first();
                if ($VehicleReview) {
                    $VehicleReviewRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental-reviews');
                    });

                    if (isset($VehicleReviewRoutes)) {
                        foreach ($VehicleReviewRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $VehicleReview, route: $route, type: 'Vehicle_Review', prefix: 'VehicleReview',);
                        }
                    }
                }
            }
        } else {

            //Store
            $store = Store::where('id', $store_id)->where('name', 'LIKE', '%' . $searchKeyword . '%')

                ->orWhere('meta_title', 'LIKE', '%' . $searchKeyword . '%')
                ->orWhere('meta_description', 'LIKE', '%' . $searchKeyword . '%')

                ->first();

            if ($store) {
                $storeUrlRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'business-settings/store-setup');
                });

                if (isset($storeUrlRoutes)) {
                    foreach ($storeUrlRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $store, route: $route, type: 'settings');
                    }
                }
            }
            //Store
            $store = Store::where('id', $store_id)->where('name', 'LIKE', '%' . $searchKeyword . '%')
                ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                ->orWhere('address', 'LIKE', '%' . $searchKeyword . '%')
                ->orWhere('announcement_message', 'LIKE', '%' . $searchKeyword . '%')
                ->orWhereHas('vendor', function ($query) use ($searchKeyword) {
                    return $query->where('f_name', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('l_name', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                        ->orWhereRaw("CONCAT(l_name, ' ', f_name) LIKE ?", ['%' . $searchKeyword . '%'])
                        ->orWhereRaw("CONCAT(l_name,f_name) LIKE ?", ['%' . $searchKeyword . '%'])
                        ->orWhereRaw("CONCAT(f_name,l_name) LIKE ?", ['%' . $searchKeyword . '%']);
                })
                ->first();

            if ($store) {
                $storeUrlRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'vendor-panel/store/view');
                });

                if (isset($storeUrlRoutes)) {
                    foreach ($storeUrlRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $store, route: $route, type: 'info', prefix: 'My Store');
                    }
                }
            }
            //

            if ($moduleType !== 'rental') {
                //Order
                $orders = Order::where('store_id', $store_id)
                    ->with('customer')->where(function ($query) use ($searchKeyword) {
                        $query->where('order_status', 'LIKE', '%' . $searchKeyword . '%' )
                    ->orwhere('payment_method', 'LIKE', '%' . $searchKeyword . '%' )
                        ->orwhereHas('customer', function ($query) use ($searchKeyword) {
                            $query->where('f_name', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('l_name', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                                ->orWhereRaw("CONCAT(f_name,l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                                ->orWhereRaw("CONCAT(l_name,f_name) LIKE ?", ['%' . $searchKeyword . '%']);
                        })
                            ->orwhere(function ($query) use ($searchKeyword) {
                                $query->WhereRaw("JSON_SEARCH(delivery_address, 'one', ?) IS NOT NULL", ['%' . $searchKeyword . '%']);
                            });
                    })->get();


                if ($orders) {
                    $ordersRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'order/details') || str_contains($route->uri(), 'expense-report') && !str_contains($route->uri(), 'pos');
                    });

                    if (isset($ordersRoutes)) {
                        foreach ($orders as $order) {
                            foreach ($ordersRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $order, route: $route, type: 'order', prefix: 'Order', name: $order->id, searchKeyword: $order->id);
                            }
                        }
                    }
                }


                //Category
                $categories = Category::where(['module_id' => $module_id, 'position' => 0])->where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($categories) {
                    $categoryRoutes = $storeRoutes->filter(function ($route) {
                        return  !str_contains($route->uri(), '-category/list') &&  (str_contains($route->uri(), 'category/list') && !str_contains($route->uri(), 'category/sub-category-list'));
                    });

                    if (isset($categoryRoutes)) {
                        foreach ($categories as $category) {
                            foreach ($categoryRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $category, route: $route, type: 'category', prefix: 'Category', name: $category->name, searchKeyword: $category->name);
                            }
                        }
                    }
                }

                //sub Category
                $categories = Category::where(['module_id' => $module_id, 'position' => 1])->where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($categories) {
                    $categoryRoutes = $storeRoutes->filter(function ($route) {
                        return   str_contains($route->uri(), 'category/sub-category-list');
                    });

                    if (isset($categoryRoutes)) {
                        foreach ($categories as $category) {
                            foreach ($categoryRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $category, route: $route, type: 'category', prefix: 'Category', name: $category->name, searchKeyword: $category->name);
                            }
                        }
                    }
                }


                if ($moduleType == 'food') {

                    //AddOn
                    $addOns = AddOn::where('store_id', $store_id)
                        ->where(function ($query) use ($searchKeyword) {
                            $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                        })
                        ->get();

                    if ($addOns) {
                        $addOnRoutes = $storeRoutes->filter(function ($route) {
                            return str_contains($route->uri(), 'addon/edit');
                        });

                        if (isset($addOnRoutes)) {
                            foreach ($addOns as $addOn) {
                                foreach ($addOnRoutes as $route) {
                                    $validRoutes[] = $this->filterRoute(model: $addOn, route: $route, prefix: 'Addon', name: $addOn->name);
                                }
                            }
                        }
                    }
                }
                //

                //Item
                $Items = Item::where('store_id', $store_id)
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($Items) {
                    $ItemRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'item/view')
                            || str_contains($route->uri(), 'item/edit')) &&  !str_contains($route->uri(), 'item/requested');
                    });

                    if (isset($ItemRoutes)) {
                        foreach ($Items as $Item) {
                            foreach ($ItemRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $Item, route: $route, type: 'Item', prefix: 'Item', name: $Item->name);
                            }
                        }
                    }
                }

                //Item
                $Tempfood = TempProduct::where('store_id', $store_id)
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($Tempfood) {
                    $ItemRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'item/requested/item/view'));
                    });

                    if (isset($ItemRoutes)) {
                        foreach ($Tempfood as $Item) {
                            foreach ($ItemRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $Item, route: $route, type: 'tempitem', name: $Item?->name);
                            }
                        }
                    }
                }



                //Campaign
                $campaigns = Campaign::running()->active()->latest()->module($module_id)
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($campaigns) {
                    $campaignRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'campaign/list');
                    });

                    if (isset($campaignRoutes)) {
                        foreach ($campaigns as $campaign) {
                            foreach ($campaignRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $campaign, route: $route, type: 'campaign', prefix: 'Campaign', name: $campaign->title, searchKeyword: $campaign->title);
                            }
                        }
                    }
                }




                //ItemCampaign
                $itemCampaigns = ItemCampaign::where('store_id', $store_id)
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($itemCampaigns) {
                    $itemCampaignRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'campaign/item/list');
                    });

                    if (isset($itemCampaignRoutes)) {
                        foreach ($itemCampaigns as $itemCampaign) {
                            foreach ($itemCampaignRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $itemCampaign, route: $route, type: 'item-campaign', prefix: 'Campaign', name: $itemCampaign->title, searchKeyword: $itemCampaign->title);
                            }
                        }
                    }
                }


                if ($store_data->sub_self_delivery) {

                    //DeliveryMan
                    $deliveryMen = DeliveryMan::where('store_id', $store_id)
                        ->where(function ($query) use ($searchKeyword) {
                            $query->where('f_name', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('l_name', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('identity_type', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                                ->orWhereRaw("CONCAT(f_name,l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                                ->orWhereRaw("CONCAT(l_name,f_name) LIKE ?", ['%' . $searchKeyword . '%']);
                        })
                        ->get();

                    if ($deliveryMen) {
                        $deliveryManRoutes = $storeRoutes->filter(function ($route) {
                            return str_contains($route->uri(), 'delivery-man')
                                && str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'preview');
                        });
                        if (isset($deliveryManRoutes)) {
                            foreach ($deliveryMen as $deliveryMan) {
                                foreach ($deliveryManRoutes as $route) {
                                    $validRoutes[] = $this->filterRoute(model: $deliveryMan, route: $route, type: 'deliveryMan', name: $deliveryMan->f_name . ' ' . $deliveryMan->l_name, prefix: 'Delivery Man');
                                }
                            }
                        }
                    }
                }

                $reviews = Review::whereHas('item', function ($query) use ($store_id) {
                    return $query->where('store_id', $store_id);
                })->where(function ($query) use ($searchKeyword) {
                    $query->where('comment', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('reply', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();


                if ($reviews) {
                    $reviewRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'vendor-panel/review') && !str_contains($route->uri(), 'rental') && !str_contains($route->uri(), 'export');
                    });

                    if (isset($reviewRoutes)) {
                        foreach ($reviews as $review) {
                            foreach ($reviewRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $review, route: $route, prefix: 'reviews', searchKeyword: $review?->item?->name);
                            }
                        }
                    }
                }
            }




            //Coupon
            $coupons = Coupon::where('created_by', 'vendor')->where('store_id', $store_id)
                ->where(function ($query) use ($searchKeyword) {
                    $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('code', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('discount_type', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('coupon_type', 'LIKE', '%' . $searchKeyword . '%');
                })
                ->get();

            if ($coupons) {

                if ($moduleType == 'rental') {
                    $couponRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental-coupon') && (!str_contains($route->uri(), 'status') && !str_contains($route->uri(), 'export'));
                    });
                } else {
                    $couponRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'coupon/add-new') || str_contains($route->uri(), 'coupon/update') && (!str_contains($route->uri(), 'status') && !str_contains($route->uri(), 'export') && !str_contains($route->uri(), 'rental'));
                    });
                }

                if (isset($couponRoutes)) {
                    foreach ($coupons as $coupon) {
                        foreach ($couponRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $coupon, route: $route, prefix: 'Coupon', name: $coupon->title, searchKeyword: $coupon->title);
                        }
                    }
                }
            }
            //banner
            $banners = Banner::where('created_by', 'store')->where('data', $store_id)
                ->where(function ($query) use ($searchKeyword) {
                    $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('type', 'LIKE', '%' . $searchKeyword . '%');
                })
                ->get();

            if ($banners) {

                if ($moduleType == 'rental') {
                    $bannerRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'rental-banner') ||  str_contains($route->uri(), 'banner/edit')) && !str_contains($route->uri(), 'status') &&  str_contains($route->uri(), 'rental') && !str_contains($route->uri(), 'export');
                    });
                } else {
                    $bannerRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'banner/list') ||  str_contains($route->uri(), 'banner/edit')) && !str_contains($route->uri(), 'status') &&  !str_contains($route->uri(), 'rental') &&  !str_contains($route->uri(), 'export');
                    });
                }

                if (isset($bannerRoutes)) {
                    foreach ($banners as $banner) {
                        foreach ($bannerRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $banner, route: $route, prefix: 'banner', name: $banner->title, searchKeyword: $banner->title);
                        }
                    }
                }
            }

            //Advertisement
            $advertisements = Advertisement::where('store_id', $store_id)
                ->where(function ($query) use ($searchKeyword) {
                    $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('add_type', 'LIKE', '%' . $searchKeyword . '%');
                })
                ->get();

            if ($advertisements) {
                $adsRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'advertisement')
                        && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'detail'));
                });
                if (isset($adsRoutes)) {
                    foreach ($advertisements as $advertisement) {
                        foreach ($adsRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $advertisement, route: $route, prefix: 'Advertisement');
                        }
                    }
                }
            }


            //store disbursement
            $storeDisbursement = DisbursementDetails::where('store_id', $store_id)
                ->wherehas('withdraw_method', function ($query) use ($searchKeyword) {
                    $query->where('method_name', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhereRaw("JSON_SEARCH(method_fields, 'one', ?) IS NOT NULL", ['%' . $searchKeyword . '%']);
                })
                ->get();

            if ($storeDisbursement) {
                $storeDisbursementRoutes = $storeRoutes->filter(function ($route) {
                    return (str_contains($route->uri(), 'disbursement-report') || str_contains($route->uri(), 'wallet/disbursement-list')) && !str_contains($route->uri(), 'disbursement-report-export');
                });
                if (isset($storeDisbursementRoutes)) {
                    foreach ($storeDisbursement as $Disbursement) {
                        foreach ($storeDisbursementRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $Disbursement, route: $route, name: $Disbursement->disbursement_id);
                        }
                    }
                }
            }

            //Withdraw Request
            $withdrawRequests = WithdrawRequest::where('vendor_id', $vendor_id)
                ->where(function ($query) use ($searchKeyword) {
                    $query->where('type', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('withdrawal_method_fields->account_name', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('withdrawal_method_fields->account_number', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('withdrawal_method_fields->email', 'LIKE', '%' . $searchKeyword . '%');
                })
                ->get();

            if ($withdrawRequests) {
                $withdrawRequestRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'wallet') && !str_contains($route->uri(), 'disbursement-list')  && !str_contains($route->uri(), 'wallet-payment-list') && !str_contains($route->uri(), 'method-list') && !str_contains($route->uri(), 'export')  && !str_contains($route->uri(), 'subscription');
                });

                if (isset($withdrawRequestRoutes)) {
                    foreach ($withdrawRequests as $withdrawRequest) {
                        foreach ($withdrawRequestRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $withdrawRequest, route: $route, prefix: 'Withdraw Request');
                        }
                    }
                }
            }

            //accountTransaction Request
            $accountTransaction = AccountTransaction::where('type', 'collected')
                ->where('created_by', 'store')
                ->where('from_id', $vendor_id)
                ->where('from_type', 'store')


                ->where(function ($query) use ($searchKeyword) {
                    $query->where('method', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('ref', 'LIKE', '%' . $searchKeyword . '%');
                })
                ->get();

            if ($accountTransaction) {
                $accountTransactionRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'wallet-payment-list');
                });

                if (isset($accountTransactionRoutes)) {
                    foreach ($accountTransaction as $accountTransactionRequest) {
                        foreach ($accountTransactionRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $accountTransactionRequest, route: $route);
                        }
                    }
                }
            }
            //
            //Withdrawal Method
            $withdrawalMethods = DisbursementWithdrawalMethod::where('store_id', $store_id)
                ->where(function ($query) use ($searchKeyword) {
                    $query->where('method_name', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhereRaw("JSON_SEARCH(method_fields, 'one', ?) IS NOT NULL", ['%' . $searchKeyword . '%']);
                })
                ->get();

            if ($withdrawalMethods) {
                $withdrawalMethodRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'withdraw-method') && !str_contains($route->uri(), 'default');
                });

                if (isset($withdrawalMethodRoutes)) {
                    foreach ($withdrawalMethods as $withdrawalMethod) {
                        foreach ($withdrawalMethodRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $withdrawalMethod, route: $route, prefix: 'Withdraw Method');
                        }
                    }
                }
            }



            if ($userType == 'vendor') {
                //vendor Role
                $vendorRoles = EmployeeRole::where('store_id', $store_id)
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($vendorRoles) {
                    $vendorRoleRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'custom-role/create') && !str_contains($route->uri(), 'edit');
                    });

                    if (isset($vendorRoleRoutes)) {
                        foreach ($vendorRoles as $vendorRole) {
                            foreach ($vendorRoleRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $vendorRole, route: $route, prefix: 'Employee Role', searchKeyword: $vendorRole->name);
                            }
                        }
                    }
                }

                $vendorEmployee = VendorEmployee::where('store_id', $store_id)
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('f_name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('l_name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($vendorEmployee) {
                    $adminRoleRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'employee/list') && !str_contains($route->uri(), 'update') && !str_contains($route->uri(), 'export');
                    });

                    if (isset($adminRoleRoutes)) {
                        foreach ($vendorEmployee as $employee) {
                            foreach ($adminRoleRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $employee, route: $route, prefix: 'Employee', searchKeyword: $employee->f_name . ' ' . $employee->l_name);
                            }
                        }
                    }
                }
            }


            if ($store_data->store_business_model !== 'commission') {

                //Store Subscription
                $storeSubscriptions = StoreSubscription::with('package')->where('store_id', $store_id)
                    ->whereHas('package', function ($query) use ($searchKeyword) {
                        $query->where('package_name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('text', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($storeSubscriptions) {
                    $storeSubscriptionRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'subscriber-detail');
                    });

                    if (isset($storeSubscriptionRoutes)) {
                        foreach ($storeSubscriptions as $storeSubscription) {
                            foreach ($storeSubscriptionRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $storeSubscription, route: $route, prefix: 'Subscription');
                            }
                        }
                    }
                }

                //Subscription Transaction
                $SubscriptionTransaction = SubscriptionTransaction::where('store_id', $store_id)
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('reference', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();


                if ($SubscriptionTransaction) {
                    $storeSubscriptionBillingAndRefundHistoryRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'subscriber-transactions');
                    });

                    if (isset($storeSubscriptionBillingAndRefundHistoryRoutes)) {
                        foreach ($SubscriptionTransaction as $history) {
                            foreach ($storeSubscriptionBillingAndRefundHistoryRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $history, type: 'subscriber-transactions', route: $route, searchKeyword: $history->id);
                            }
                        }
                    }
                }

                //subscriber billing and refund history
                $storeSubscriptionBillingAndRefundHistorys = SubscriptionBillingAndRefundHistory::where('store_id', $store_id)
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('transaction_type', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('reference', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();


                if ($storeSubscriptionBillingAndRefundHistorys) {
                    $storeSubscriptionBillingAndRefundHistoryRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'subscriber-wallet-transactions');
                    });

                    if (isset($storeSubscriptionBillingAndRefundHistoryRoutes)) {
                        foreach ($storeSubscriptionBillingAndRefundHistorys as $history) {
                            foreach ($storeSubscriptionBillingAndRefundHistoryRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $history, route: $route, prefix: 'Subscription');
                            }
                        }
                    }
                }
            }


            //expense report
            $expense = Expense::where('store_id', $store_id)->where('created_by', 'vendor')->where(function ($query) use ($searchKeyword) {
                $query->where('type', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('order_id', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('trip_id', 'LIKE', '%' . $searchKeyword . '%');
            })
                ->get();
            if ($expense) {
                $expenseRoutes = $storeRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'expense-report');
                });

                if (isset($expenseRoutes)) {
                    foreach ($expense as $expens) {
                        foreach ($expenseRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $expens, route: $route, prefix: null);
                        }
                    }
                }
            }



            if (in_array($moduleType, ['grocery', 'ecommerce'])) {
                //item
                $FlashSaleItems = FlashSaleItem::wherehas('item', function ($query) use ($store_id, $searchKeyword) {
                    $query->where('store_id', $store_id)
                        ->where(function ($query) use ($searchKeyword) {
                            $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                        })


                    ;
                })->get();
                if ($FlashSaleItems) {
                    $itemRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'item/flash-sale'));
                    });

                    if (isset($itemRoutes)) {
                        foreach ($FlashSaleItems as $item) {
                            foreach ($itemRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $item, route: $route, type: 'Flash Sale Item', prefix: 'FlashSale', name: $item?->item?->name, searchKeyword: $item?->item?->name);
                            }
                        }
                    }
                }
            }






            if ($moduleType == 'rental') {

                $trips = Trips::where('provider_id', $store_id)
                    ->with('customer')->where(function ($query) use ($searchKeyword) {
                        $query->whereHas('customer', function ($query) use ($searchKeyword) {
                            $query->where('f_name', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('l_name', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                                ->orWhereRaw("CONCAT(f_name,l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                                ->orWhereRaw("CONCAT(l_name,f_name) LIKE ?", ['%' . $searchKeyword . '%']);
                        });
                    })->get();


                if ($trips) {
                    $tripsRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'trip/details') || str_contains($route->uri(), 'trip-report')) && !str_contains($route->uri(), 'expense-report') && !str_contains($route->uri(), 'export');
                    });

                    if (isset($tripsRoutes)) {
                        foreach ($trips as $trip) {
                            foreach ($tripsRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $trip, route: $route, type: 'trip', prefix: 'trip', name: $trip->id, searchKeyword: $trip->id);
                            }
                        }
                    }
                }



                $vehicles = Vehicle::where('provider_id', $store_id)
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();


                if ($vehicles) {
                    $vehiclesRoutes = $storeRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'vehicle/details') || str_contains($route->uri(), 'vehicle/update'));
                    });

                    if (isset($vehiclesRoutes)) {
                        foreach ($vehicles as $vehicle) {
                            foreach ($vehiclesRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $vehicle, route: $route, type: 'Vehicle', prefix: 'Vehicle', name: $vehicle->name);
                            }
                        }
                    }
                }

                $VehicleCategorys = VehicleCategory::where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();


                if ($VehicleCategorys) {
                    $VehicleCategorysRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'vehicle-category/list');
                    });

                    if (isset($VehicleCategorysRoutes)) {
                        foreach ($VehicleCategorys as $VehicleCategory) {
                            foreach ($VehicleCategorysRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $VehicleCategory, route: $route, type: 'VehicleCategory', prefix: 'VehicleCategory', name: $VehicleCategory->name, searchKeyword: $VehicleCategory->name);
                            }
                        }
                    }
                }
                $VehicleBrand = VehicleBrand::where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();


                if ($VehicleBrand) {
                    $VehicleBrandRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'vehicle-brand/list');
                    });

                    if (isset($VehicleBrandRoutes)) {
                        foreach ($VehicleBrand as $Vehicle_Brand) {
                            foreach ($VehicleBrandRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $Vehicle_Brand, route: $route, type: 'VehicleBrand', prefix: 'VehicleBrand', name: $Vehicle_Brand->name, searchKeyword: $Vehicle_Brand->name);
                            }
                        }
                    }
                }
                $VehicleDriver = VehicleDriver::where('provider_id', $store_id)
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('first_name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orwhere('email', 'LIKE', '%' . $searchKeyword . '%')
                            ->orwhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                            ->orwhere('identity_number', 'LIKE', '%' . $searchKeyword . '%')
                            ->orwhere('last_name', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();


                if ($VehicleDriver) {
                    $VehicleDriverRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'driver/details') || str_contains($route->uri(), 'driver/update');
                    });

                    if (isset($VehicleDriverRoutes)) {
                        foreach ($VehicleDriver as $Vehicle_Driver) {
                            foreach ($VehicleDriverRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $Vehicle_Driver, route: $route, type: 'VehicleDriver', prefix: 'VehicleDriver', name: $Vehicle_Driver->first_name, searchKeyword: $Vehicle_Driver->first_name);
                            }
                        }
                    }
                }
                $VehicleReview = VehicleReview::where(function ($query) use ($searchKeyword) {
                    $query->where('comment', 'LIKE', '%' . $searchKeyword . '%')
                        ->orwhere('reply', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();


                if ($VehicleReview) {
                    $VehicleReviewRoutes = $storeRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental-reviews');
                    });

                    if (isset($VehicleReviewRoutes)) {
                        foreach ($VehicleReview as $Vehicle_Review) {
                            foreach ($VehicleReviewRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $Vehicle_Review, route: $route, type: 'Vehicle_Review', prefix: 'VehicleReview');
                            }
                        }
                    }
                }
            }
        }

        $result = array_merge($formattedRoutes, $validRoutes);
        $result = collect($result);
        $result = $result->unique('fullRoute')->values()->all();
        return $this->sortBySearchKeyword($result, $searchKeyword);
    }

    private function routeFullUrl($uri)
    {
        return url($uri);
    }

    private function filterRoute($model, $route, $type = null, $name = null, $prefix = null, $searchKeyword = null): array
    {
        $uri = $route->uri();
        $routeName = $route->getName();
        $formattedRouteName = ucwords(str_replace(['.', '_'], ' ', Str::afterLast($routeName, '.')));

        preg_match_all('/\{(\w+\??)\}/', $uri, $matches);
        $placeholders = $matches[1];

        $uriWithParameter = $uri;

        if (!empty($placeholders)) {
            $firstPlaceholder = $placeholders[0];
            $uriWithParameter = str_replace("{{$firstPlaceholder}}", $model->id, $uriWithParameter);
        }

        $uriWithParameter = preg_replace('/\{\w+\?\}/', '', $uriWithParameter);
        $uriWithParameter = preg_replace('/\/+/', '/', $uriWithParameter);
        $uriWithParameter = rtrim($uriWithParameter, '/');


        if ($searchKeyword && (!is_numeric($searchKeyword) || in_array($type, ['subscriber-transactions', 'order', 'trip', 'Vehicle_Review']))) {
            $uriWithParameter .= '?search=' . urlencode($searchKeyword);
        }
        $fullURL = url('/') . '/' . $uriWithParameter;
        $routeName = $prefix ? $prefix . ' ' . $formattedRouteName : $formattedRouteName;
        $routeName = $name ? $routeName . ' - (' . $name . ')' : $routeName;
        $routeName = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $routeName);
        $routeName = trim(preg_replace('/\s+/', ' ', $routeName));

        return [
            'routeName' => $routeName,
            'URI' => $uriWithParameter,
            'fullRoute' => $fullURL,
        ];
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeClickedRoute(Request $request): JsonResponse
    {
        $userId = Helpers::get_vendor_id();
        $userType = auth('vendor')->check() ? 'vendor' : 'vendor-employee';
        $routeName = $request->input('routeName');
        $routeUri = $request->input('routeUri');
        $routeFullUrl = $request->input('routeFullUrl');
        $searchKeyword = $request->input('searchKeyword');

        $existingClick = RecentSearch::where('user_id', $userId)
            ->where('user_type', $userType)
            ->where('route_uri', $routeUri)
            ->first();

        if (!$existingClick) {
            $clickedRoute = new RecentSearch();
            $clickedRoute->user_id = $userId;
            $clickedRoute->user_type = $userType;
            $clickedRoute->route_name = $routeName;
            $clickedRoute->route_uri = $routeUri;
            $clickedRoute->module_id = Helpers::get_store_data()->module_id;
            $clickedRoute->keyword = $searchKeyword;
            $clickedRoute->route_full_url = isset($searchKeyword) ? $routeFullUrl . '?keyword=' . $searchKeyword : $routeFullUrl;
            $clickedRoute->save();
        } else {
            $existingClick->created_at = now();
            $existingClick->update();
        }

        $userClicksCount = RecentSearch::where('user_id', $userId)
            ->where('user_type', $userType)
            ->count();

        if ($userClicksCount > 15) {
            RecentSearch::where('user_id', $userId)
                ->where('user_type', $userType)
                ->orderBy('created_at', 'asc')
                ->first()
                ->delete();
        }

        return response()->json(['message' => 'Clicked route stored successfully']);
    }

    public function recentSearch(): JsonResponse
    {
        $userId = Helpers::get_vendor_id();
        $userType = auth('vendor')->check() ? 'vendor' : 'vendor-employee';

        $recentSearches = RecentSearch::where('user_id', $userId)
            ->where('user_type', $userType)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($recentSearches);
    }

    private function sortBySearchKeyword(array $routes, string $keyword): array
    {
        usort($routes, function ($a, $b) use ($keyword) {
            $aMatch = min(
                $this->strposIgnoreCase($a['routeName'], $keyword),
                $this->strposIgnoreCase($a['URI'], $keyword),
                $this->strposIgnoreCase($a['fullRoute'], $keyword)
            );
            $bMatch = min(
                $this->strposIgnoreCase($b['routeName'], $keyword),
                $this->strposIgnoreCase($b['URI'], $keyword),
                $this->strposIgnoreCase($b['fullRoute'], $keyword)
            );

            return $aMatch <=> $bMatch;
        });

        return $routes;
    }

    private function strposIgnoreCase($haystack, $needle)
    {
        $pos = stripos($haystack, $needle);
        return $pos === false ? PHP_INT_MAX : $pos;
    }
}
