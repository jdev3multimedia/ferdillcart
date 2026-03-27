<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\Zone;
use App\Models\Admin;
use App\Models\Store;
use App\Models\Module;
use App\Models\Vendor;
use App\Models\Translation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\VendorEmployee;
use App\Mail\StoreRegistration;
use App\Models\BusinessSetting;
use App\CentralLogics\StoreLogic;
use App\Http\Controllers\Controller;
use App\Mail\VendorSelfRegistration;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\Rental\Emails\ProviderRegistration;
use Modules\Rental\Emails\ProviderSelfRegistration;

class VendorLoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $vendor_type= $request->vendor_type;

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if($vendor_type == 'owner'){
            if (auth('vendor')->attempt($data)) {
                $token = $this->genarate_token($request['email']);
                $vendor = Vendor::where(['email' => $request['email']])->first();

            $storeSubscriptionCheck=  $this->storeSubscriptionCheck($vendor?->stores[0],$vendor,$token);

                    if(data_get($storeSubscriptionCheck,'type') != null){
                        return response()->json(data_get($storeSubscriptionCheck,'data'), data_get($storeSubscriptionCheck,'code'));
                    }

                    if($vendor?->stores[0]?->module?->module_type == 'rental'){
                        if(!addon_published_status('Rental')){
                            $errors = [];
                            array_push($errors, ['code' => 'auth-001', 'message' => translate('rental_module_is_not_available')]);
                            return response()->json([
                                'errors' => $errors
                            ], 401);
                        }
                    }
                $vendor->auth_token = $token;
                $vendor->save();
                return response()->json(['token' => $token, 'zone_wise_topic'=> $vendor->stores[0]->zone->store_wise_topic, 'module_type' => $vendor?->stores[0]?->module?->module_type], 200);
            }  else {
                $errors = [];
                array_push($errors, ['code' => 'auth-001', 'message' => translate('Credential_do_not_match,_please_try_again')]);
                return response()->json([
                    'errors' => $errors
                ], 401);
            }
        }elseif($vendor_type == 'employee'){

            if (auth('vendor_employee')->attempt($data)) {
                $token = $this->genarate_token($request['email']);
                $vendor = VendorEmployee::where(['email' => $request['email']])->first();
                $storeSubscriptionCheck=  $this->storeSubscriptionCheck($vendor?->store,$vendor,$token);
                if(data_get($storeSubscriptionCheck,'type') != null){
                    return response()->json(data_get($storeSubscriptionCheck,'data'), data_get($storeSubscriptionCheck,'code'));
                }

                if($vendor?->store?->module_type == 'rental'){
                    if(!addon_published_status('Rental')){
                        $errors = [];
                        array_push($errors, ['code' => 'auth-001', 'message' => translate('rental_module_is_not_available')]);
                        return response()->json([
                            'errors' => $errors
                        ], 401);
                    }
                }

                $vendor->auth_token = $token;
                $vendor->save();
                $role = $vendor->role ? json_decode($vendor->role->modules):[];
                return response()->json(['token' => $token, 'zone_wise_topic'=> $vendor->store->zone->store_wise_topic, 'role'=>$role,
                    'module_type' => $vendor?->store?->module_type], 200);
            } else {
                $errors = [];
                array_push($errors, ['code' => 'auth-001', 'message' => translate('Credential_do_not_match,_please_try_again')]);
                return response()->json([
                    'errors' => $errors
                ], 401);
            }
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => translate('Credential_do_not_match,_please_try_again')]);
            return response()->json([
                'errors' => $errors
            ], 401);
        }

    }

    private function genarate_token($email)
    {
        $token = Str::random(120);
        $is_available = Vendor::where('auth_token', $token)->where('email', '!=', $email)->count();
        if($is_available)
        {
            $this->genarate_token($email);
        }
        return $token;
    }

    public function register(Request $request)
    {
        $status = BusinessSetting::where('key', 'toggle_store_registration')->first();
        if(!isset($status) || $status->value == '0')
        {
            return response()->json(['errors' => Helpers::error_processor('self-registration', translate('messages.store_self_registration_disabled'))]);
        }

        $validator = Validator::make($request->all(), [
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'latitude' => 'required',
            'longitude' => 'required',
            'email' => 'required|unique:vendors',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:vendors',
            'minimum_delivery_time' => 'required',
            'maximum_delivery_time' => 'required',
            'delivery_time_type'=>'required',
            'password' => ['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
            'zone_id' => 'required',
            'module_id' => 'required',
            'logo' => 'required|image|max:2048|mimes:'.IMAGE_FORMAT_FOR_VALIDATION,
            'cover_photo' => 'nullable|image|max:2048|mimes:'.IMAGE_FORMAT_FOR_VALIDATION,
        ],[
            'password.required' => translate('The password is required'),
            'password.min_length' => translate('The password must be at least :min characters long'),
            'password.mixed' => translate('The password must contain both uppercase and lowercase letters'),
            'password.letters' => translate('The password must contain letters'),
            'password.numbers' => translate('The password must contain numbers'),
            'password.symbols' => translate('The password must contain symbols'),
            'password.uncompromised' => translate('The password is compromised. Please choose a different one'),
        ]);

        if($request->zone_id)
        {
            $zone = Zone::query()
            ->whereContains('coordinates', new Point($request->latitude, $request->longitude, POINT_SRID))
            ->where('id',$request->zone_id)
            ->first();
            if(!$zone){
                $validator->getMessageBag()->add('latitude', translate('messages.coordinates_out_of_zone'));
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
        }
        $module = Module::find($request['module_id']);
        if ($module?->module_type == 'rental' && addon_published_status('Rental') && empty($request['pickup_zone_id'])){
            $validator->getMessageBag()->add('pickup_zone_id', translate('messages.You_must_select_a_pickup_zone'));
            return back()->withErrors($validator)
                ->withInput();
        }

        $data = json_decode($request->translations, true);

        if (count($data) < 1) {
            $validator->getMessageBag()->add('translations', translate('messages.Name and description in english is required'));
        }

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $vendor = new Vendor();
        $vendor->f_name = $request->f_name;
        $vendor->l_name = $request->l_name;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->password = bcrypt($request->password);
        $vendor->status = null;
        $vendor->save();

        $store = new Store;
        $store->name = $data[0]['value'];
        $store->phone = $request->phone;
        $store->email = $request->email;
        $store->logo = Helpers::upload('store/', 'png', $request->file('logo'));
        $store->cover_photo = Helpers::upload('store/cover/', 'png', $request->file('cover_photo'));
        $store->address = $data[1]['value'];
        $store->latitude = $request->latitude;
        $store->longitude = $request->longitude;
        $store->vendor_id = $vendor->id;
        $store->zone_id = $request->zone_id;
        $store->tin = $request->tin;
        $store->tin_expire_date = $request->tin_expire_date == 'null' ? null : $request->tin_expire_date;
        $extension = $request->file('tin_certificate_image') ? $request->file('tin_certificate_image')->getClientOriginalExtension() : 'png';
        $store->tin_certificate_image = Helpers::upload('store/', $extension, $request->file('tin_certificate_image'));
        $store->delivery_time = $request->minimum_delivery_time .'-'. $request->maximum_delivery_time.' '.$request->delivery_time_type;
        $store->module_id = $request->module_id;
        $store->status = 0;
        $store->store_business_model = 'none';
        $store->pickup_zone_id = $request['pickup_zone_id'] ?? json_encode([]);
        $store->save();
        // $store->module->increment('stores_count');
        if(config('module.'.$store->module->module_type)['always_open'])
        {
            StoreLogic::insert_schedule($store->id);
        }

        foreach ($data as $key=>$i) {
            $data[$key]['translationable_type'] = 'App\Models\Store';
            $data[$key]['translationable_id'] = $store->id;
        }
        Translation::insert($data);


        try{
            $admin= Admin::where('role_id', 1)->first();
            $mail_status = Helpers::get_mail_status('registration_mail_status_store');
            if($module?->module_type != 'rental' && config('mail.status') && $mail_status == '1' &&  Helpers::getNotificationStatusData('store','store_registration','mail_status')){
                Mail::to($request['email'])->send(new VendorSelfRegistration('pending', $vendor->f_name.' '.$vendor->l_name));
            }
            elseif($module?->module_type == 'rental' && addon_published_status('Rental')&& config('mail.status') && Helpers::get_mail_status('rental_registration_mail_status_provider') == '1' &&  Helpers::getRentalNotificationStatusData('provider','provider_registration','mail_status') ){
                Mail::to($request['email'])->send(new ProviderSelfRegistration('pending', $vendor->f_name.' '.$vendor->l_name));
            }

            $mail_status = Helpers::get_mail_status('store_registration_mail_status_admin');
            if($module?->module_type != 'rental' && config('mail.status') && $mail_status == '1' &&  Helpers::getNotificationStatusData('admin','store_self_registration','mail_status')){
                Mail::to($admin?->getRawOriginal('email'))->send(new StoreRegistration('pending', $vendor->f_name.' '.$vendor->l_name));
            }
            elseif($module?->module_type == 'rental' && addon_published_status('Rental') && config('mail.status') && Helpers::get_mail_status('rental_provider_registration_mail_status_admin') == '1' &&  Helpers::getRentalNotificationStatusData('admin','provider_self_registration','mail_status') ){
                Mail::to($admin?->getRawOriginal('email'))->send(new ProviderRegistration('pending', $vendor->f_name.' '.$vendor->l_name));
            }
        }catch(\Exception $ex){
            info($ex->getMessage());
        }


        if (Helpers::subscription_check()) {
                if ($request->business_plan == 'subscription' && $request->package_id != null ) {
                    $store->package_id = $request->package_id;
                    $store->save();

                    return response()->json([
                        'store_id'=> $store->id,
                        'package_id'=> $store->package_id,
                        'type'=> 'subscription',
                        'message'=>translate('messages.application_placed_successfully')],200);
                }
                elseif($request->business_plan == 'commission' ){
                    $store->store_business_model = 'commission';
                    $store->save();
                    return response()->json([
                        'store_id'=> $store->id,
                        'type'=> 'commission',
                        'message'=>translate('messages.application_placed_successfully')],200);
                }
                else{
                    return response()->json([
                        'store_id'=> $store->id,
                        'type'=> 'business_model_fail',
                        'message'=>translate('messages.application_placed_successfully')],200);
                }
            } else{
                $store->store_business_model = 'commission';
                $store->save();
                return response()->json([
                    'store_id'=> $store->id,
                    'type'=> 'commission',
                    'message'=>translate('messages.application_placed_successfully')],200);
            }

        return response()->json([
            'store_id'=> $store->id,
            'message'=>translate('messages.application_placed_successfully')],200);
    }





    private function storeSubscriptionCheck($store, $vendor,$token){
        if ($store?->store_business_model == 'none') {
            $vendor->auth_token = $token;
            $vendor?->save();
            return [
                'type' => 'subscribed',
                'code' => 200,
                'data' => [
                    'subscribed' => [
                        'store_id' => $store?->id,
                        'token' => $token,
                        'package_id' => $store?->package_id,
                        'zone_wise_topic' => $store?->zone?->store_wise_topic,
                        'type' => 'new_join',
                        'module_type' => $store?->module?->module_type
                    ]
                ]
            ];
        }

        if ($store->status == 0 && $vendor->status == 0) {
            return [
                'type' => 'errors',
                'code' => 403,
                'data' => [
                    'errors' => [
                        ['code' => 'auth-002', 'message' => translate('messages.Your_registration_is_not_approved_yet._You_can_login_once_admin_approved_the_request')]
                    ]
                ]
            ];
        } elseif ($store->status == 0 && $vendor->status == 1 && in_array($store?->store_business_model ,['subscription' ,'commission']) ) {
            return [
                'type' => 'errors',
                'code' => 403,
                'data' => [
                    'errors' => [
                        ['code' => 'auth-002', 'message' => translate('messages.Your_account_is_suspended')]
                    ]
                ]
            ];
        }

        if ($store?->store_business_model == 'subscription') {
            $store_sub = $store?->store_sub;
            if (isset($store_sub)) {
                if ($store_sub?->mobile_app == 0) {
                    return [
                        'type' => 'errors',
                        'code' => 401,
                        'data' => [
                            'errors' => [
                                ['code' => 'no_mobile_app', 'message' => translate('messages.Your Subscription Plan is not Active for Mobile App')]
                            ]
                        ]
                    ];
                }
            }
        }

        if ($store?->store_business_model == 'unsubscribed' && isset($store?->store_sub_update_application)) {
            return null;
        }

        if ($store?->store_business_model == 'unsubscribed' && !isset($store?->store_sub_update_application)) {
            $vendor->auth_token = $token;
            $vendor?->save();
            return [
                'type' => 'subscribed',
                'code' => 200,
                'data' => [
                    'subscribed' => [
                        'store_id' => $store?->id,
                        'token' => $token,
                        'zone_wise_topic' => $store?->zone?->store_wise_topic,
                        'type' => 'new_join',
                        'module_type' => $store?->module?->module_type
                    ]
                ]
            ];
        }
        return null ;
    }

}
