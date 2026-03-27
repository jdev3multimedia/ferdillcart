<?php

namespace App\Http\Middleware;

use App\Traits\ActivationClass;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


class ActivationCheckMiddleware
{
    use ActivationClass;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $area = null): mixed
    {
        $response = $this->checkActivationCache(app: $area);
        if (!$response) {
            if (!strpos(url()->current(), '/api/v1')) {
                return Redirect::away(route(base64_decode('c3lzdGVtLmFjdGl2YXRpb24tY2hlY2s=')))->send();
            }

            return response()->json([
                'code' => 503,
                'message' => 'Please check activation for '. str_replace('_', ' ', $area),
            ], 503);
        }
        return $next($request);
    }
}
