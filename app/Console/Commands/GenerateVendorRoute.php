<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class GenerateVendorRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:vendor-route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate vendor formatted routes';

    /**
     * Execute the console command.
     */

    public function handle(): int
    {
        $routes = Route::getRoutes();
        $restaurantRoutes = collect($routes->getRoutesByMethod()['GET'])->filter(function ($route) {
            return Str::startsWith($route->uri(), 'vendor-panel');
        });

        $excludeTermsRoute = [
            'print', 'download', 'export', 'edit', 'update', 'invoice', 'child', 'update-default-status', 'update-status',
            'system-currency', 'status', 'paidStatus', 'priority'
        ];

        $excludeTermsAjax = $this->getAjaxRoutes($restaurantRoutes);
        $jsonFilePath = public_path('vendor_formatted_routes.json');
        $excludeTerms = array_merge($excludeTermsAjax, $excludeTermsRoute);
        $formattedRoutes = [];

        foreach ($restaurantRoutes as $route) {
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
                file_put_contents($jsonFilePath, json_encode($updatedRoutes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        } else {
            file_put_contents($jsonFilePath, json_encode($formattedRoutes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        return 0;
    }


    function getAjaxRoutes($restaurantRoutes): array
    {
        $jsonRoutes = [];
        $route_names = [];

        foreach ($restaurantRoutes as $route) {
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

    private function manualyAddedBladePath($formattedRoutes): array
    {
        $array = [
            'vendor-views.product.bulk-export' => ['vendor-panel/item/bulk-export'],
            'vendor-views.messages.index' => ['vendor-panel/message/list'],
            'vendor-views.business-settings.restaurant-index' => ['vendor-panel/business-settings/store-setup'],
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

            $keywords = $this->getTextDataFromBladeFile($path);
            $keywords = ucwords(str_replace(['.', '_', '-'], ' ', $keywords));

            if (strlen($keywords) > 3) {
                $formattedRoutes[] = [
                    'routeName'   => $routeName,
                    'URI'         => $uri,
                    'keywords'    => $keywords,
                    'bladePath'   => $path,
                    'moduleType'  => $moduleType  !== "" ?  $moduleType : null,
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
