<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminRentalModuleCheckMiddleware
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed|never
     */
    public function handle(Request $request, Closure $next)
    {
        if (addon_published_status('Rental') && (config('module.current_module_type') == 'rental' ) ||   $request->is('admin/rental/trip/details/*') ||  $request->is('admin/rental/provider/details/*') ||  $request->is('admin/rental/provider/vehicle/details/*')  ){
            return $next($request);
        }

        return abort(404);
    }
}
