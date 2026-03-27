<?php

namespace App\Http\Controllers;

use App\Models\Admin;
 use App\Models\Module;
use App\Models\Vendor;
use App\Models\DataSetting;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\VendorEmployee;
use Illuminate\Support\Carbon;
use App\Models\BusinessSetting;
use App\CentralLogics\SMS_module;
use App\Models\PhoneVerification;
 use Illuminate\Support\Facades\DB;
 use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Mail\AdminPasswordResetMail;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Mail\PasswordResetRequestMail;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Modules\Gateways\Traits\SmsGateway;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
 
class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin,vendor', ['except' => 'logout']);
    }

    public function login($login_url)
    {
        $language = BusinessSetting::where('key', 'system_language')->first();
        if ($language) {
            foreach (json_decode($language->value, true) as $key => $data) {
                if ($data['default']) {
                    $lang = $data['code'];
                    $direction = $data['direction'];
                }
            }
        }
        $data = array_column(DataSetting::whereIn('key', ['store_employee_login_url', 'store_login_url', 'admin_employee_login_url', 'admin_login_url'
        ])->get(['key', 'value'])->toArray(), 'value', 'key');

        $loginTypes = [
            'admin' => 'admin_login_url',
            'admin_employee' => 'admin_employee_login_url',
            'vendor' => 'store_login_url',
            'vendor_employee' => 'store_employee_login_url'
        ];
        $siteDirections = [
            'admin' => session()?->get('site_direction') ?? $direction ?? 'ltr',
            'admin_employee' => session()?->get('site_direction') ?? $direction ?? 'ltr',
            'vendor' => session()?->get('vendor_site_direction') ?? $direction ?? 'ltr',
            'vendor_employee' => session()?->get('vendor_site_direction') ?? $direction ?? 'ltr'
        ];
        $locals = [
            'admin' => session()?->get('local') ?? $lang ?? 'en',
            'admin_employee' => session()?->get('local') ?? $lang ?? 'en',
            'vendor' => session()?->get('vendor_local') ?? $lang ?? 'en',
            'vendor_employee' => session()?->get('vendor_local') ?? $lang ?? 'en'
        ];
        $role = null;

        $user_type = array_search($login_url, $data);
        abort_if(!$user_type, 404);
        $role = array_search($user_type, $loginTypes, true);

        abort_if($role == null, 404);
        $site_direction = $siteDirections[$role];
        $locale = $locals[$role];
        App::setLocale($locale);
        $custome_recaptcha = new CaptchaBuilder;
        $custome_recaptcha->build();
        Session::put('six_captcha', $custome_recaptcha->getPhrase());

        $email = null;
        $password = null;
        if (Cookie::has('p_token') && Cookie::has('e_token') && Cookie::has('role') && Cookie::get('role') == $role) {
            $email = Crypt::decryptString(Cookie::get('e_token'));
            $password = Crypt::decryptString(Cookie::get('p_token'));
        }

        return view('auth.login', compact('custome_recaptcha', 'email', 'password', 'role', 'site_direction', 'locale'));
    }

    public function login_attemp($role, $email, $password, $ip, $remember = false)
    {
        $auth = ($role == 'admin_employee' ? 'admin' : $role);
        if (auth($auth)->attempt(['email' => $email, 'password' => $password], $remember)) {
            $user = auth($auth)->user();
            $newToken = $user?->login_remember_token ?? Str::random(60);
            $user->login_remember_token = $newToken;
            $user->save();
            session(['login_remember_token' => $newToken]);
            if ($remember) {
                Cookie::queue('role', $role, 120);
                Cookie::queue('e_token', Crypt::encryptString($email), 120);
                Cookie::queue('p_token', Crypt::encryptString($password), 120);
            }
            if ($auth == 'admin') {
                RateLimiter::clear('login-attempts:' . $ip);
                return 'admin';
            } else {
                RateLimiter::clear('login-attempts:' . $ip);
                return 'vendor';
            }
        }
        return false;
    }


    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1 && !$request?->set_default_captcha) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'];
                        $gResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                            'secret' => $secret_key,
                            'response' => $value,
                            'remoteip' => \request()->ip(),
                        ]);

                        if (!$gResponse->successful()) {
                            $fail(translate('ReCaptcha Failed'));
                        }
                    },
                ],
            ]);
        } else if (strtolower(session('six_captcha')) != strtolower($request->custome_recaptcha)) {
            Toastr::error(translate('messages.ReCAPTCHA Failed'));
            return back();
        }

        $ip = $request->ip();
        $key = 'login-attempts:' . $ip;
        $maxAttempts = 5;
        $decayMinutes = 2;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $time = $seconds > 60
                ? ceil($seconds / 60) . ' minutes'
                : $seconds . ' seconds';

            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['Too many login attempts. Try again in ' . $time . '.']);
        }


        if ($request->role == 'admin_employee') {
            $data = Admin::where('email', $request->email)->where('role_id', 1)->exists();
            if ($data) {
                RateLimiter::hit($key, $decayMinutes * 60);
                return redirect()->back()->withInput($request->only('email', 'remember'))
                    ->withErrors(['Email does not match.']);
            }
        }
        elseif ($request->role == 'admin') {
            $data = Admin::where('email', $request->email)->where('role_id', 1)->exists();
            if (!$data) {
                RateLimiter::hit($key, $decayMinutes * 60);
                return redirect()->back()->withInput($request->only('email', 'remember'))
                    ->withErrors(['Email does not match.']);
            }
        }
        elseif ($request->role == 'vendor') {
            $vendor = Vendor::where('email', $request->email)->first();
            if ($vendor) {
                if($vendor?->stores[0]?->module?->module_type == 'rental'){
                    if(!addon_published_status('Rental')){
                        return redirect()->back()->withInput($request->only('email', 'remember'))
                        ->withErrors([translate('messages.rental_module_is_not_available')]);
                    }
                }
                if ($vendor?->stores[0]?->store_business_model == 'none') {
                    $key = ['subscription_free_trial_days', 'subscription_free_trial_type', 'subscription_free_trial_status'];
                    $free_trial_settings = BusinessSetting::whereIn('key', $key)->pluck('value', 'key');

                    return view('vendor-views.auth.register-subscription-payment', [
                        'package_id' => $vendor?->stores[0]?->package_id,
                        'store_id' => $vendor?->stores[0]?->id,
                        'free_trial_settings' => $free_trial_settings,
                        'payment_methods' => Helpers::getActivePaymentGateways(),
                    ]);
                }

                if ($vendor?->stores[0]?->status == 0 && $vendor?->status == 0) {
                    return redirect()->back()->withInput($request->only('email', 'remember'))
                        ->withErrors([translate('messages.Admin_did_not_approve_your_registration_yet.')]);
                }
            }else{
                RateLimiter::hit($key, $decayMinutes * 60);
                return redirect()->back()->withInput($request->only('email', 'remember'))
                ->withErrors(['Email does not match.']);
            }
        } elseif ($request->role == 'vendor_employee') {
            $employee = VendorEmployee::where('email', $request->email)->first();
                if($employee?->store?->module?->module_type == 'rental'){
                    if(!addon_published_status('Rental')){
                        return redirect()->back()->withInput($request->only('email', 'remember'))
                        ->withErrors([translate('messages.rental_module_is_not_available')]);
                    }
                }
                if ($employee && (in_array($employee?->store?->store_business_model, ['none', 'unsubscribed']) || $employee?->store?->status == 0)) {
                    return redirect()->back()->withInput($request->only('email', 'remember'))
                        ->withErrors([translate('messages.store_is_inactive')]);
                }
                if (!$employee) {
                    RateLimiter::hit($key, $decayMinutes * 60);
                    return redirect()->back()->withInput($request->only('email', 'remember'))
                        ->withErrors(['Email does not match.']);
                }
        }

        $data = $this->login_attemp($request->role, $request->email, $request->password, $request->ip(), $request->remember);

        if($request->remember){
            $forgetCookies = [];
        }else{
            $forgetCookies = [
                Cookie::forget('role'),
                Cookie::forget('e_token'),
                Cookie::forget('p_token'),
            ];
        }


        if ($data == 'admin') {
            $admin = Admin::find(auth('admin')->id());
            $admin->is_logged_in = 1;
            $admin->save();
            $modules = Module::Active()->get();
            if (isset($modules) && ($modules->count() > 0)) {

                return redirect()->route('admin.dashboard')->withCookies($forgetCookies);
            }
            return redirect()->route('admin.business-settings.business-setup')->withCookies($forgetCookies);
        }
        if ($data == 'vendor') {
            if ($request->role === 'vendor_employee') {
                $employee = VendorEmployee::where('email', $request->email)->first();
                $employee->is_logged_in = 1;
                $employee->save();
            }
            if(Helpers::get_store_data()?->module_type == 'rental' && addon_published_status('Rental')){
                return redirect()->route('vendor.providerDashboard')->withCookies($forgetCookies);
            }
            return redirect()->route('vendor.dashboard')->withCookies($forgetCookies);
        }
        RateLimiter::hit($key, $decayMinutes * 60);
        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['Password does not match.']);
    }

    public function reloadCaptcha()
    {
        $custome_recaptcha = new CaptchaBuilder;
        $custome_recaptcha->build();
        Session::put('six_captcha', $custome_recaptcha->getPhrase());

        return response()->json([
            'view' => view('auth.custom-captcha', compact('custome_recaptcha'))->render()
        ], 200);
    }

    public function reset_password_request(Request $request)
    {
        $admin = Admin::where('role_id', 1)->first();

        if (isset($admin)) {
            $token = Helpers::generate_reset_password_code();
            DB::table('password_resets')->insert([
                'email' => $admin['email'],
                'token' => $token,
                'created_by' => 'admin',
                'created_at' => now(),
            ]);
            $url = url('/') . '/password-reset?token=' . $token;
            try {
                if (config('mail.status') && $admin['email'] && Helpers::get_mail_status('forget_password_mail_status_admin') == '1' && Helpers::getNotificationStatusData('admin', 'forget_password', 'mail_status')) {
                    Mail::to($admin?->getRawOriginal('email'))->send(new AdminPasswordResetMail($url, $admin['f_name']));
                    session()->put('log_email_succ', 1);
                } else {
                    Toastr::error(translate('messages.Failed_to_send_mail'));
                }
            } catch (\Throwable $th) {
                info($th->getMessage());
                Toastr::error(translate('messages.Failed_to_send_mail'));
            }
            return back();
        }
        Toastr::error(translate('messages.credential_doesnt_match'));
        return back();
    }

    public function vendor_reset_password_request(Request $request)
    {
        $request->validate([
            'email' => 'required'
        ]);
        $vendor = Vendor::where('email', $request['email'])->first();

        if (isset($vendor)) {
            $token = Helpers::generate_reset_password_code();
            DB::table('password_resets')->insert([
                'email' => $vendor['email'],
                'token' => $token,
                'created_by' => 'vendor',
                'created_at' => now(),
            ]);
            $url = url('/') . '/password-reset?token=' . $token;

            try {
                if (config('mail.status') && $vendor['email']) {
                    Mail::to($vendor?->getRawOriginal('email'))->send(new PasswordResetRequestMail($url, $vendor['f_name']));
                    session()->put('log_email_succ', 1);
                } else {
                    Toastr::error(translate('messages.Failed_to_send_mail'));
                }
            } catch (\Throwable $th) {
                info($th->getMessage());
                Toastr::error(translate('messages.Failed_to_send_mail'));
            }
            return back();
        }
        Toastr::error(translate('messages.Email_does_not_exists'));
        return back();
    }

    public function reset_password(Request $request)
    {
        $language = BusinessSetting::where('key', 'system_language')->first();
        if ($language) {
            foreach (json_decode($language->value, true) as $key => $data) {
                if ($data['default']) {
                    $lang = $data['code'];
                    $direction = $data['direction'];
                }
            }
        }
        $data = DB::table('password_resets')->where(['token' => $request['token']])->first();
        if (!$data || Carbon::parse($data->created_at)->diffInMinutes(Carbon::now()) >= 60) {
            Toastr::error(translate('messages.link_expired'));
            return redirect()->route('home');
        }
        $token = $request['token'];
        if ($data->created_by == 'admin') {
            $admin = Admin::where('email', $data->email)->where('role_id', 1)->first();
            $otp = rand(10000, 99999);
            DB::table('phone_verifications')->updateOrInsert(['phone' => $admin['phone']],
                [
                    'token' => $otp,
                    'otp_hit_count' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            //for payment and sms gateway addon

            $response = null;
            if (Helpers::getNotificationStatusData('admin', 'forget_password', 'sms_status')) {
                $published_status = addon_published_status('Gateways');
                if ($published_status == 1) {
                    $response = SmsGateway::send($admin['phone'], $otp);
                } else {
                    $response = SMS_module::send($admin['phone'], $otp);
                }
            }

            $site_direction = session()?->get('site_direction') ?? $direction ?? 'ltr';
            $locale = session()?->get('local') ?? $lang ?? 'en';
            App::setLocale($locale);
            if ($response == 'success') {
                return view('auth.verify-otp', compact('token', 'admin', 'site_direction', 'locale'));
            }
            return view('auth.reset-password', compact('token', 'admin', 'site_direction', 'locale'));
        } else {
            $site_direction = session()?->get('vendor_site_direction') ?? $direction ?? 'ltr';
            $locale = session()?->get('vendor_local') ?? $lang ?? 'en';
            App::setLocale($locale);
            return view('auth.reset-password', compact('token', 'site_direction', 'locale'));
        }


    }

    public function verify_token(Request $request)
    {
        $request->validate([
            'reset_token' => 'required',
            'opt-value' => 'required',
        ]);
        $language = BusinessSetting::where('key', 'system_language')->first();
        if ($language) {
            foreach (json_decode($language->value, true) as $key => $data) {
                if ($data['default']) {
                    $lang = $data['code'];
                    $direction = $data['direction'];
                }
            }
        }
        $token = $request['reset_token'];
        $admin = Admin::where('phone', $request['phone'])->where('role_id', 1)->first();

        $data = PhoneVerification::where([
            'phone' => $request['phone'],
            'token' => $request['opt-value'],
        ])->first();

        if (isset($data)) {
            $data?->delete();
            $site_direction = session()?->get('site_direction') ?? $direction ?? 'ltr';
            $locale = session()?->get('local') ?? $lang ?? 'en';
            App::setLocale($locale);
            return view('auth.reset-password', compact('token', 'admin', 'site_direction', 'locale'));
        }

        Toastr::error(translate('messages.otp_doesnt_match'));
        return back();
    }

    public function reset_password_submit(Request $request)
    {
        $request->validate([
            'reset_token' => 'required',
            'password' => ['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
            'confirm_password' => 'required|same:password',
        ]);
        $data = DB::table('password_resets')->where(['token' => $request['reset_token']])->first();
        if (isset($data)) {
            if ($request['password'] == $request['confirm_password']) {
                $newRememberToken = Str::random(60);
                if ($data->created_by == 'admin') {
                    DB::table('admins')->where(['email' => $data->email])->update([
                        'password' => bcrypt($request['confirm_password']),
                        'login_remember_token' => $newRememberToken,
                    ]);
                    $user_link = Helpers::get_login_url('admin_login_url');
                } else {
                    DB::table('vendors')->where(['email' => $data->email])->update([
                        'password' => bcrypt($request['confirm_password']),
                        'login_remember_token' => $newRememberToken,
                    ]);
                    $user_link = Helpers::get_login_url('store_login_url');
                }
                DB::table('password_resets')->where(['token' => $request['reset_token']])->delete();
                Toastr::success(translate('messages.password_changed_successfully'));
                return redirect()->route('login', [$user_link]);
            }
        }
        Toastr::error(translate('messages.something_went_wrong'));
        return back();

    }

    public function logout()
    {
        if (auth('vendor')?->check()) {
            $user_link = Helpers::get_login_url('store_login_url');
            auth()->guard('vendor')->logout();
            session()->forget('subscription_free_trial_close_btn');
            session()->forget('subscription_renew_close_btn');
            session()->forget('subscription_cancel_close_btn');

        } elseif (auth('vendor_employee')?->check()) {
            $user_link = Helpers::get_login_url('store_employee_login_url');
            auth()->guard('vendor_employee')->logout();
            session()->forget('subscription_free_trial_close_btn');
            session()->forget('subscription_renew_close_btn');
            session()->forget('subscription_cancel_close_btn');
        } else {
            if (auth()?->guard('admin')?->user()?->role_id == 1) {
                    $user_link = Helpers::get_login_url('admin_login_url');
                } else {
                $user_link = Helpers::get_login_url('admin_employee_login_url');
            }
            auth()?->guard('admin')?->logout();
        }
        return redirect()->route('login', [$user_link]);
    }

    public function otp_resent(Request $request)
    {
        $data = DB::table('password_resets')->where(['token' => $request['token']])->first();
        if (!$data || Carbon::parse($data->created_at)->diffInMinutes(Carbon::now()) >= 60) {
            return response()->json(['errors' => 'link_expired']);
        }

        if (Helpers::getNotificationStatusData('admin', 'forget_password', 'sms_status') != 1) {
            return response()->json(['otp_fail' => 'otp_fail']);
        }

        if ($data->created_by == 'admin') {
            $admin = Admin::where('email', $data->email)->where('role_id', 1)->first();
            $otp = rand(10000, 99999);
            DB::table('phone_verifications')->updateOrInsert(['phone' => $admin['phone']],
                [
                    'token' => $otp,
                    'otp_hit_count' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            //for payment and sms gateway addon


            $published_status = addon_published_status('Gateways');

            if ($published_status == 1) {
                $response = SmsGateway::send($admin['phone'], $otp);
            } else {
                $response = SMS_module::send($admin['phone'], $otp);
            }


            if ($response != 'success') {
                return response()->json(['otp_fail' => 'otp_fail']);
            }
            return response()->json(['success' => 'otp_send']);

        }
    }
}
