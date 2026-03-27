<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\CentralLogics\Helpers;


class AdminMiddleware
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
        if (Auth::guard('admin')->user() && Auth::guard('admin')->user()->is_logged_in == 0) {
            auth()->guard('admin')->logout();
        }

        if (Auth::guard('admin')->user()) {
            if (session('login_remember_token') !== Auth::guard('admin')->user()?->login_remember_token) {
                if (auth()?->guard('admin')?->user()?->role_id == 1) {
                    $user_link = Helpers::get_login_url('admin_login_url');
                } else {
                    $user_link = Helpers::get_login_url('admin_employee_login_url');
                }
                Auth::guard('admin')->logout();
                session()->invalidate();
                session()->regenerateToken();
                return redirect()->route('login', [$user_link])
                    ->withErrors(['Your session has expired. Please log in again.']);
            }
        }

        if (Auth::guard('admin')->check()) {
            return $next($request);
        }
        return redirect()->route('home');
    }
}
