<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\CentralLogics\Helpers;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('vendor')->check()) {
            if (!auth('vendor')->user()->status) {
                auth()->guard('vendor')->logout();
                return redirect()->route('home');
            }

            if (session('login_remember_token') !== auth('vendor')->user()?->login_remember_token) {
                auth()->guard('vendor')->logout();
                session()->invalidate();
                session()->regenerateToken();
                $user_link = Helpers::get_login_url('store_login_url');
                return redirect()->route('login', [$user_link])
                    ->withErrors(['Your session has expired. Please log in again.']);
            }

            return $next($request);
        } else if (Auth::guard('vendor_employee')->check()) {
            if (Auth::guard('vendor_employee')->user()->is_logged_in == 0) {
                auth()->guard('vendor_employee')->logout();
                return redirect()->route('home');
            }
            if (!auth('vendor_employee')->user()->store->status) {
                auth()->guard('vendor_employee')->logout();
                return redirect()->route('home');
            }

            if (session('login_remember_token') !== Auth::guard('vendor_employee')->user()?->login_remember_token) {
                auth()->guard('vendor_employee')->logout();
                session()->invalidate();
                session()->regenerateToken();
                $user_link = Helpers::get_login_url('store_employee_login_url');
                return redirect()->route('login', [$user_link])
                    ->withErrors(['Your session has expired. Please log in again.']);
            }
            return $next($request);
        }
        return redirect()->route('home');
    }
}
