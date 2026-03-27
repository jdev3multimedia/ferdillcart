<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProviderRentalModuleCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (\App\CentralLogics\Helpers::get_store_data()?->module_type == 'rental'){
            return $next($request);
        }
        return abort(404);

    }
}
