<?php

namespace App\Http\Middleware;

use App\CentralLogics\Helpers;
use Closure;
use Illuminate\Support\Facades\App;


class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $lang = 'en';
        $direction = 'ltr';
        try {
            $language = Helpers::get_business_settings('system_language');
            if ($language) {
                foreach ($language as $key => $data) {
                    if ($data['default']) {
                        $lang = $data['code'];
                        $direction = $data['direction'];
                    }
                }
            }
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }
        if ($request->is('vendor-panel*')) {
            if (session()->has('vendor_local')) {
                App::setLocale(session()->get('vendor_local'));
            } else {
                App::setLocale($lang);
            }

            if ($language) {
                foreach ($language as $key => $data) {
                    if ($data['code'] == App::getLocale()) {
                        $direction = $data['direction'] ?? 'ltr';
                    }
                }
            }
            session()->put('vendor_site_direction', $direction);

        } elseif ($request->is('admin*') || $request->is('taxvat*')) {
            if (session()->has('local')) {
                App::setLocale(session()->get('local'));
            } else {
                App::setLocale($lang);
            }

            if ($language) {
                foreach ($language as $key => $data) {
                    if ($data['code'] == App::getLocale()) {
                        $direction = $data['direction'] ?? 'ltr';
                    }
                }
            }
            session()->put('site_direction', $direction);

        } else {
            if (session()->has('landing_local')) {
                App::setLocale(session()->get('landing_local'));
            } else {
                App::setLocale($lang);
            }

            if ($language) {
                foreach ($language as $key => $data) {
                    if ($data['code'] == App::getLocale()) {
                        $direction = $data['direction'] ?? 'ltr';
                    }
                }
            }
            session()->put('landing_site_direction', $direction);
        }
        return $next($request);
    }
}
