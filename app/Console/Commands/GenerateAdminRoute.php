<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class GenerateAdminRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:admin-route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate admin formatted routes';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $routes = Route::getRoutes();
        $adminRoutes = collect($routes->getRoutesByMethod()['GET'])->filter(function ($route) {
            return Str::startsWith($route->uri(), 'admin');
        });

        $excludeTermsRoute = [
            'print', 'download', 'export', 'edit', 'update', 'invoice', 'child', 'update-default-status', 'update-status',
            'system-currency', 'status', 'paidStatus', 'priority', 'remove-proof-image', 'select-customer', 'orders', 'logs',
            'refund_mode', 'account-transaction/create', 'provide-deliveryman-earnings/create', 'system-addons', 'social-media/create',
            'drivemond'
        ];

        $excludeTermsAjax = $this->getAjaxRoutes($adminRoutes);
        $jsonFilePath = public_path('admin_formatted_routes.json');
        $excludeTerms = array_merge($excludeTermsAjax, $excludeTermsRoute);
        $formattedRoutes = [];

        foreach ($adminRoutes as $route) {
            $uri = $route->uri();
            $exclude = collect($excludeTerms)->contains(function ($term) use ($uri) {
                return Str::contains($uri, $term);
            });

            if (!$exclude) {
                $hasParameters = preg_match('/\{(.*?)\}/', $uri);
                if (!$hasParameters) {
                    $routeName = $this->getRouteName($route->getName());
                    $bladePath = $this->getBladePathFromController($route);
                    $formattedRoutes= $this->genetateRouteJsonFileFormate($formattedRoutes,$bladePath,$routeName, $uri);

                }
                // else{
                //     info("Route excluded: " . $route->getName() . " - " . $uri);
                // }
            }
        }
        $formattedRoutes= $this->manualyAddedBladePath($formattedRoutes);


        if (file_exists($jsonFilePath)) {
            $fileContents = file_get_contents($jsonFilePath);
            $existingRoutes = json_decode($fileContents, true) ?? [];

            $newRoutes = array_filter($formattedRoutes, function ($newRoute) use ($existingRoutes) {
                foreach ($existingRoutes as $existingRoute) {
                    if ($existingRoute['URI'] === $newRoute['URI']) {
                        return false;
                    }
                }
                return true;
            });

            if (!empty($newRoutes)) {
                $updatedRoutes = array_merge($existingRoutes, $newRoutes);
               $updatedRoutes= $this->manualyAddedBladePartialsPath($updatedRoutes);
                file_put_contents($jsonFilePath, json_encode($updatedRoutes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        } else {
            $updatedRoutes= $this->manualyAddedBladePartialsPath($formattedRoutes);
            file_put_contents($jsonFilePath, json_encode($formattedRoutes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        return 0;
    }

    function getAjaxRoutes ($adminRoutes): array
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
            $route_names[] =$route['uri'];
        }

        return $route_names;
    }

    function getBladePathFromController($route)
    {
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

                if (preg_match("/view\\(['\"](.*?)['\"]/", $methodBody, $matches)) {
                    $bladePath = $matches[1];

                    if (preg_match_all('/\{\$(\w+)\}/', $bladePath, $varMatches)) {
                        $moduleTypes =config('module.module_type');
                        $viewBasePaths =null;

                        foreach ($moduleTypes as $type) {
                            $resolvedPath = $bladePath;
                            foreach ($varMatches[1] as $varName) {
                                $resolvedPath = str_replace('{$' . $varName . '}', $type, $resolvedPath);
                            }
                            $filePath = str_replace('.', '/', $resolvedPath);
                            if (View::exists($filePath)) {
                                $fullPath = View::getFinder()->find($filePath);
                                if (file_exists($fullPath)) {
                                    $viewBasePaths[$type] =$filePath;
                                }
                            }
                        }

                        return $viewBasePaths;
                    }
                    return str_replace('.', '/', $bladePath);
                }
            }
        }

        return null;
    }

    function getTextDataFromBladeFile($viewPath): ? string
    {
        try {
            if (!$viewPath) {
                return null;
            }
            if (!View::exists($viewPath)) {
                return null;
            }
            $viewFilePath = View::getFinder()->find($viewPath);
            if (!File::exists($viewFilePath)) {
                return null;
            }

            $pattern = "/translate\('([^']+)'\)/";
            $textData = [];

            $content = File::get($viewFilePath);
            preg_match_all($pattern, $content, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $text) {
                    $cleanedText = preg_replace("/^messages\./", "", $text);
                    $cleanedText = preg_replace("/[_:\?\.,-]+/", " ", $cleanedText);
                    $cleanedText = preg_replace("/\d+/", "", $cleanedText);
                    $cleanedText = preg_replace("/\s+/", " ", trim($cleanedText));

                    $textData[] = $cleanedText;
                }
            }

            $textData = array_unique($textData);
            $finalText = implode(" ", $textData);

            return trim($finalText);
        }
        catch (\Exception $exception) {
            info([$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            return null;
        }
    }

    private function manualyAddedBladePartialsPath(array $formattedArray): array
    {
        try {
            $bladePartials = [
                'admin-views/dashboard-grocery' => [
                    'admin-views.partials._top-restaurants',
                    'admin-views.partials._zone-change',
                    'admin-views.partials._popular-restaurants',
                    'admin-views.partials._top-selling-foods',
                    'admin-views.partials._top-rated-foods',
                    'admin-views.partials._top-deliveryman',
                    'admin-views.partials._top-customer'
                ],
                'admin-views/dashboard-food' => [
                    'admin-views.partials._top-restaurants',
                    'admin-views.partials._zone-change',
                    'admin-views.partials._popular-restaurants',
                    'admin-views.partials._top-selling-foods',
                    'admin-views.partials._top-rated-foods',
                    'admin-views.partials._top-deliveryman',
                    'admin-views.partials._top-customer'
                ],
                'admin-views/dashboard-ecommerce' => [
                    'admin-views.partials._top-restaurants',
                    'admin-views.partials._zone-change',
                    'admin-views.partials._popular-restaurants',
                    'admin-views.partials._top-selling-foods',
                    'admin-views.partials._top-rated-foods',
                    'admin-views.partials._top-deliveryman',
                    'admin-views.partials._top-customer'
                ],
                'admin-views/dashboard-pharmacy' => [
                    'admin-views.partials._top-restaurants',
                    'admin-views.partials._zone-change',
                    'admin-views.partials._popular-restaurants',
                    'admin-views.partials._top-selling-foods',
                    'admin-views.partials._top-rated-foods',
                    'admin-views.partials._top-deliveryman',
                    'admin-views.partials._top-customer'
                ],
                'admin-views/dashboard-parcel' => [
                    'admin-views.partials._zone-change',
                    'admin-views.partials._top-deliveryman',
                    'admin-views.partials._top-customer'
                ],

            ];

            foreach ($formattedArray as $index => $item) {
                $bladePath = $item['bladePath'] ?? null;
                if ($bladePath &&  array_key_exists($bladePath, $bladePartials)) {
                    $keywords = $item['keywords'] ?? '';
                    foreach ($bladePartials[$bladePath] as $partialPath) {
                        $text = $this->getTextDataFromBladeFile($partialPath);
                        if ($text) {
                            $keywords .= ' ' . $text;
                        }
                    }
                    $formattedArray[$index]['keywords'] = trim($keywords);
                }

            }
        } catch (\Exception $exception) {
            info([$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
        }
        return $formattedArray;
    }
    private function manualyAddedBladePath($formattedRoutes): array
    {
        $array = [
            'admin-views.order.offline_verification_list' => ['admin/order/offline/payment/list/all'],
            'admin-views.order.list' => ['admin/refund/requested'],
            'admin-views.zone.index' => ['admin/business-settings/zone'],
            'admin-views.category.index' => ['admin/category/add?position=0'],
            'admin-views.category.sub-index' => ['admin/category/add?position=1'],
            'admin-views.category.bulk-import' => ['admin/category/bulk-import'],
            'admin-views.category.bulk-export' => ['admin/category/bulk-export'],
            'admin-views.product.bulk-export' => ['admin/item/bulk-export'],
            'admin-views.vendor.bulk-export' => ['admin/store/bulk-export'],
            'admin-views.addon.bulk-import' => ['admin/addon/bulk-import'],
            'admin-views.addon.bulk-export' => ['admin/addon/bulk-export'],
            'admin-views.wallet-bonus.index' => ['admin/users/customer/wallet/bonus'],
            'admin-views.dm-vehicle.list' => ['admin/users/delivery-man/vehicle'],
            'admin-views.delivery-man.index' => ['admin/users/delivery-man/add'],
            'admin-views.delivery-man.list' => ['admin/users/delivery-man'],
            'admin-views.delivery-man.new' => ['admin/users/delivery-man/new'],
            'admin-views.delivery-man.deny' => ['admin/users/delivery-man/deny'],
            'admin-views.custom-role.create' => ['admin/users/custom-role/create'],
            'admin-views.employee.add-new' => ['admin/users/employee/store'],
            'admin-views.campaign.item.list' => ['admin/campaign/item/list'],
            'admin-views.campaign.basic.list' => ['admin/campaign/basic/list'],
            'admin-views.business-settings.settings.order-index' => ['admin/business-settings/business-setup/order'],
            'admin-views.business-settings.settings.business-index' => ['admin/business-settings/business-setup'],
            'admin-views.business-settings.settings.refund-index' => ['admin/business-settings/business-setup/refund-settings'],
            'admin-views.business-settings.settings.store-index' => ['admin/business-settings/business-setup/store'],
            'admin-views.business-settings.settings.deliveryman-index' => ['admin/business-settings/business-setup/deliveryman'],
            'admin-views.business-settings.settings.customer-index' => ['admin/business-settings/business-setup/customer'],
            'admin-views.business-settings.settings.priority-index' => ['admin/business-settings/business-setup/priority'],
            'admin-views.business-settings.language.index' => ['admin/business-settings/language'],
            'admin-views.business-settings.landing-index' => ['admin/business-settings/business-setup/landing-page'],
            'admin-views.business-settings.websocket-index' => ['admin/business-settings/business-setup/websocket'],
            'admin-views.business-settings.settings.automated-message' => ['admin/business-settings/business-setup/automated-message'],
            'admin-views.business-settings.settings.disbursement-index' => ['admin/business-settings/business-setup/disbursement'],
            'admin-views.business-settings.landing-page-settings.admin-fixed-data' => ['admin/business-settings/pages/admin-landing-page-settings/fixed-data'],
            'admin-views.business-settings.landing-page-settings.admin-promotional-section' => ['admin/business-settings/pages/admin-landing-page-settings/promotional-section'],
            'admin-views.business-settings.landing-page-settings.admin-feature-list' => ['admin/business-settings/pages/admin-landing-page-settings/feature-list'],
            'admin-views.business-settings.landing-page-settings.admin-earn-money' => ['admin/business-settings/pages/admin-landing-page-settings/earn-money'],
            'admin-views.business-settings.landing-page-settings.admin-landing-why-choose' => ['admin/business-settings/pages/admin-landing-page-settings/why-choose-us'],
            'admin-views.business-settings.landing-page-settings.admin-landing-available-zone' => ['admin/business-settings/pages/admin-landing-page-settings/available-zone'],
            'admin-views.business-settings.landing-page-settings.admin-landing-download-apps' => ['admin/business-settings/pages/admin-landing-page-settings/download-apps'],
            'admin-views.business-settings.landing-page-settings.admin-landing-contact' => ['admin/business-settings/pages/admin-landing-page-settings/contact-us'],
            'admin-views.business-settings.landing-page-settings.admin-landing-background-color' => ['admin/business-settings/pages/admin-landing-page-settings/background-color'],

            'admin-views.business-settings.landing-page-settings.react-landing-page-header' => ['admin/business-settings/pages/react-landing-page-settings/header'],
            'admin-views.business-settings.landing-page-settings.react-landing-page-trust-section' => ['admin/business-settings/pages/react-landing-page-settings/trust-section'],
            'admin-views.business-settings.landing-page-settings.react-landing-available-zone' => ['admin/business-settings/pages/react-landing-page-settings/available-zone'],
            'admin-views.business-settings.landing-page-settings.react-landing-promotion-banners' => ['admin/business-settings/pages/react-landing-page-settings/promotion-banner'],
            'admin-views.business-settings.landing-page-settings.react-landing-download-apps' => ['admin/business-settings/pages/react-landing-page-settings/download-user-app'],
            'admin-views.business-settings.landing-page-settings.react-landing-page-popular-clients' => ['admin/business-settings/pages/react-landing-page-settings/popular-clients'],
            'admin-views.business-settings.landing-page-settings.react-landing-page-download-seller-app' => ['admin/business-settings/pages/react-landing-page-settings/download-seller-app'],
            'admin-views.business-settings.landing-page-settings.react-landing-page-download-deliveryman-app' => ['admin/business-settings/pages/react-landing-page-settings/download-deliveryman-app'],
            'admin-views.business-settings.landing-page-settings.react-landing-page-banner-section' => ['admin/business-settings/pages/react-landing-page-settings/banner-section'],
            'admin-views.business-settings.landing-page-settings.react-landing-testimonial' => ['admin/business-settings/pages/react-landing-page-settings/testimonials'],
            'admin-views.business-settings.landing-page-settings.react-landing-page-gallery' => ['admin/business-settings/pages/react-landing-page-settings/gallery'],
            'admin-views.business-settings.landing-page-settings.react-landing-page-highlight-section' => ['admin/business-settings/pages/react-landing-page-settings/highlight-section'],
            'admin-views.business-settings.landing-page-settings.react-landing-page-faq' => ['admin/business-settings/pages/react-landing-page-settings/faq'],
            'admin-views.business-settings.landing-page-settings.react-landing-fixed-data' => ['admin/business-settings/pages/react-landing-page-settings/fixed-data'],
            'admin-views.business-settings.landing-page-settings.react-landing-page-company' => ['admin/business-settings/pages/react-landing-page-settings/company-intro'],
            'admin-views.business-settings.landing-page-settings.react-landing-earn-money' => ['admin/business-settings/pages/react-landing-page-settings/earn-money'],
            'admin-views.business-settings.landing-page-settings.react-landing-business' => ['admin/business-settings/pages/react-landing-page-settings/business-section'],

            'admin-views.business-settings.landing-page-settings.flutter-fixed-data' => ['admin/business-settings/pages/flutter-landing-page-settings/fixed-data'],
            'admin-views.business-settings.landing-page-settings.flutter-landing-page-special-criteria' => ['admin/business-settings/pages/flutter-landing-page-settings/special-criteria'],
            'admin-views.business-settings.landing-page-settings.flutter-landing-page-available-zone' => ['admin/business-settings/pages/flutter-landing-page-settings/available-zone'],
            'admin-views.business-settings.landing-page-settings.flutter-download-apps' => ['admin/business-settings/pages/flutter-landing-page-settings/download-apps'],
            'admin-views.business-settings.landing-page-settings.flutter-landing-page-join-as' => ['admin/business-settings/pages/flutter-landing-page-settings/join-as'],
            'admin-views.file-manager.index' => ['admin/business-settings/file-manager/index'],
            'admin-views.business-settings.notification_setup' => ['admin/business-settings/notification-setup'],
            'admin-views.business-settings.email-format-setting.admin-email-formats.forgot-pass-format' => ['admin/business-settings/email-setup/admin/forgot-password'],
            'admin-views.coupon.index' => ['admin/coupon'],
            'rental::admin.coupon.list' => ['admin/rental/coupon'],
            'rental::admin.push-notification.list' => ['admin/rental/notification'],
            'admin-views.notification.index' => ['admin/notification'],
            'admin-views.attribute.index' => ['admin/attribute'],
            'admin-views.unit.index' => ['admin/unit'],
            'admin-views.common-condition.index' => ['admin/common-condition'],
            'admin-views.brand.index' => ['admin/brand'],
            'admin-views.order.distaptch_list' => ['admin/parcel/dispatch/searching_for_deliverymen'],
            'admin-views.order.distaptch_list' => ['admin/parcel/dispatch/on_going'],
            'rental::admin.dashboard-rental' => ['admin/rental'],
            'rental::admin.trip.list' => ['admin/rental/trip'],
            'rental::admin.banner.list' => ['admin/rental/banner'],
            'rental::admin.cashback.list' => ['admin/rental/cashback'],
            'rental::admin.category.list' => ['admin/rental/category/list'],
            'rental::admin.brand.list' => ['admin/rental/brand/list'],
            'rental::admin.vehicle.create' => ['admin/rental/provider/vehicle/create'],
            'rental::admin.vehicle.list' => ['admin/rental/provider/vehicle/list'],
            'rental::admin.vehicle.review-list' => ['admin/rental/provider/vehicle/review-list'],
            'rental::admin.vehicle.bulk-import' => ['admin/rental/provider/vehicle/bulk-import'],
            'rental::admin.vehicle.bulk-export' => ['admin/rental/provider/vehicle/bulk-export'],
            'rental::admin.provider.new-reques' => ['admin/rental/provider/new-requests'],
            'rental::admin.provider.create' => ['admin/rental/provider/create'],
            'rental::admin.provider.list' => ['admin/rental/provider/list'],
            'rental::admin.provider.bulk-export' => ['dmin/rental/provider/bulk-export'],
            'rental::admin.provider.bulk-import' => ['admin/rental/provider/bulk-import'],
            'rental::admin.home-page-setup.download-app' => ['admin/rental/settings'],


        ];

        foreach ($array as $bladePath => $value) {
            foreach ($value as $uri) {
                $formattedRoutes=  $this->genetateRouteJsonFileFormate($formattedRoutes,$bladePath,$this->getRouteName($bladePath), $uri);
            }
        }
        return $formattedRoutes;
    }


    private function genetateRouteJsonFileFormate($formattedRoutes,$bladePath, $routeName, $uri) : array  {
        $bladePaths = is_array($bladePath) ? $bladePath : [null => $bladePath];

        foreach ($bladePaths as $moduleType => $path) {
            if (!$path) continue;

            if (strpos($path, '::') !== false) {
                list($moduleName, $viewFileName) = explode('::', $path);
                if (Module::where('module_type' , $moduleName)->exists()) {
                    $moduleType=$moduleName;
                }
            }

            if($moduleType  == ""){
                $containsParcel = (
                    stripos($path, 'parcel') !== false ||
                    stripos($routeName, 'parcel') !== false ||
                    stripos($uri, 'parcel') !== false
                );
                if($containsParcel){
                    $moduleType='parcel';
                }
                $containsRental = (
                    stripos($path, 'rental') !== false ||
                    stripos($routeName, 'rental') !== false ||
                    stripos($uri, 'rental') !== false
                );
                if($containsRental){
                    $moduleType='rental';
                }
            }

            $keywords = $this->getTextDataFromBladeFile($path);
            $keywords = ucwords(str_replace(['.', '_', '-'], ' ', $keywords));

            if (strlen($keywords) > 3) {
                $formattedRoutes[] = [
                    'routeName'   => $routeName,
                    'URI'         => $uri,
                    'keywords'    => $keywords,
                    'bladePath'   => $path,
                    'moduleType'  => $moduleType  !== "" ? [$moduleType] : [],
                    'isModified'  => false,
                ];
            }
        }
        return $formattedRoutes;
    }

    private function getRouteName($actualRouteName){
        $routeNameParts = explode('.', $actualRouteName);
        if (count($routeNameParts) >= 2) {
            $lastPart = $routeNameParts[count($routeNameParts) - 1];
            $secondLastPart = $routeNameParts[count($routeNameParts) - 2];

            if (strtolower($lastPart) === 'index') {
                $lastPart = 'List';
            }

            $lastPartWords = explode(' ', str_replace(['_', '-'], ' ', $lastPart));
            $secondLastPartWords = explode(' ', str_replace(['_', '-'], ' ', $secondLastPart));
            $allWords = array_merge($secondLastPartWords, $lastPartWords);
            $uniqueWords = [];

            foreach ($allWords as $word) {
                $lowerWord = strtolower($word);
                if (empty($uniqueWords) || strtolower(end($uniqueWords)) !== $lowerWord) {
                    $uniqueWords[] = $word;
                }
            }

            if (count($uniqueWords) > 1 && strtolower($uniqueWords[0]) === strtolower(end($uniqueWords))) {
                array_shift($uniqueWords);
            }

            $uniqueWords = array_filter($uniqueWords, function ($word) {
                return strtolower($word) !== 'rental';
            });

            $routeName = ucwords(implode(' ', $uniqueWords));
        } else {
            $routeName = ucwords(str_replace(['.', '_', '-'], ' ', Str::afterLast($actualRouteName, '.')));
        }
        return $routeName;
    }
}
