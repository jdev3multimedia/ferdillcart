<?php

namespace App\Http\Controllers\Admin;

use App\Models\Item;
use App\Models\Unit;
use App\Models\User;
use App\Models\Zone;
use App\Models\AddOn;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Store;
use App\Models\Banner;
use App\Models\Coupon;
use App\Models\Module;
use App\Models\Contact;
use App\Models\Campaign;
use App\Models\CashBack;
use App\Models\Category;
use App\Models\AdminRole;
use App\Models\Attribute;
use App\Models\DMVehicle;
use App\Models\FlashSale;
use App\Models\Newsletter;
use App\Models\DeliveryMan;
use App\Models\TempProduct;
use App\Models\WalletBonus;
use Illuminate\Support\Str;
use App\Models\Disbursement;
use App\Models\ItemCampaign;
use App\Models\Notification;
use App\Models\RecentSearch;
use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Models\ParcelCategory;
use App\Models\CommonCondition;
use App\Models\WithdrawalMethod;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Modules\Rental\Entities\Trips;
use App\Models\SubscriptionPackage;
use App\Http\Controllers\Controller;
use Modules\Rental\Entities\Vehicle;
use Illuminate\Support\Facades\Route;
use App\Models\LoyaltyPointTransaction;
use Modules\Rental\Entities\VehicleBrand;
use Modules\Rental\Entities\VehicleReview;
use Modules\Rental\Entities\VehicleCategory;


class SearchRoutingController extends Controller
{
    public function index(Request $request)
    {
        $searchKeyword = $request->input('search');
        session(['search_keyword' => $searchKeyword]);
        $currentModuleType = config('module.current_module_type') ?? null;
        $currentModuleId = config('module.current_module_id') ?? null;
        //1st layer
        $formattedRoutes = [];
        $jsonFilePath = public_path('admin_formatted_routes.json');
        if (file_exists($jsonFilePath)) {
            $fileContents = file_get_contents($jsonFilePath);
            $routes = json_decode($fileContents, true);
            if (!addon_published_status('Rental') || $currentModuleType !== 'rental') {
                $routes = array_filter($routes, function ($route) {
                    return  !in_array('rental', $route['moduleType']);
                });
            }


            foreach ($routes as $route) {
                $uri = $route['URI'];
                if (Str::contains(strtolower($route['keywords']), strtolower($searchKeyword)) || Str::contains(strtolower($route['URI']), strtolower($searchKeyword))) {
                    $hasParameters = preg_match('/\{(.*?)\}/', $uri);

                    $fullURL = $this->routeFullUrl($uri);

                    if (!$hasParameters) {
                        if (in_array($currentModuleType, $route['moduleType']) || $route['moduleType'] === []) {

                            $routeName = $route['routeName'];
                            $routeName = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $routeName);
                            $routeName = trim(preg_replace('/\s+/', ' ', $routeName));

                            $formattedRoutes[] = [
                                'routeName' => ucwords($routeName),
                                'URI' => $uri,
                                'fullRoute' => $fullURL,
                                'currentModuleType' => $currentModuleType,
                                'data_from' => 'files',
                            ];
                        }
                    }
                }
            }
        }

        // //2nd layer
        $routes = Route::getRoutes();
        $adminRoutes = collect($routes->getRoutesByMethod()['GET'])->filter(function ($route) {
            return str_starts_with($route->uri(), 'admin');
        });


        $excludeTermsRoute = ['{status}', 'review-status', 'review-export','{locale}','export-review','admin/transactions/report/vendor-tax-report','-auto-fill'];
        if (!addon_published_status('Rental') || $currentModuleType !== 'rental') {
            $excludeTermsRoute[] = 'rental';
        }
        $excludeTermsAjax = $this->getAjaxRoutes($adminRoutes);
        $addUrl = [
            'admin/users/cashback/edit/{id}',
            'admin/rental',
            'admin/rental/trip/details/{id}'
        ];

        $excludeTermsAjax = array_values(array_diff($excludeTermsAjax, $addUrl));

        $excludeTerms = array_merge($excludeTermsAjax, $excludeTermsRoute);


        $adminRoutes = $adminRoutes->filter(function ($route) use ($excludeTerms) {
            foreach ($excludeTerms as $term) {
                if (str_contains($route->uri(), $term)) {
                    return false;
                }
            }
            return true;
        });
//         // print_r($adminRoutes->toArray());
// dd($adminRoutes->toArray());
        $validRoutes = [];
        if (is_numeric($searchKeyword) && $searchKeyword > 0) {
            //store

            $store = Store::with('vendor')
                ->when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })
                ->where('id', $searchKeyword)
                ->first();

            if ($store) {

                if ($currentModuleType == 'rental') {

                    if ($store->vendor->status === 0) {
                        $storeRoutes = $adminRoutes->filter(function ($route) {
                            return str_contains($route->uri(), 'rental/provider') && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'details')  || str_contains($route->uri(), 'deny-requests'))
                                && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                        });
                    } elseif ($store->vendor->status === null) {
                        $storeRoutes = $adminRoutes->filter(function ($route) {
                            return str_contains($route->uri(), 'rental/provider') && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'details')  || str_contains($route->uri(), 'pending-requests'))
                                && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                        });
                    } else {
                        $storeRoutes = $adminRoutes->filter(function ($route) {
                            return str_contains($route->uri(), 'rental/provider') && str_contains($route->uri(), 'edit')
                                && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                        });
                    }
                } else {

                    if ($store->vendor->status === 0) {
                        $storeRoutes = $adminRoutes->filter(function ($route) {
                            return str_contains($route->uri(), 'store') && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'view')  || str_contains($route->uri(), 'deny-requests') || basename(parse_url($route->uri(), PHP_URL_PATH)) == 'recommended-store')
                                && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                        });
                    } elseif ($store->vendor->status === null) {
                        $storeRoutes = $adminRoutes->filter(function ($route) {
                            return str_contains($route->uri(), 'store') && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'view')  || str_contains($route->uri(), 'pending-requests')  || basename(parse_url($route->uri(), PHP_URL_PATH)) == 'recommended-store')
                                && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                        });
                    } else {
                        $storeRoutes = $adminRoutes->filter(function ($route) {
                            return str_contains($route->uri(), 'store') && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'view')  || basename(parse_url($route->uri(), PHP_URL_PATH)) == 'recommended-store')
                                && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                        });
                    }
                }

                if (isset($storeRoutes)) {
                    foreach ($storeRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $store, route: $route, type: 'store', prefix: 'Store');
                    }
                }
            }
            if ($currentModuleType !== 'rental') {

                //order
                $order = Order::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->find($searchKeyword);
                if ($order) {

                    $orderRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/order/details');
                    });


                    if (isset($orderRoutes)) {
                        foreach ($orderRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $order, route: $route, type: 'order', prefix: 'Order');
                        }
                    }
                }

                //multiple orders with customer id
                $orders = Order::with(['customer', 'store'])
                    ->when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                        return $query->where('module_id', $currentModuleId);
                    })
                    ->whereHas('customer', function ($query) use ($searchKeyword) {
                        $query->where('id', $searchKeyword);
                    })
                    ->orWhereHas('store', function ($query) use ($searchKeyword) {
                        $query->where('id', $searchKeyword);
                    })
                    ->get();

                if ($orders) {
                    $ordersRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/order/details');
                    });
                    if (isset($ordersRoutes)) {
                        foreach ($orders as $order) {
                            foreach ($ordersRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $order, route: $route, type: 'order', prefix: 'Order');
                            }
                        }
                    }
                }


                //category
                $category = Category::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->find($searchKeyword);

                if ($category) {
                    $categoryRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'category') && str_contains($route->uri(), 'update') && !str_contains($route->uri(), 'category/update-priority');
                    });

                    if (isset($categoryRoutes)) {
                        foreach ($categoryRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $category, route: $route, type: 'category', prefix: 'Category');
                        }
                    }
                }



                //AddOn
                $addOn = AddOn::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->wherehas('store', function ($query) use ($currentModuleId) {
                        $query->where('module_id', $currentModuleId);
                    });
                })
                    ->find($searchKeyword);
                if ($addOn) {
                    $addOnRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'addon') && str_contains($route->uri(), 'edit');
                    });

                    if (isset($addOnRoutes)) {
                        foreach ($addOnRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $addOn, route: $route, prefix: 'Addon');
                        }
                    }
                }

                //Item
                $Item = Item::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->find($searchKeyword);

                if ($Item) {
                    $ItemRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/item/edit/') || str_contains($route->uri(), 'admin/item/view/');
                    });

                    if (isset($ItemRoutes)) {
                        foreach ($ItemRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $Item, route: $route, type: 'Item', prefix: 'Item');
                        }
                    }
                }

                //basic campaign
                $campaign = Campaign::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->find($searchKeyword);
                if ($campaign) {
                    $campaignRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'campaign')
                            && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'view'));
                    });

                    if (isset($campaignRoutes)) {
                        foreach ($campaignRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $campaign, route: $route, type: 'basic-campaign', prefix: 'Basic Campaign');
                        }
                    }
                }

                //item campaign
                $itemCampaign = ItemCampaign::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->find($searchKeyword);
                if ($itemCampaign) {
                    $itemCampaignRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'campaign')
                            && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'view'));
                    });

                    if (isset($itemCampaignRoutes)) {
                        foreach ($itemCampaignRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $itemCampaign, route: $route, type: 'item-campaign', prefix: 'Item Campaign');
                        }
                    }
                }

                //vehicle
                $vehicle = DMVehicle::find($searchKeyword);
                if ($vehicle) {
                    $vehicleRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'delivery-man/vehicle/edit');
                    });

                    if (isset($vehicleRoutes)) {
                        foreach ($vehicleRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $vehicle, route: $route, type: 'vehicle', prefix: 'Vehicle');
                        }
                    }
                }

                //delivery man
                $deliveryMan = DeliveryMan::find($searchKeyword);
                if ($deliveryMan) {
                    $deliveryManRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'delivery-man/edit') || str_contains($route->uri(), 'delivery-man/preview');
                    });
                    if (isset($deliveryManRoutes)) {
                        foreach ($deliveryManRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $deliveryMan, route: $route, type: 'deliveryMan', prefix: 'Delivery Man');
                        }
                    }
                }





                //tepmProduct
                $tepmProduct = TempProduct::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->find($searchKeyword);
                if ($tepmProduct) {
                    $tepmProductRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/item/new/item/list') || str_contains($route->uri(), 'admin/item/requested/item/view/');
                    });

                    if (isset($tepmProductRoutes)) {
                        foreach ($tepmProductRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $tepmProduct, route: $route, prefix: 'New Product');
                        }
                    }
                }

                //Advertisement
                $ads = Advertisement::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->find($searchKeyword);
                if ($ads) {
                    $adsRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'advertisement')
                            && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'detail'));
                    });
                    if (isset($adsRoutes)) {
                        foreach ($adsRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $ads, route: $route, prefix: 'Advertisement');
                        }
                    }
                }
            }

            //zone
            $zone = Zone::find($searchKeyword);
            if ($zone) {
                $zoneRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'business-settings/zone') &&
                        (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'zone/module-setup/'));
                });

                if (isset($zoneRoutes)) {
                    foreach ($zoneRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $zone, route: $route, type: 'zone', prefix: 'Zone');
                    }
                }
            }




            if (in_array($currentModuleType, ['grocery', 'pharmacy', 'ecommerce'])) {
                //Unit
                $unit = Unit::find($searchKeyword);
                if ($unit) {
                    $unitRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'unit')  && !str_contains($route->uri(), 'unit/export');
                    });

                    if (isset($unitRoutes)) {
                        foreach ($unitRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $unit, route: $route, prefix: 'unit');
                        }
                    }
                }

                // attribute
                $attribute = Attribute::find($searchKeyword);
                if ($attribute) {
                    $attributeRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'attribute')  && !str_contains($route->uri(), 'attribute/export');
                    });

                    if (isset($attributeRoutes)) {
                        foreach ($attributeRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $attribute, route: $route, prefix: 'attribute');
                        }
                    }
                }
            }




            if (in_array($currentModuleType, ['ecommerce'])) {
                // Brand
                $Brand = Brand::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where(function ($query) use ($currentModuleId) {
                        $query->where('module_id', $currentModuleId)
                            ->orWhere('module_id', null);
                    });
                })
                    ->find($searchKeyword);
                if ($Brand) {
                    $BrandRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'brand')  && !str_contains($route->uri(), 'brand/export') && !str_contains($route->uri(), 'brand/status');
                    });

                    if (isset($BrandRoutes)) {
                        foreach ($BrandRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $Brand, route: $route, prefix: 'Brand');
                        }
                    }
                }
            }




            //coupon
            $coupon = Coupon::where('created_by', 'admin')->when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                return $query->where('module_id', $currentModuleId);
            })->find($searchKeyword);
            if ($coupon) {


                if ($currentModuleType == 'rental') {

                    $couponRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental/coupon') && (!str_contains($route->uri(), 'status') && !str_contains($route->uri(), 'export'));
                    });
                } else {

                    $couponRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'coupon/edit');
                    });
                }
                if (isset($couponRoutes)) {
                    foreach ($couponRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $coupon, route: $route, prefix: 'Coupon', searchKeyword: $coupon->title);
                    }
                }
            }

            //cashback
            $cashback = CashBack::find($searchKeyword);
            if ($cashback) {

                if ($currentModuleType == 'rental') {
                    $cashbackRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental/cashback') && !str_contains($route->uri(), 'status');
                    });
                } else {
                    $cashbackRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'cashback') && !str_contains($route->uri(), 'status');
                    });
                }

                if (isset($cashbackRoutes)) {
                    foreach ($cashbackRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $cashback, route: $route, prefix: 'Cashback', searchKeyword: $cashback->title);
                    }
                }
            }

            //banner
            $banner = Banner::where('created_by', 'admin')->when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                return $query->where('module_id', $currentModuleId);
            })->find($searchKeyword);
            if ($banner) {

                if ($currentModuleType == 'rental') {
                    $bannerRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/rental/banner') && !str_contains($route->uri(), 'status');
                    });
                } else {

                    $bannerRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/banner') && !str_contains($route->uri(), 'status');
                    });
                }


                if (isset($bannerRoutes)) {
                    foreach ($bannerRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $banner, route: $route, prefix: 'Banner');
                    }
                }
            }



            $contact = Contact::find($searchKeyword);
            if ($contact) {
                $contactRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'contact') && str_contains($route->uri(), 'view');
                });

                if (isset($contactRoutes)) {
                    foreach ($contactRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $contact, route: $route, prefix: 'Contact');
                    }
                }
            }

            $notification = Notification::find($searchKeyword);
            if ($notification) {

                if ($currentModuleType == 'rental') {
                    $notificationRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental/notification') && str_contains($route->uri(), 'edit');
                    });
                } else {

                    $notificationRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'notification') && str_contains($route->uri(), 'edit');
                    });
                }

                if (isset($notificationRoutes)) {
                    foreach ($notificationRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $notification, route: $route, prefix: 'Notification', name: $notification->title, searchKeyword: $notification->title);
                    }
                }
            }

            //customer
            $customer = User::find($searchKeyword);
            if ($customer) {
                $customerRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'users/customer/view');
                });

                if (isset($customerRoutes)) {
                    foreach ($customerRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $customer, route: $route, type: 'customer', prefix: 'Customer');
                    }
                }
            }

            //WalletBonus
            $bonus = WalletBonus::find($searchKeyword);
            if ($bonus) {
                $bonusRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'wallet/bonus');
                });

                if (isset($bonusRoutes)) {
                    foreach ($bonusRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $bonus, route: $route, type: 'bonus', prefix: 'Bonus');
                    }
                }
            }



            //store disbursement
            $storeDisbursement = Disbursement::where('created_for', 'store')->find($searchKeyword);
            if ($storeDisbursement) {
                $storeDisbursementRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'store-disbursement') &&  str_contains($route->uri(), 'details');
                });

                if (isset($storeDisbursementRoutes)) {
                    foreach ($storeDisbursementRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $storeDisbursement, route: $route, prefix: 'Store Disbursement');
                    }
                }
            }

            //delivery man disbursement
            $dmDisbursement = Disbursement::where('created_for', 'delivery_man')->find($searchKeyword);
            if ($dmDisbursement) {
                $dmDisbursementRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'dm-disbursement') &&  str_contains($route->uri(), 'details');
                });

                if (isset($dmDisbursementRoutes)) {
                    foreach ($dmDisbursementRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $dmDisbursement, route: $route, prefix: 'Delivery Man Disbursement');
                    }
                }
            }



            //withdraw method
            $withdrawalMethod = WithdrawalMethod::find($searchKeyword);
            if ($withdrawalMethod) {
                $withdrawalMethodRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'withdraw-method') && str_contains($route->uri(), 'edit');
                });

                if (isset($withdrawalMethodRoutes)) {
                    foreach ($withdrawalMethodRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $withdrawalMethod, route: $route, prefix: 'Withdraw Method');
                    }
                }
            }

            // Employee role
            $adminRole = AdminRole::find($searchKeyword);
            if ($adminRole) {
                $adminRoleRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'custom-role') && str_contains($route->uri(), 'edit');
                });

                if (isset($adminRoleRoutes)) {
                    foreach ($adminRoleRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $adminRole, route: $route, prefix: 'Employee Role');
                    }
                }
            }

            //Employee
            $employee = Admin::whereNotIn('id', [1, auth('admin')->user()->id])->find($searchKeyword);
            if ($employee) {
                $employeeRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'employee') && str_contains($route->uri(), 'edit');
                });

                if (isset($employeeRoutes)) {
                    foreach ($employeeRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $employee, route: $route, prefix: 'Employee');
                    }
                }
            }

            //subscription package
            $subscriptionPackage = SubscriptionPackage::find($searchKeyword);
            if ($subscriptionPackage) {
                $subscriptionPackageRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'subscription/subscriptionackage');
                });

                if (isset($subscriptionPackageRoutes)) {
                    foreach ($subscriptionPackageRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $subscriptionPackage, route: $route, prefix: 'Subscription');
                    }
                }
            }

            //Module
            $module = Module::find($searchKeyword);
            if ($module) {
                $moduleRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'business-settings/module') && !str_contains($route->uri(), 'export');
                });

                if (isset($moduleRoutes)) {
                    foreach ($moduleRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $module, route: $route, prefix: 'Module');
                    }
                }
            }

            if (in_array($currentModuleType, ['grocery', 'ecommerce'])) {
                //flashSale
                $flashSale = FlashSale::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->find($searchKeyword);
                if ($flashSale) {
                    $flashSaleRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'flash-sale/') && !str_contains($route->uri(), 'publish');
                    });

                    if (isset($flashSaleRoutes)) {
                        foreach ($flashSaleRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $flashSale, route: $route, prefix: 'flashSale');
                        }
                    }
                }
            }

            //ParcelCategory
            if (in_array($currentModuleType, ['parcel'])) {
                $ParcelCategory = ParcelCategory::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->find($searchKeyword);
                if ($ParcelCategory) {
                    $ParcelCategoryRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/parcel/category/') && (str_contains($route->uri(), 'edit'));
                    });

                    if (isset($ParcelCategoryRoutes)) {
                        foreach ($ParcelCategoryRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $ParcelCategory, route: $route, prefix: 'Parcel Category');
                        }
                    }
                }
            }
            //CommonCondition
            if (in_array($currentModuleType, ['pharmacy'])) {
                $CommonCondition = CommonCondition::find($searchKeyword);
                if ($CommonCondition) {
                    $CommonConditionRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/common-condition');
                    });

                    if (isset($CommonConditionRoutes)) {
                        foreach ($CommonConditionRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $CommonCondition, route: $route, prefix: 'Common Condition');
                        }
                    }
                }
            }
            //walletTransaction
            $walletTransaction = WalletTransaction::find($searchKeyword);
            if ($walletTransaction) {
                $walletTransactionRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'users/customer/wallet/report');
                });

                if (isset($walletTransactionRoutes)) {
                    foreach ($walletTransactionRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $walletTransaction, route: $route, prefix: 'Customer Wallet Report');
                    }
                }
            }


            //LoyaltyPoint
            $LoyaltyPoint = LoyaltyPointTransaction::find($searchKeyword);
            if ($LoyaltyPoint) {
                $LoyaltyPointRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'users/customer/loyalty-point/report');
                });

                if (isset($LoyaltyPointRoutes)) {
                    foreach ($LoyaltyPointRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $LoyaltyPoint, route: $route, prefix: 'Customer Loyalty Point');
                    }
                }
            }
            //Newsletter
            $Newsletter = Newsletter::find($searchKeyword);
            if ($Newsletter) {
                $NewsletterRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'admin/users/customer/subscribed');
                });

                if (isset($NewsletterRoutes)) {
                    foreach ($NewsletterRoutes as $route) {
                        $validRoutes[] = $this->filterRoute(model: $Newsletter, route: $route, prefix: 'Customers');
                    }
                }
            }





            if ($currentModuleType == 'rental') {
                $trip = Trips::find($searchKeyword);
                if ($trip) {
                    $tripRoutes = $adminRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'rental/trip/details')) && !str_contains($route->uri(), 'transactions/rental/trip/details/')  && !str_contains($route->uri(), 'expense-report') && !str_contains($route->uri(), 'export');
                    });

                    if (isset($tripRoutes)) {
                        foreach ($tripRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $trip, route: $route, type: 'trip', prefix: 'Trip', name: $trip->id, searchKeyword: $trip->id);
                        }
                    }
                }

                //Vehicle
                $Vehicle = Vehicle::find($searchKeyword);
                if ($Vehicle) {
                    $VehicleRoutes = $adminRoutes->filter(function ($route) {
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
                    $VehicleCategoryRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental/category/edit/');
                    });

                    if (isset($VehicleCategoryRoutes)) {
                        foreach ($VehicleCategoryRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $VehicleCategory, route: $route, type: 'VehicleCategory', prefix: 'VehicleCategory', name: $VehicleCategory->name, searchKeyword: $VehicleCategory->name);
                        }
                    }
                }
                $VehicleBrand = VehicleBrand::find($searchKeyword);
                if ($VehicleBrand) {
                    $VehicleBrandRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental/brand/edit');
                    });

                    if (isset($VehicleBrandRoutes)) {
                        foreach ($VehicleBrandRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $VehicleBrand, route: $route, type: 'VehicleBrand', prefix: 'VehicleBrand', name: $VehicleBrand->name, searchKeyword: $VehicleBrand->name);
                        }
                    }
                }
                // $VehicleDriver = VehicleDriver::find($searchKeyword);
                // if ($VehicleDriver) {
                //     $VehicleDriverRoutes = $adminRoutes->filter(function ($route) {
                //         return str_contains($route->uri(), 'driver/details') || str_contains($route->uri(), 'driver/update');
                //     });

                //     if (isset($VehicleDriverRoutes)) {
                //         foreach ($VehicleDriverRoutes as $route) {
                //             $validRoutes[] = $this->filterRoute(model: $VehicleDriver, route: $route, type: 'VehicleDriver', prefix: 'VehicleDriver', name: $VehicleDriver->first_name, searchKeyword: $VehicleDriver->first_name);
                //         }
                //     }
                // }
                $VehicleReview = VehicleReview::with('vehicle')->where('review_id', $searchKeyword)->first();
                if ($VehicleReview) {
                    $VehicleReviewRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'vehicle/review-list');
                    });

                    if (isset($VehicleReviewRoutes)) {
                        foreach ($VehicleReviewRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $VehicleReview, route: $route, type: 'Vehicle_Review', prefix: 'VehicleReview', name: $VehicleReview?->vehicle?->name, searchKeyword: $VehicleReview?->vehicle?->name);
                        }
                    }
                }
            }


            // review
            // $review = Review::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
            //                                         return $query->where('module_id', $currentModuleId);
            //                                     })->find($searchKeyword);
            // if ($review){
            //     $reviewRoutes = $adminRoutes->filter(function ($route) {
            //         return str_contains($route->uri(), 'admin/item/reviews') || str_contains($route->uri(), 'admin/item/requested/item/view/') ;
            //     });

            //     if (isset($reviewRoutes)) {
            //         foreach ($reviewRoutes as $route) {
            //             $validRoutes[] = $this->filterRoute(model: $review, route: $route, prefix: 'Review');
            //         }
            //     }
            // }

            // //subscriber
            // $storeSubscription = StoreSubscription::wherehas('store', function($query){
            //     $query->whereIn('store_business_model' ,['subscription','unsubscribed']);
            // })->where('store_id',$searchKeyword);

            // if ($storeSubscription){
            //     $storeSubscriptionRoutes = $adminRoutes->filter(function ($route) {
            //         return str_contains($route->uri(), 'subscription') && str_contains($route->uri(), 'subscriber-detail');
            //     });

            //     if (isset($storeSubscriptionRoutes)) {
            //         foreach ($storeSubscriptionRoutes as $route) {
            //             $validRoutes[] = $this->filterRoute(model: $storeSubscription, route: $route, prefix: 'Subscription');
            //         }
            //     }
            // }
        } else {
            //Store
            $stores = Store::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                return $query->where('module_id', $currentModuleId);
            })->where(function ($query) use ($searchKeyword) {
                $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('address', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('meta_title', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('meta_description', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhereHas('vendor', function ($query) use ($searchKeyword) {
                        return $query->where('f_name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('l_name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                            ->orWhereRaw("CONCAT(l_name, ' ', f_name) LIKE ?", ['%' . $searchKeyword . '%'])
                            ->orWhereRaw("CONCAT(l_name,f_name) LIKE ?", ['%' . $searchKeyword . '%'])
                            ->orWhereRaw("CONCAT(f_name,l_name) LIKE ?", ['%' . $searchKeyword . '%']);
                    });
            })
                ->get();

            if ($stores) {
                foreach ($stores as $store) {


                    if ($currentModuleType == 'rental') {

                        if ($store->vendor->status === 0) {
                            $storeRoutes = $adminRoutes->filter(function ($route) {
                                return str_contains($route->uri(), 'rental/provider') && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'details')  || str_contains($route->uri(), 'deny-requests'))
                                    && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                            });
                        } elseif ($store->vendor->status === null) {
                            $storeRoutes = $adminRoutes->filter(function ($route) {
                                return str_contains($route->uri(), 'rental/provider') && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'details')  || str_contains($route->uri(), 'pending-requests'))
                                    && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                            });
                        } else {
                            $storeRoutes = $adminRoutes->filter(function ($route) {
                                return str_contains($route->uri(), 'rental/provider') && str_contains($route->uri(), 'edit')
                                    && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                            });
                        }
                    } else {

                        if ($store->vendor->status === 0) {
                            $storeRoutes = $adminRoutes->filter(function ($route) {
                                return str_contains($route->uri(), 'store') && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'view')  || str_contains($route->uri(), 'deny-requests') || basename(parse_url($route->uri(), PHP_URL_PATH)) == 'recommended-store')
                                    && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                            });
                        } elseif ($store->vendor->status === null) {
                            $storeRoutes = $adminRoutes->filter(function ($route) {
                                return str_contains($route->uri(), 'store') && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'view')  || str_contains($route->uri(), 'pending-requests')  || basename(parse_url($route->uri(), PHP_URL_PATH)) == 'recommended-store')
                                    && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                            });
                        } else {
                            $storeRoutes = $adminRoutes->filter(function ($route) {
                                return str_contains($route->uri(), 'store') && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'view')  || basename(parse_url($route->uri(), PHP_URL_PATH)) == 'recommended-store')
                                    && !str_contains($route->uri(), 'withdraw-view')  && !str_contains($route->uri(), 'transactions');
                            });
                        }
                    }

                    if (isset($storeRoutes)) {
                        foreach ($storeRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $store, route: $route, type: 'store', prefix: 'Store');
                        }
                    }
                }
            }

            if ($currentModuleType !== 'rental') {
                //  Order
                $orders = Order::with('customer')->when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->where(function ($query) use ($searchKeyword) {

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
                        ->orWhereHas('store', function ($query) use ($searchKeyword) {
                            $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('address', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('meta_title', 'LIKE', '%' . $searchKeyword . '%')
                                ->orWhere('meta_description', 'LIKE', '%' . $searchKeyword . '%');
                        });
                })

                    ->get();

                if ($orders) {
                    $ordersRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/order/details');
                    });
                    if (isset($ordersRoutes)) {
                        foreach ($orders as $order) {
                            foreach ($ordersRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $order, route: $route, type: 'order', prefix: 'Order');
                            }
                        }
                    }
                }
                //Advertisement
                $advertisements = Advertisement::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->where(function ($query) use ($searchKeyword) {
                    $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('add_type', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($advertisements) {
                    $adsRoutes = $adminRoutes->filter(function ($route) {
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
                //Category
                $categories = Category::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($categories) {
                    $categoryRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'category') && str_contains($route->uri(), 'update') && !str_contains($route->uri(), 'category/update-priority');
                    });

                    if (isset($categoryRoutes)) {
                        foreach ($categories as $category) {
                            foreach ($categoryRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $category, route: $route, type: 'category', prefix: 'Category');
                            }
                        }
                    }
                }
                //Item
                $Items = Item::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($Items) {
                    $ItemRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/item/edit/') || str_contains($route->uri(), 'admin/item/view/');
                    });

                    if (isset($ItemRoutes)) {
                        foreach ($Items as $Item) {
                            foreach ($ItemRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $Item, route: $route, type: 'Item', prefix: 'Item');
                            }
                        }
                    }
                }

                //Campaign
                $campaigns = Campaign::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->where(function ($query) use ($searchKeyword) {
                    $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($campaigns) {
                    $campaignRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'campaign')
                            && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'view'));
                    });

                    if (isset($campaignRoutes)) {
                        foreach ($campaigns as $campaign) {
                            foreach ($campaignRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $campaign, route: $route, type: 'basic-campaign', prefix: 'Basic Campaign');
                            }
                        }
                    }
                }

                //ItemCampaign
                $itemCampaigns = ItemCampaign::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->where(function ($query) use ($searchKeyword) {
                    $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($itemCampaigns) {
                    $itemCampaignRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'campaign')
                            && (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'view'));
                    });

                    if (isset($itemCampaignRoutes)) {
                        foreach ($itemCampaigns as $itemCampaign) {
                            foreach ($itemCampaignRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $itemCampaign, route: $route, type: 'item-campaign', prefix: 'Item Campaign');
                            }
                        }
                    }
                }

                //Vehicle
                $vehicles = DmVehicle::where(function ($query) use ($searchKeyword) {
                    $query->where('type', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($vehicles) {
                    $vehicleRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'delivery-man/vehicle/edit');
                    });

                    if (isset($vehicleRoutes)) {
                        foreach ($vehicles as $vehicle) {
                            foreach ($vehicleRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $vehicle, route: $route, type: 'vehicle', prefix: 'Vehicle');
                            }
                        }
                    }
                }

                //DeliveryMan
                $deliveryMen = DeliveryMan::where(function ($query) use ($searchKeyword) {
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
                    $deliveryManRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'delivery-man/edit') || str_contains($route->uri(), 'delivery-man/preview');
                    });
                    if (isset($deliveryManRoutes)) {
                        foreach ($deliveryMen as $deliveryMan) {
                            foreach ($deliveryManRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $deliveryMan, route: $route, type: 'deliveryMan', name: $deliveryMan->f_name . ' ' . $deliveryMan->l_name, prefix: 'Delivery Man');
                            }
                        }
                    }
                }
                //tepmProduct
                $tepmProduct = TempProduct::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->whereAny([
                    'name',
                    'description',
                ], 'LIKE', "%$searchKeyword%")

                    ->get();

                if ($tepmProduct) {
                    $tepmProductRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/item/new/item/list') || str_contains($route->uri(), 'admin/item/requested/item/view/');
                    });
                    if (isset($tepmProductRoutes)) {
                        foreach ($tepmProduct as $tepmProduct) {
                            foreach ($tepmProductRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $tepmProduct, route: $route, prefix: 'New Product', name: $tepmProduct?->name);
                            }
                        }
                    }
                }
            }





            if (in_array($currentModuleType, ['food'])) {

                //AddOn
                $addOns = AddOn::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->wherehas('store', function ($query) use ($currentModuleId) {
                        $query->where('module_id', $currentModuleId);
                    });
                })
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($addOns) {
                    $addOnRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'addon') && str_contains($route->uri(), 'edit');
                    });

                    if (isset($addOnRoutes)) {
                        foreach ($addOns as $addOn) {
                            foreach ($addOnRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $addOn, route: $route, prefix: 'Addon');
                            }
                        }
                    }
                }
            }






            if (in_array($currentModuleType, ['grocery', 'pharmacy', 'ecommerce'])) {
                //unit
                $units = Unit::where(function ($query) use ($searchKeyword) {
                    $query->where('unit', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($units) {
                    $unitRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'unit')  && !str_contains($route->uri(), 'unit/export');
                    });

                    if (isset($unitRoutes)) {
                        foreach ($units as $unit) {
                            foreach ($unitRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $unit, route: $route, prefix: 'unit');
                            }
                        }
                    }
                }
                //Attribute
                $Attributes = Attribute::where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($Attributes) {
                    $AttributeRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'attribute')  && !str_contains($route->uri(), 'attribute/export');
                    });

                    if (isset($AttributeRoutes)) {
                        foreach ($Attributes as $Attribute) {
                            foreach ($AttributeRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $Attribute, route: $route, prefix: 'Attribute');
                            }
                        }
                    }
                }
            }

            if (in_array($currentModuleType, ['ecommerce'])) {
                //Brand
                $Brands = Brand::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where(function ($query) use ($currentModuleId) {
                        $query->where('module_id', $currentModuleId)
                            ->orWhere('module_id', null);
                    });
                })
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($Brands) {
                    $BrandRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'brand')  && !str_contains($route->uri(), 'brand/export') && !str_contains($route->uri(), 'brand/status');
                    });

                    if (isset($BrandRoutes)) {
                        foreach ($Brands as $Brand) {
                            foreach ($BrandRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $Brand, route: $route, prefix: 'Brand');
                            }
                        }
                    }
                }
            }








            //Coupon
            $coupons = Coupon::where('created_by', 'admin')->when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                return $query->where('module_id', $currentModuleId);
            })->where(function ($query) use ($searchKeyword) {
                $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('code', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('discount_type', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('coupon_type', 'LIKE', '%' . $searchKeyword . '%');
            })
                ->get();

            if ($coupons) {

                if ($currentModuleType == 'rental') {

                    $couponRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental/coupon') && (!str_contains($route->uri(), 'status') && !str_contains($route->uri(), 'export'));
                    });
                } else {

                    $couponRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'coupon/edit');
                    });
                }

                if (isset($couponRoutes)) {
                    foreach ($coupons as $coupon) {
                        foreach ($couponRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $coupon, route: $route, prefix: 'Coupon', searchKeyword: $coupon->title);
                        }
                    }
                }
            }

            //CashBack
            $cashBacks = CashBack::where(function ($query) use ($searchKeyword) {
                $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('cashback_type', 'LIKE', '%' . $searchKeyword . '%');
            })
                ->get();

            if ($cashBacks) {

                if ($currentModuleType == 'rental') {
                    $cashbackRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental/cashback') && !str_contains($route->uri(), 'status');
                    });
                } else {
                    $cashbackRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'cashback') && !str_contains($route->uri(), 'status');
                    });
                }



                if (isset($cashbackRoutes)) {
                    foreach ($cashBacks as $cashBack) {
                        foreach ($cashbackRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $cashBack, route: $route, prefix: 'Cashback', searchKeyword: $cashBack->title);
                        }
                    }
                }
            }

            //Banner
            $banners = Banner::where('created_by', 'admin')->when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                return $query->where('module_id', $currentModuleId);
            })->where(function ($query) use ($searchKeyword) {
                $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('type', 'LIKE', '%' . $searchKeyword . '%');
            })
                ->get();

            if ($banners) {
                if ($currentModuleType == 'rental') {
                    $bannerRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/rental/banner') && !str_contains($route->uri(), 'status') && !str_contains($route->uri(), 'export');
                    });
                } else {
                    $bannerRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/banner') && !str_contains($route->uri(), 'status') && !str_contains($route->uri(), 'export');
                    });
                }

                if (isset($bannerRoutes)) {
                    foreach ($banners as $banner) {
                        foreach ($bannerRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $banner, route: $route, prefix: 'Banner');
                        }
                    }
                }
            }

            //Zone
            $zones = Zone::where(function ($query) use ($searchKeyword) {
                $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('display_name', 'LIKE', '%' . $searchKeyword . '%');
            })
                ->get();

            if ($zones) {
                $zoneRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'business-settings/zone') &&
                        (str_contains($route->uri(), 'edit') || str_contains($route->uri(), 'zone/module-setup/'));
                });

                if (isset($zoneRoutes)) {
                    foreach ($zones as $zone) {
                        foreach ($zoneRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $zone, route: $route, type: 'zone', prefix: 'Zone');
                        }
                    }
                }
            }

            // ContactMessage
            $contactMessages = Contact::where(function ($query) use ($searchKeyword) {
                $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('subject', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('message', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('reply', 'LIKE', '%' . $searchKeyword . '%');
            })
                ->get();

            if ($contactMessages) {
                $contactRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'contact') && str_contains($route->uri(), 'view');
                });

                if (isset($contactRoutes)) {
                    foreach ($contactMessages as $contactMessage) {
                        foreach ($contactRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $contactMessage, route: $route, prefix: 'Contact');
                        }
                    }
                }
            }

            //Notification
            $notifications = Notification::where(function ($query) use ($searchKeyword) {
                $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
            })
                ->get();

            if ($notifications) {

                if ($currentModuleType == 'rental') {
                    $notificationRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental/notification') && str_contains($route->uri(), 'edit');
                    });
                } else {

                    $notificationRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'notification') && str_contains($route->uri(), 'edit');
                    });
                }

                if (isset($notificationRoutes)) {
                    foreach ($notifications as $notification) {
                        foreach ($notificationRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $notification, route: $route, prefix: 'Notification', name: $notification->title, searchKeyword: $notification->title);
                        }
                    }
                }
            }

            //customer
            $customers = User::where(function ($query) use ($searchKeyword) {
                $query->where('f_name', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('l_name', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                    ->orWhereRaw("CONCAT(f_name,l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                    ->orWhereRaw("CONCAT(l_name,f_name) LIKE ?", ['%' . $searchKeyword . '%']);
            })
                ->get();

            if ($customers) {
                $customerRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'users/customer/view');
                });

                if (isset($customerRoutes)) {
                    foreach ($customers as $customer) {
                        foreach ($customerRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $customer, route: $route, type: 'customer', name: $customer->f_name . ' ' . $customer->l_name, prefix: 'Customer');
                        }
                    }
                }
            }

            //WalletBonus
            $walletBonus = WalletBonus::where(function ($query) use ($searchKeyword) {
                $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('bonus_type', 'LIKE', '%' . $searchKeyword . '%');
            })
                ->get();

            if ($walletBonus) {
                $bonusRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'wallet/bonus');
                });

                if (isset($bonusRoutes)) {
                    foreach ($walletBonus as $bonus) {
                        foreach ($bonusRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $bonus, route: $route, type: 'bonus', prefix: 'Bonus');
                        }
                    }
                }
            }



            //Store Disbursement
            $storeDisbursements = Disbursement::where('created_for', 'store')
                ->where(function ($query) use ($searchKeyword) {
                    $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                })
                ->get();

            if ($storeDisbursements) {
                $storeDisbursementRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'store-disbursement') &&  str_contains($route->uri(), 'details');
                });


                if (isset($storeDisbursementRoutes)) {
                    foreach ($storeDisbursements as $storeDisbursement)
                        foreach ($storeDisbursementRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $storeDisbursement, route: $route, prefix: 'Store Disbursement');
                        }
                }
            }

            //DM Disbursement
            $dmDisbursements = Disbursement::where('created_for', 'delivery_man')
                ->where(function ($query) use ($searchKeyword) {
                    $query->where('title', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                })
                ->get();

            if ($dmDisbursements) {
                $dmDisbursementRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'dm-disbursement') &&  str_contains($route->uri(), 'details');
                });

                if (isset($dmDisbursementRoutes)) {
                    foreach ($dmDisbursements as $dmDisbursement) {
                        foreach ($dmDisbursementRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $dmDisbursement, route: $route, prefix: 'Delivery Man Disbursement');
                        }
                    }
                }
            }

            //     //Withdraw Request
            //     $withdrawRequests = WithdrawRequest::
            //         where(function($query) use ($searchKeyword) {
            //             $query->where('type', 'LIKE', '%' . $searchKeyword . '%')
            //                 ->orWhere('withdrawal_method_fields->account_name', 'LIKE', '%' . $searchKeyword . '%')
            //                 ->orWhere('withdrawal_method_fields->account_number', 'LIKE', '%' . $searchKeyword . '%')
            //                 ->orWhere('withdrawal_method_fields->email', 'LIKE', '%' . $searchKeyword . '%');
            //         })
            //         ->get();

            //     if ($withdrawRequests){
            //         $withdrawRequestRoutes = $adminRoutes->filter(function ($route) {
            //             return str_contains($route->uri(), 'withdraw') && str_contains($route->uri(), 'withdraw-view');
            //         });

            //         if (isset($withdrawRequestRoutes)) {
            //             foreach ($withdrawRequests as $withdrawRequest) {
            //                 foreach ($withdrawRequestRoutes as $route) {
            //                     $validRoutes[] = $this->filterRoute(model: $withdrawRequest, route: $route, prefix: 'Withdraw Request');
            //                 }
            //             }
            //         }
            //     }

            //Withdrawal Method
            $withdrawalMethods = WithdrawalMethod::where(function ($query) use ($searchKeyword) {
                $query->where('method_name', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhereRaw("JSON_SEARCH(method_fields, 'one', ?) IS NOT NULL", ['%' . $searchKeyword . '%']);
            })
                ->get();

            if ($withdrawalMethods) {
                $withdrawalMethodRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'withdraw-method') && str_contains($route->uri(), 'edit');
                });

                if (isset($withdrawalMethodRoutes)) {
                    foreach ($withdrawalMethods as $withdrawalMethod) {
                        foreach ($withdrawalMethodRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $withdrawalMethod, route: $route, prefix: 'Withdraw Method');
                        }
                    }
                }
            }

            //Admin Role
            $adminRoles = AdminRole::
                // whereNotIn('id', [1])
                where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                })
                ->get();

            if ($adminRoles) {
                $adminRoleRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'custom-role') && str_contains($route->uri(), 'edit');
                });

                if (isset($adminRoleRoutes)) {
                    foreach ($adminRoles as $adminRole) {
                        foreach ($adminRoleRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $adminRole, route: $route, prefix: 'Employee Role');
                        }
                    }
                }
            }
            //Admin Employee
            $adminEmpoyee = Admin::whereNotIn('id', [1, auth('admin')->user()->id])

                ->where(function ($query) use ($searchKeyword) {
                    $query->where('f_name', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('l_name', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                        ->orWhereRaw("CONCAT(f_name,l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                        ->orWhereRaw("CONCAT(l_name,f_name) LIKE ?", ['%' . $searchKeyword . '%']);
                })
                ->get();


            if ($adminEmpoyee) {
                $adminEmployeeRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'employee') && str_contains($route->uri(), 'edit');
                });

                if (isset($adminEmployeeRoutes)) {
                    foreach ($adminEmpoyee as $Employee) {
                        foreach ($adminEmployeeRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $Employee, route: $route, prefix: 'Employee');
                        }
                    }
                }
            }

            //Subscription Package
            $subscriptionPackages = SubscriptionPackage::where(function ($query) use ($searchKeyword) {
                $query->where('package_name', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('colour', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('text', 'LIKE', '%' . $searchKeyword . '%');
            })
                ->get();

            if ($subscriptionPackages) {
                $subscriptionPackageRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'subscription/subscriptionackage');
                });

                if (isset($subscriptionPackageRoutes)) {
                    foreach ($subscriptionPackages as $subscriptionPackage) {
                        foreach ($subscriptionPackageRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $subscriptionPackage, route: $route, prefix: 'Subscription');
                        }
                    }
                }
            }






            //Modules
            $Modules = Module::where(function ($query) use ($searchKeyword) {
                $query->where('module_name', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('module_type', 'LIKE', '%' . $searchKeyword . '%');
            })
                ->get();

            if ($Modules) {
                $moduleRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'business-settings/module') && !str_contains($route->uri(), 'export');
                });

                if (isset($moduleRoutes)) {
                    foreach ($Modules as $module) {
                        foreach ($moduleRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $module, route: $route, prefix: 'Module', name: $module->module_name);
                        }
                    }
                }
            }
            if (in_array($currentModuleType, ['grocery', 'ecommerce'])) {

                //flashSale
                $flashSales = FlashSale::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->where(function ($query) use ($searchKeyword) {
                    $query->where('title', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($flashSales) {
                    $flashSaleRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'flash-sale/') && !str_contains($route->uri(), 'publish');
                    });
                    if (isset($flashSaleRoutes)) {
                        foreach ($flashSales as $flashSale) {
                            foreach ($flashSaleRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $flashSale, route: $route, prefix: 'flashSale', name: $flashSale->title);
                            }
                        }
                    }
                }
            }


            if (in_array($currentModuleType, ['parcel'])) {
                //ParcelCategory
                $ParcelCategories = ParcelCategory::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })->where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($ParcelCategories) {
                    $parcelCategoryRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/parcel/category/') && (str_contains($route->uri(), 'edit'));
                    });
                    if (isset($parcelCategoryRoutes)) {
                        foreach ($ParcelCategories as $parcelCategory) {
                            foreach ($parcelCategoryRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $parcelCategory, route: $route, prefix: 'parcelCategory', name: $parcelCategory->name);
                            }
                        }
                    }
                }
            }

            if (in_array($currentModuleType, ['pharmacy'])) {
                //CommonCondition
                $CommonCondition = CommonCondition::where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                })
                    ->get();

                if ($CommonCondition) {
                    $commonconRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'admin/common-condition');
                    });
                    if (isset($commonconRoutes)) {
                        foreach ($CommonCondition as $commoncon) {
                            foreach ($commonconRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $commoncon, route: $route, prefix: 'Common Condition', name: $commoncon->name);
                            }
                        }
                    }
                }
            }


            //walletTransaction
            $walletTransaction = WalletTransaction::with('user')->whereAny([
                'transaction_id',
                'transaction_type',
                'reference',
            ], 'LIKE', "%$searchKeyword%")
                ->orwherehas('user', function ($query) use ($searchKeyword) {
                    $query->whereAny([
                        'f_name',
                        'l_name',
                        'phone',
                        'email',
                    ], 'LIKE', "%$searchKeyword%");
                })
                ->get();

            if ($walletTransaction) {
                $walletTransactionRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'users/customer/wallet/report');
                });
                if (isset($walletTransactionRoutes)) {
                    foreach ($walletTransaction as $walletTransaction) {
                        foreach ($walletTransactionRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $walletTransaction, route: $route, prefix: 'Wallet Transaction', name: $walletTransaction?->user?->f_name . ' ' . $walletTransaction?->user?->l_name);
                        }
                    }
                }
            }


            //LoyaltyPoint
            $LoyaltyPoint = LoyaltyPointTransaction::with('user')->whereAny([
                'transaction_id',
                'transaction_type',
                'reference',
            ], 'LIKE', "%$searchKeyword%")
                ->orwherehas('user', function ($query) use ($searchKeyword) {
                    $query->whereAny([
                        'f_name',
                        'l_name',
                        'phone',
                        'email',
                    ], 'LIKE', "%$searchKeyword%");
                })
                ->get();

            if ($LoyaltyPoint) {
                $LoyaltyPointRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'users/customer/loyalty-point/report');
                });
                if (isset($LoyaltyPointRoutes)) {
                    foreach ($LoyaltyPoint as $LoyaltyPoint) {
                        foreach ($LoyaltyPointRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $LoyaltyPoint, route: $route, prefix: 'Wallet Transaction', name: $LoyaltyPoint?->user?->f_name . ' ' . $LoyaltyPoint?->user?->l_name);
                        }
                    }
                }
            }


            //Newsletter
            $Newsletter = Newsletter::whereAny([
                'email',
            ], 'LIKE', "%$searchKeyword%")

                ->get();

            if ($Newsletter) {
                $NewsletterRoutes = $adminRoutes->filter(function ($route) {
                    return str_contains($route->uri(), 'admin/users/customer/subscribed');
                });
                if (isset($NewsletterRoutes)) {
                    foreach ($Newsletter as $Newsletter) {
                        foreach ($NewsletterRoutes as $route) {
                            $validRoutes[] = $this->filterRoute(model: $Newsletter, route: $route, prefix: 'Customers', name: $Newsletter?->email);
                        }
                    }
                }
            }





            //     //Store Subscription
            //     $storeSubscriptions = StoreSubscription::with('package')
            //         ->whereHas('package', function ($query) use ($searchKeyword){
            //             $query->where('package_name', 'LIKE', '%' . $searchKeyword . '%')
            //                 ->orWhere('colour', 'LIKE', '%' . $searchKeyword . '%')
            //                 ->orWhere('text', 'LIKE', '%' . $searchKeyword . '%');
            //         })
            //         ->get();

            //     if ($storeSubscriptions){
            //         $storeSubscriptionRoutes = $adminRoutes->filter(function ($route) {
            //             return str_contains($route->uri(), 'subscription') && str_contains($route->uri(), 'subscriber-detail');
            //         });

            //         if (isset($storeSubscriptionRoutes)) {
            //             foreach ($storeSubscriptions as $storeSubscription) {
            //                 foreach ($storeSubscriptionRoutes as $route) {
            //                     $validRoutes[] = $this->filterRoute(model: $storeSubscription, route: $route, name: $storeSubscription?->store?->name, prefix: 'Subscription');
            //                 }
            //             }
            //         }
            //     }





            if ($currentModuleType == 'rental') {

                //  Trips
                $trips = Trips::with('customer')->when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->where('module_id', $currentModuleId);
                })
                    ->whereHas('customer', function ($query) use ($searchKeyword) {
                        $query->where('f_name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('l_name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                            ->orWhereRaw("CONCAT(f_name,l_name) LIKE ?", ['%' . $searchKeyword . '%'])
                            ->orWhereRaw("CONCAT(l_name,f_name) LIKE ?", ['%' . $searchKeyword . '%']);
                    })
                    ->orWhereHas('provider', function ($query) use ($searchKeyword) {
                        $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('phone', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('address', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('meta_title', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('meta_description', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();

                if ($trips) {
                    $tripsRoutes = $adminRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'rental/trip/details')) && !str_contains($route->uri(), 'transactions/rental/trip/details/')  && !str_contains($route->uri(), 'expense-report') && !str_contains($route->uri(), 'export');
                    });
                    if (isset($tripsRoutes)) {
                        foreach ($trips as $trip) {
                            foreach ($tripsRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $trip, route: $route, type: 'trip', prefix: 'Trip', name: $trip->id, searchKeyword: $trip->id);
                            }
                        }
                    }
                }

                //Vehicle
                $vehicles = Vehicle::when(is_numeric($currentModuleId), function ($query) use ($currentModuleId) {
                    return $query->wherehas('provider', function ($query) use ($currentModuleId) {
                        $query->where('module_id', $currentModuleId);
                    });
                })
                    ->where(function ($query) use ($searchKeyword) {
                        $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('description', 'LIKE', '%' . $searchKeyword . '%');
                    })
                    ->get();


                if ($vehicles) {
                    $vehiclesRoutes = $adminRoutes->filter(function ($route) {
                        return (str_contains($route->uri(), 'vehicle/details') || str_contains($route->uri(), 'vehicle/update'));
                    });

                    if (isset($vehiclesRoutes)) {
                        foreach ($vehicles as $vehicle) {
                            foreach ($vehiclesRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $vehicle, route: $route, type: 'Vehicle', prefix: 'Vehicle', name: $vehicle?->name);
                            }
                        }
                    }
                }

                $VehicleCategorys = VehicleCategory::where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                })->get();


                if ($VehicleCategorys) {
                    $VehicleCategorysRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental/category/edit/');
                    });

                    if (isset($VehicleCategorysRoutes)) {
                        foreach ($VehicleCategorys as $VehicleCategory) {
                            foreach ($VehicleCategorysRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $VehicleCategory, route: $route, type: 'VehicleCategory', prefix: 'VehicleCategory', name: $VehicleCategory?->name, searchKeyword: $VehicleCategory?->name);
                            }
                        }
                    }
                }


                $VehicleBrand = VehicleBrand::where(function ($query) use ($searchKeyword) {
                    $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                })->get();

                if ($VehicleBrand) {
                    $VehicleBrandRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'rental/brand/edit');
                    });

                    if (isset($VehicleBrandRoutes)) {
                        foreach ($VehicleBrand as $Vehicle_Brand) {
                            foreach ($VehicleBrandRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $Vehicle_Brand, route: $route, type: 'VehicleBrand', prefix: 'VehicleBrand', name: $Vehicle_Brand?->name, searchKeyword: $Vehicle_Brand?->name);
                            }
                        }
                    }
                }


                // $VehicleDriver = VehicleDriver::find($searchKeyword);
                // if ($VehicleDriver) {
                //     $VehicleDriverRoutes = $adminRoutes->filter(function ($route) {
                //         return str_contains($route->uri(), 'driver/details') || str_contains($route->uri(), 'driver/update');
                //     });

                //     if (isset($VehicleDriverRoutes)) {
                //         foreach ($VehicleDriverRoutes as $route) {
                //             $validRoutes[] = $this->filterRoute(model: $VehicleDriver, route: $route, type: 'VehicleDriver', prefix: 'VehicleDriver', name: $VehicleDriver->first_name, searchKeyword: $VehicleDriver->first_name);
                //         }
                //     }
                // }

                $VehicleReview = VehicleReview::where(function ($query) use ($searchKeyword) {
                    $query->where('comment', 'LIKE', '%' . $searchKeyword . '%')
                        ->orwhere('reply', 'LIKE', '%' . $searchKeyword . '%');
                })->get();

                if ($VehicleReview) {
                    $VehicleReviewRoutes = $adminRoutes->filter(function ($route) {
                        return str_contains($route->uri(), 'vehicle/review-list');
                    });

                    if (isset($VehicleReviewRoutes)) {
                        foreach ($VehicleReview as $Vehicle_Review) {
                            foreach ($VehicleReviewRoutes as $route) {
                                $validRoutes[] = $this->filterRoute(model: $Vehicle_Review, route: $route, type: 'Vehicle_Review', prefix: 'VehicleReview', name: $Vehicle_Review?->vehicle?->name, searchKeyword: $Vehicle_Review?->vehicle?->name);
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

    private function filterRoute($model, $route, $type = null, $name = null, $prefix = null, $module_type = null, $searchKeyword = null): array
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

        if ($type == 'store' && $model->vendor->status == null) {
            $fullURL = $formattedRouteName == 'View' ? $fullURL . '/pending-list' : $fullURL;
        }

        if ($type === 'basic-campaign') {
            $baseURL = url('/') . '/admin/campaign/basic';
            $action = $formattedRouteName === 'Edit' ? 'edit' : 'view';
            $fullURL = "{$baseURL}/{$action}/{$model->id}";
            $uriWithParameter = "admin/campaign/basic/{$action}/{$model->id}";
        }

        if ($type === 'item-campaign') {
            $baseURL = url('/') . '/admin/campaign/item';
            $action = $formattedRouteName === 'Edit' ? 'edit' : 'view';
            $fullURL = "{$baseURL}/{$action}/{$model->id}";
            $uriWithParameter = "admin/campaign/item/{$action}/{$model->id}";
        }


        $routeName = $prefix ? $prefix . ' ' . $formattedRouteName : $formattedRouteName;
        $routeName = $name ? $routeName . ' - (' . $name . ')' : $routeName;
        $routeName = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $routeName);
        $routeName = trim(preg_replace('/\s+/', ' ', $routeName));

        return [
            'routeName' => $routeName,
            'URI' => $uriWithParameter,
            'fullRoute' => $fullURL,
            'module_type' => $module_type,
            'data_from' => 'database',
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeClickedRoute(Request $request): JsonResponse
    {
        $userId = auth('admin')->user()->id;
        $userType = auth('admin')->user()->role_id ? 'admin' : 'admin-employee';
        $routeName = $request->input('routeName');
        $routeUri = $request->input('routeUri');
        $routeFullUrl = $request->input('routeFullUrl');
        $searchKeyword = $request->input('searchKeyword');
        $module_id = $request->input('moduleId');

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
            $clickedRoute->module_id = $module_id;
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
        $userId = auth('admin')->user()->id;
        $userType = auth('admin')->user()->role_id ? 'admin' : 'admin-employee';

        $recentSearches = RecentSearch::where('user_id', $userId)
            ->where(function ($query) {
                $query->whereNull('module_id')->orwhere('module_id', config('module.current_module_id'));
            })
            ->where('user_type', $userType)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($recentSearches);
    }


    private function getAjaxRoutes($adminRoutes): array
    {
        $jsonRoutes = [];
        $route_names = [];

        foreach ($adminRoutes as $route) {
            $uri = $route->uri();
            $action = $route->getAction();

            $controller = $action['controller'] ?? null;
            if ($controller) {
                list($controllerClass, $method) = explode('@', $controller);

                if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
                    $reflectionMethod = new \ReflectionMethod($controllerClass, $method);
                    $filename = $reflectionMethod->getFileName();
                    $startLine = $reflectionMethod->getStartLine();
                    $endLine = $reflectionMethod->getEndLine();

                    $file = file($filename);
                    $methodBody = implode('', array_slice($file, $startLine - 1, $endLine - $startLine + 1));

                    if (strpos($methodBody, 'return response()->json') !== false) {
                        $jsonRoutes[] = [
                            'method' => implode('|', $route->methods()),
                            'uri' => $uri,
                            'controller' => $controller
                        ];
                    }
                }
            }
        }

        foreach ($jsonRoutes as $route) {
            $route_names[] = $route['uri'];
        }

        return $route_names;
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
