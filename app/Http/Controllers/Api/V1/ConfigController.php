<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\AnalyticScript;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Currency;
use App\Models\DataSetting;
use App\Models\DMVehicle;
use App\Models\FAQ;
use App\Models\FlutterSpecialCriteria;
use App\Models\Module;
use App\Models\OfflinePaymentMethod;
use App\Models\PageSeoData;
use App\Models\ParcelCancellationReason;
use App\Models\ReactPromotionalBanner;
use App\Models\ReactTestimonial;
use App\Models\Setting;
use App\Models\SocialMedia;
use App\Models\Zone;
use App\Traits\AddonHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\Rental\Entities\Vehicle;

class ConfigController extends Controller
{
    private $map_api_key;

    use AddonHelper;

    public function __construct()
    {
        $map_api_key_server = BusinessSetting::where(['key' => 'map_api_key_server'])->first();
        $map_api_key_server = $map_api_key_server ? $map_api_key_server->value : null;
        $this->map_api_key = $map_api_key_server;
    }

    public function configuration()
    {
        $key = [
            'currency_code',
            'cash_on_delivery',
            'digital_payment',
            'default_location',
            'business_name',
            'logo',
            'address',
            'phone',
            'email_address',
            'country',
            'currency_symbol_position',
            'app_minimum_version_android',
            'app_url_android',
            'app_minimum_version_ios',
            'app_url_ios',
            'app_url_android_store',
            'app_minimum_version_ios_store',
            'app_url_ios_store',
            'app_minimum_version_ios_deliveryman',
            'app_url_ios_deliveryman',
            'app_minimum_version_android_deliveryman',
            'app_minimum_version_android_store',
            'app_url_android_deliveryman',
            'schedule_order',
            'order_delivery_verification',
            'show_dm_earning',
            'canceled_by_deliveryman',
            'canceled_by_store',
            'timeformat',
            'toggle_veg_non_veg',
            'toggle_dm_registration',
            'toggle_store_registration',
            'schedule_order_slot_duration',
            'parcel_per_km_shipping_charge',
            'parcel_minimum_shipping_charge',
            'footer_text',

            'loyalty_point_exchange_rate',
            'loyalty_point_item_purchase_point',
            'loyalty_point_status',
            'loyalty_point_minimum_point',
            'wallet_status',
            'dm_tips_status',
            'ref_earning_status',
            'ref_earning_exchange_rate',
            'refund_active_status',
            'refund',
            'cancelation',
            'shipping_policy',
            'prescription_order_status',
            'icon',
            'cookies_text',
            'home_delivery_status',
            'takeaway_status',
            'additional_charge',
            'additional_charge_status',
            'additional_charge_name',
            'dm_picture_upload_status',
            'partial_payment_status',
            'partial_payment_method',
            'add_fund_status',
            'offline_payment_status',
            'websocket_url',
            'websocket_port',
            'websocket_status',
            'guest_checkout_status',
            'disbursement_type',
            'restaurant_disbursement_waiting_time',
            'dm_disbursement_waiting_time',
            'min_amount_to_pay_store',
            'min_amount_to_pay_dm',
            'admin_commission',
            'new_customer_discount_status',
            'new_customer_discount_amount',
            'new_customer_discount_amount_type',
            'new_customer_discount_amount_validity',
            'new_customer_discount_validity_type',
            'store_review_reply',
            'subscription_business_model',
            'commission_business_model',
            'subscription_deadline_warning_days',
            'subscription_deadline_warning_message',
            'subscription_free_trial_days',
            'subscription_free_trial_type',
            'subscription_free_trial_status',
            'country_picker_status',
            'firebase_otp_verification',
            'manual_login_status',
            'otp_login_status',
            'social_login_status',
            'google_login_status',
            'facebook_login_status',
            'apple_login_status',
            'email_verification_status',
            'phone_verification_status',
            'admin_free_delivery_option',
            'admin_free_delivery_status',
            'free_delivery_over',

            'parcel_cancellation_status',
            'parcel_cancellation_basic_setup',
            'parcel_return_time_fee',
            'openai_config',

            'dm_loyality_point_status',
            'dm_loyality_point_per_order',
            'dm_loyality_point_conversion_rate',
            'dm_min_loyality_point_to_convert',
            'dm_referal_status',
            'dm_referal_amount',
            'dm_referal_bonus',

        ];

        $vehicle_distance_min = 0;
        $vehicle_hourly_min = 0;
        $vehicle_day_wise_min = 0;

        $drivemondExternalSetting = false;

        $cacheKey = 'business_settings_config_keys';
        $settings = Cache::rememberForever($cacheKey, function () use ($key) {
            return array_column(BusinessSetting::whereIn('key', $key)->get()->toArray(), 'value', 'key');
        });
        $image_key = ['logo', 'icon'];
        $data = [];
        $openAIStatus = isset($settings['openai_config']) ? json_decode($settings['openai_config'], true) : [];
        $openAIStatus = isset($openAIStatus['status']) && $openAIStatus['status'] == 1 ? 1 : 0;
        foreach ($image_key as $value) {
            $data[$value . '_storage'] = Cache::rememberForever("business_settings_config_{$value}_storage", function () use ($value) {
                return BusinessSetting::where('key', $value)->first()?->storage[0]?->value ?? 'public';
            });
        }

        $DataSetting = Cache::rememberForever('data_settings_flutter_landing_page', function () {
            return DataSetting::where('type', 'flutter_landing_page')
                ->where('key', 'download_user_app_links')
                ->pluck('value', 'key')
                ->toArray();
        });
        $DataSetting = isset($DataSetting['download_user_app_links']) ? json_decode($DataSetting['download_user_app_links'], true) : [];


        $currency_symbol = Cache::rememberForever('business_settings_currency_symbol', function () {
            return Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        });
        $cod = json_decode($settings['cash_on_delivery'], true);
        $digital_payment = json_decode($settings['digital_payment'], true);
        $default_location = isset($settings['default_location']) ? json_decode($settings['default_location'], true) : 0;

        $admin_free_delivery = [
            'status' => (bool)data_get($settings, 'admin_free_delivery_status', 0),
            'type' => data_get($settings, 'admin_free_delivery_option'),
            'free_delivery_over' => (float)data_get($settings, 'free_delivery_over', 0),
        ];

        $additional_charge = isset($settings['additional_charge']) ? (float)$settings['additional_charge'] : 0;
        $module = Cache::rememberForever('module_config', function () {
            return Module::active()->count() == 1 ? Module::active()->first() : null;
        });
        $languages = Helpers::get_business_settings('language');
        $lang_array = [];
        foreach ($languages as $language) {
            array_push($lang_array, [
                'key' => $language,
                'value' => Helpers::get_language_name($language),
            ]);
        }
        $system_languages = Helpers::get_business_settings('system_language');
        $sys_lang_array = [];
        foreach ($system_languages as $language) {
            array_push($sys_lang_array, [
                'key' => $language['code'],
                'value' => Helpers::get_language_name($language['code']),
                'direction' => $language['direction'],
                'default' => $language['default'],
            ]);
        }
        $social_login = [];
        foreach (Helpers::get_business_settings('social_login') as $social) {
            $config = [
                'login_medium' => $social['login_medium'],
                'status' => (bool)$social['status'],
            ];
            array_push($social_login, $config);
        }
        $apple_login = [];
        $apples = Helpers::get_business_settings('apple_login');
        if (isset($apples)) {
            foreach (Helpers::get_business_settings('apple_login') as $apple) {
                $config = [
                    'login_medium' => $apple['login_medium'],
                    'status' => (bool)$apple['status'],
                    'client_id' => $apple['client_id'],
                    'client_id_app' => $apple['client_id_app'] ?? '',
                    'redirect_url_flutter' => $apple['redirect_url_flutter'] ?? '',
                    'redirect_url_react' => $apple['redirect_url_react'] ?? '',
                ];
                array_push($apple_login, $config);
            }
        }

        // addon settings publish status
        $published_status = 0; // Set a default value
        $payment_published_status = config('get_payment_publish_status');
        if (isset($payment_published_status[0]['is_published'])) {
            $published_status = $payment_published_status[0]['is_published'];
        }

        $active_addon_payment_lists = Helpers::getActivePaymentGateways();

        $digital_payment_infos = [
            'digital_payment' => (bool)($digital_payment['status'] == 1 ? true : false),
            'plugin_payment_gateways' => (bool)($published_status ? true : false),
            'default_payment_gateways' => (bool)($published_status ? false : true),
        ];
            $dm_loyality_point_data = [
            'dm_loyality_point_status' => (bool) (data_get($settings, 'dm_loyality_point_status') == 1 ? true : false),
            'dm_loyality_point_per_order' => (float) data_get($settings, 'dm_loyality_point_per_order') ?? 0,
            'dm_loyality_point_conversion_rate' => (float) data_get($settings, 'dm_loyality_point_conversion_rate') ?? 0,
            'dm_min_loyality_point_to_convert' => (float) data_get($settings, 'dm_min_loyality_point_to_convert') ?? 0,
        ];

        $dm_referral_data = [
            'dm_referal_status' => (bool) (data_get($settings,'dm_referal_status') == 1 ? true : false),
            'dm_referal_amount' => (float) data_get($settings, 'dm_referal_amount') ?? 0,
            'dm_referal_bonus' => (float) data_get($settings, 'dm_referal_bonus') ?? 0,
        ];

        if (data_get($settings, 'subscription_free_trial_type') == 'year') {
            $trial_period = data_get($settings, 'subscription_free_trial_days') > 0 ? data_get($settings, 'subscription_free_trial_days') / 365 : 0;
        } elseif (data_get($settings, 'subscription_free_trial_type') == 'month') {
            $trial_period = data_get($settings, 'subscription_free_trial_days') > 0 ? data_get($settings, 'subscription_free_trial_days') / 30 : 0;
        } else {
            $trial_period = data_get($settings, 'subscription_free_trial_days') > 0 ? data_get($settings, 'subscription_free_trial_days') : 0;
        }

        if (addon_published_status('Rental')) {
            $cache_dis_key_min = 'vehicle_dis_min_price_conf';
            $vehicle_distance_min = Cache::rememberForever($cache_dis_key_min, function () {
                return Vehicle::where('distance_price', '>', '0')->min('distance_price');
            });
            $cache_hour_key_min = 'vehicle_hour_min_price_conf';
            $vehicle_hourly_min = Cache::rememberForever($cache_hour_key_min, function () {
                return Vehicle::where('hourly_price', '>', '0')->min('hourly_price');
            });
            $cache_day_wise_key_min = 'vehicle_day_wise_min_price_conf';
            if (Schema::hasColumn('vehicles', 'day_wise_price')) {
                $vehicle_day_wise_min = Cache::rememberForever($cache_day_wise_key_min, function () {
                    return Vehicle::where('day_wise_price', '>', '0')->min('day_wise_price');
                });
            }
        }
        if (addon_published_status('TaxModule')) {
            $systemTax = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
        }

        return response()->json([
            'business_name' => $settings['business_name'],
            'logo' => $settings['logo'],
            'logo_full_url' => Helpers::get_full_url('business', $settings['logo'], $data['logo_storage'] ?? 'public'),
            'address' => $settings['address'],
            'phone' => $settings['phone'],
            'email' => $settings['email_address'],

            'country' => $settings['country'],
            'default_location' => ['lat' => $default_location ? $default_location['lat'] : '23.757989', 'lng' => $default_location ? $default_location['lng'] : '90.360587'],
            'currency_symbol' => $currency_symbol,
            'currency_symbol_direction' => $settings['currency_symbol_position'],
            'app_minimum_version_android' => (float)$settings['app_minimum_version_android'],
            'app_url_android' => $settings['app_url_android'],
            'app_url_ios' => $settings['app_url_ios'],
            'app_minimum_version_ios' => (float)$settings['app_minimum_version_ios'],
            'app_minimum_version_android_store' => (float)(isset($settings['app_minimum_version_android_store']) ? $settings['app_minimum_version_android_store'] : 0),
            'app_url_android_store' => (isset($settings['app_url_android_store']) ? $settings['app_url_android_store'] : null),
            'app_minimum_version_ios_store' => (float)(isset($settings['app_minimum_version_ios_store']) ? $settings['app_minimum_version_ios_store'] : 0),
            'app_url_ios_store' => (isset($settings['app_url_ios_store']) ? $settings['app_url_ios_store'] : null),
            'app_minimum_version_android_deliveryman' => (float)(isset($settings['app_minimum_version_android_deliveryman']) ? $settings['app_minimum_version_android_deliveryman'] : 0),
            'app_url_android_deliveryman' => (isset($settings['app_url_android_deliveryman']) ? $settings['app_url_android_deliveryman'] : null),
            'app_minimum_version_ios_deliveryman' => (float)(isset($settings['app_minimum_version_ios_deliveryman']) ? $settings['app_minimum_version_ios_deliveryman'] : 0),
            'app_url_ios_deliveryman' => (isset($settings['app_url_ios_deliveryman']) ? $settings['app_url_ios_deliveryman'] : null),
            'prescription_order_status' => isset($settings['prescription_order_status']) ? (bool)$settings['prescription_order_status'] : false,
            'schedule_order' => (bool)$settings['schedule_order'],
            'order_delivery_verification' => (bool)$settings['order_delivery_verification'],
            'cash_on_delivery' => (bool)($cod['status'] == 1 ? true : false),
            'digital_payment' => (bool)($digital_payment['status'] == 1 ? true : false),
            'digital_payment_info' => $digital_payment_infos,
            'demo' => (bool)(env('APP_MODE') == 'demo' ? true : false),
            'maintenance_mode' => (bool)Helpers::get_business_settings('maintenance_mode') ?? 0,
            'order_confirmation_model' => config('order_confirmation_model'),
            'show_dm_earning' => (bool)$settings['show_dm_earning'],
            'canceled_by_deliveryman' => (bool)$settings['canceled_by_deliveryman'],
            'canceled_by_store' => (bool)$settings['canceled_by_store'],
            'timeformat' => (string)$settings['timeformat'],
            'language' => $lang_array,
            'sys_language' => $sys_lang_array,
            'social_login' => $social_login,
            'apple_login' => $apple_login,
            'toggle_veg_non_veg' => (bool)$settings['toggle_veg_non_veg'],
            'toggle_dm_registration' => (bool)$settings['toggle_dm_registration'],
            'toggle_store_registration' => (bool)$settings['toggle_store_registration'],
            'refund_active_status' => (bool)$settings['refund_active_status'],
            'schedule_order_slot_duration' => (int)$settings['schedule_order_slot_duration'],
            'digit_after_decimal_point' => (int)config('round_up_to_digit'),
            'module_config' => config('module'),
            'module' => $module,
            'parcel_per_km_shipping_charge' => (float)$settings['parcel_per_km_shipping_charge'],
            'parcel_minimum_shipping_charge' => (float)$settings['parcel_minimum_shipping_charge'],
            'social_media' => SocialMedia::active()->get()->toArray(),
            'footer_text' => isset($settings['footer_text']) ? $settings['footer_text'] : '',
            'cookies_text' => isset($settings['cookies_text']) ? $settings['cookies_text'] : '',
            'fav_icon' => $settings['icon'],
            'fav_icon_full_url' => Helpers::get_full_url('business', $settings['icon'], $data['icon_storage'] ?? 'public'),
            // Added Business Setting
            'dm_tips_status' => (int)(isset($settings['dm_tips_status']) ? $settings['dm_tips_status'] : 0),
            'loyalty_point_exchange_rate' => (int)(isset($settings['loyalty_point_item_purchase_point']) ? $settings['loyalty_point_exchange_rate'] : 0),
            'loyalty_point_item_purchase_point' => (float)(isset($settings['loyalty_point_item_purchase_point']) ? $settings['loyalty_point_item_purchase_point'] : 0.0),
            'loyalty_point_status' => (int)(isset($settings['loyalty_point_status']) ? $settings['loyalty_point_status'] : 0),
            'customer_wallet_status' => (int)(isset($settings['wallet_status']) ? $settings['wallet_status'] : 0),
            'ref_earning_status' => (int)(isset($settings['ref_earning_status']) ? $settings['ref_earning_status'] : 0),
            'ref_earning_exchange_rate' => (float)(isset($settings['ref_earning_exchange_rate']) ? $settings['ref_earning_exchange_rate'] : 0),
            'refund_policy' => (int)(self::get_settings_status('refund_policy_status')),
            'cancelation_policy' => (int)(self::get_settings_status('cancellation_policy_status')),
            'shipping_policy' => (int)(self::get_settings_status('shipping_policy_status')),
            'loyalty_point_minimum_point' => (int)(isset($settings['loyalty_point_minimum_point']) ? $settings['loyalty_point_minimum_point'] : 0),

            'home_delivery_status' => (int)(isset($settings['home_delivery_status']) ? $settings['home_delivery_status'] : 0),
            'takeaway_status' => (int)(isset($settings['takeaway_status']) ? $settings['takeaway_status'] : 0),
            'active_payment_method_list' => $active_addon_payment_lists,
            'additional_charge_status' => (int)(isset($settings['additional_charge_status']) ? $settings['additional_charge_status'] : 0),
            'additional_charge_name' => (isset($settings['additional_charge_name']) ? $settings['additional_charge_name'] : 'Service Charge'),
            'additional_charge' => $additional_charge,
            'partial_payment_status' => (int)(isset($settings['partial_payment_status']) ? $settings['partial_payment_status'] : 0),
            'partial_payment_method' => (isset($settings['partial_payment_method']) ? $settings['partial_payment_method'] : ''),
            'dm_picture_upload_status' => (int)(isset($settings['dm_picture_upload_status']) ? $settings['dm_picture_upload_status'] : 0),
            'add_fund_status' => (int)(isset($settings['add_fund_status']) ? $settings['add_fund_status'] : 0),
            'offline_payment_status' => (int)(isset($settings['offline_payment_status']) ? $settings['offline_payment_status'] : 0),
            'websocket_status' => (int)(isset($settings['websocket_status']) ? $settings['websocket_status'] : 0),
            'websocket_url' => (isset($settings['websocket_url']) ? $settings['websocket_url'] : ''),
            'websocket_port' => (int)(isset($settings['websocket_port']) ? $settings['websocket_port'] : 6001),
            'websocket_key' => env('PUSHER_APP_KEY'),
            'guest_checkout_status' => (int)(isset($settings['guest_checkout_status']) ? $settings['guest_checkout_status'] : 0),
            'disbursement_type' => (string)(isset($settings['disbursement_type']) ? $settings['disbursement_type'] : 'manual'),
            'restaurant_disbursement_waiting_time' => (int)(isset($settings['restaurant_disbursement_waiting_time']) ? $settings['restaurant_disbursement_waiting_time'] : 0),
            'dm_disbursement_waiting_time' => (int)(isset($settings['dm_disbursement_waiting_time']) ? $settings['dm_disbursement_waiting_time'] : 0),
            'min_amount_to_pay_store' => (float)(isset($settings['min_amount_to_pay_store']) ? $settings['min_amount_to_pay_store'] : 0),
            'min_amount_to_pay_dm' => (float)(isset($settings['min_amount_to_pay_dm']) ? $settings['min_amount_to_pay_dm'] : 0),
            'new_customer_discount_status' => (int)(isset($settings['new_customer_discount_status']) ? $settings['new_customer_discount_status'] : 0),
            'new_customer_discount_amount' => (float)(isset($settings['new_customer_discount_amount']) ? $settings['new_customer_discount_amount'] : 0),
            'new_customer_discount_amount_type' => (isset($settings['new_customer_discount_amount_type']) ? $settings['new_customer_discount_amount_type'] : 'amount'),
            'new_customer_discount_amount_validity' => (int)(isset($settings['new_customer_discount_amount_validity']) ? $settings['new_customer_discount_amount_validity'] : 0),
            'new_customer_discount_validity_type' => (isset($settings['new_customer_discount_validity_type']) ? $settings['new_customer_discount_validity_type'] : 'day'),
            'store_review_reply' => (int)(isset($settings['store_review_reply']) ? $settings['store_review_reply'] : 0),
            'admin_commission' => (float)(isset($settings['admin_commission']) ? $settings['admin_commission'] : 0),
            'subscription_business_model' => (int)(isset($settings['subscription_business_model']) ? $settings['subscription_business_model'] : 1),
            'commission_business_model' => (int)(isset($settings['commission_business_model']) ? $settings['commission_business_model'] : 1),
            'subscription_deadline_warning_days' => (int)(isset($settings['subscription_deadline_warning_days']) ? $settings['subscription_deadline_warning_days'] : 1),
            'subscription_deadline_warning_message' => isset($settings['subscription_deadline_warning_message']) ? $settings['subscription_deadline_warning_message'] : null,
            'subscription_free_trial_days' => (int)$trial_period,
            'subscription_free_trial_type' => (isset($settings['subscription_free_trial_type']) ? $settings['subscription_free_trial_type'] : 'day'),
            'subscription_free_trial_status' => (int)(isset($settings['subscription_free_trial_status']) ? $settings['subscription_free_trial_status'] : 0),
            'country_picker_status' => (int)(isset($settings['country_picker_status']) ? $settings['country_picker_status'] : 1),
            // 'external_system' => $drivemondExternalSetting,
            // 'drivemond_app_url_android' => $drivemondExternalSetting ? Helpers::get_external_data('drivemond_app_url_android') : '',
            // 'drivemond_app_url_ios' => $drivemondExternalSetting ? Helpers::get_external_data('drivemond_app_url_ios') : '',
            'firebase_otp_verification' => (int)(isset($settings['firebase_otp_verification']) ? $settings['firebase_otp_verification'] : 0),
            'centralize_login' => [
                'manual_login_status' => (int)(isset($settings['manual_login_status']) ? $settings['manual_login_status'] : 0),
                'otp_login_status' => (int)(isset($settings['otp_login_status']) ? $settings['otp_login_status'] : 0),
                'social_login_status' => (int)(isset($settings['social_login_status']) ? $settings['social_login_status'] : 0),
                'google_login_status' => (int)(isset($settings['google_login_status']) ? $settings['google_login_status'] : 0),
                'facebook_login_status' => (int)(isset($settings['facebook_login_status']) ? $settings['facebook_login_status'] : 0),
                'apple_login_status' => (int)(isset($settings['apple_login_status']) ? $settings['apple_login_status'] : 0),
                'email_verification_status' => (int)(isset($settings['email_verification_status']) ? $settings['email_verification_status'] : 0),
                'phone_verification_status' => (int)(isset($settings['phone_verification_status']) ? $settings['phone_verification_status'] : 0),
            ],

            'vehicle_distance_min' => (float)$vehicle_distance_min ?? 0,
            'vehicle_hourly_min' => (float)$vehicle_hourly_min ?? 0,
            'vehicle_day_wise_min' => (float)$vehicle_day_wise_min ?? 0,
            'admin_free_delivery' => $admin_free_delivery,
            'is_sms_active' => (bool)Setting::whereJsonContains('live_values->status', '1')->where('settings_type', 'sms_config')->exists(),
            'is_mail_active' => (bool)config('mail.status'),
            'system_tax_type' => $systemTax?->tax_type ?? null,
            'system_tax_include_status' => (int)$systemTax?->is_included,

            'parcel_cancellation_status' => (int)(1),
            'parcel_cancellation_basic_setup' => isset($settings['parcel_cancellation_basic_setup']) ? json_decode($settings['parcel_cancellation_basic_setup']) : null,
            'parcel_return_time_fee' => isset($settings['parcel_return_time_fee']) ? json_decode($settings['parcel_return_time_fee']) : null,

            'open_ai_status' => (int)$openAIStatus,

            'dm_loyality_point_data' => $dm_loyality_point_data,
            'dm_referral_data' => $dm_referral_data,
            'seo_page_list' => Helpers::seoPageList(),
            'download_user_app_links' => $DataSetting
        ]);
    }

    public static function get_settings_status($name)
    {
        $data = Cache::rememberForever('data_settings_' . $name, function () use ($name) {
            return DataSetting::where('key', $name)->value('value');
        });

        return $data ?? 0;
    }

    public function get_zone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $zones = Zone::with('modules')->whereContains('coordinates', new Point($request->lat, $request->lng, POINT_SRID))
            ->selectRaw('zones.*, ABS(ST_Area(coordinates)) as area')->orderBy('area', 'asc')->latest()->get(['id', 'status', 'cash_on_delivery', 'digital_payment', 'offline_payment']);
        if (count($zones) < 1) {
            return response()->json([
                'errors' => [
                    ['code' => 'coordinates', 'message' => translate('messages.service_not_available_in_this_area')],
                ],
            ], 404);
        }
        $data = array_filter($zones->toArray(), function ($zone) {
            if ($zone['status'] == 1) {
                return $zone;
            }
        });

        if (count($data) > 0) {
            return response()->json(['zone_id' => json_encode(array_column($data, 'id')), 'zone_data' => array_values($data)], 200);
        }

        return response()->json([
            'errors' => [
                ['code' => 'coordinates', 'message' => translate('messages.we_are_temporarily_unavailable_in_this_area')],
            ],
        ], 403);
    }

    public function place_api_autocomplete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_text' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $apiKey = $this->map_api_key;
        $url = 'https://places.googleapis.com/v1/places:autocomplete';
        $data = [
            'input' => $request['search_text'],
            'languageCode' => app()->getLocale(),
        ];

        $headers = [
            'Content-Type: application/json',
            "X-Goog-Api-Key: $apiKey",
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function distance_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origin_lat' => 'required',
            'origin_lng' => 'required',
            'destination_lat' => 'required',
            'destination_lng' => 'required',
            'mode' => 'nullable|in:DRIVE,WALK',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $apiKey = $this->map_api_key;
        $url = 'https://routes.googleapis.com/distanceMatrix/v2:computeRouteMatrix';

        $data = [
            'origins' => [
                ['waypoint' => ['location' => ['latLng' => ['latitude' => $request['origin_lat'], 'longitude' => $request['origin_lng']]]]],
            ],
            'destinations' => [
                ['waypoint' => ['location' => ['latLng' => ['latitude' => $request['destination_lat'], 'longitude' => $request['destination_lng']]]]],
            ],
            'travelMode' => $request['mode'] ?? 'WALK',
            // "routingPreference" => "TRAFFIC_AWARE"
        ];

        $headers = [
            'Content-Type: application/json',
            "X-Goog-Api-Key: $apiKey",
            'X-Goog-FieldMask: duration,distanceMeters,localizedValues',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true)[0];
    }

    public function place_api_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'placeid' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $apiKey = $this->map_api_key;
        $url = 'https://places.googleapis.com/v1/places/' . $request['placeid'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Goog-Api-Key: ' . $apiKey,
            'X-Goog-FieldMask: id,displayName,formattedAddress,location',
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function geocode_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $request->lat . ',' . $request->lng . '&key=' . $this->map_api_key);

        return $response->json();
    }


    public function extra_charge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'distance' => 'required',
        ]);
        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $distance_data = $request->distance ?? 1;
        $data = DmVehicle::active()
            ->where(function ($query) use ($distance_data) {
                $query->where('starting_coverage_area', '<=', $distance_data)->where('maximum_coverage_area', '>=', $distance_data)
                    ->orWhere(function ($query) use ($distance_data) {
                        $query->where('starting_coverage_area', '>=', $distance_data);
                    });
            })->orderBy('starting_coverage_area')->first();

        $extra_charges = (float)(isset($data) ? $data->extra_charges : 0);

        return response()->json($extra_charges, 200);
    }

    public function get_vehicles(Request $request)
    {
        $data = DMVehicle::active()->get(['id', 'type']);

        return response()->json($data, 200);
    }

    public function react_landing_page()
    {
        $datas = DataSetting::with('translations')->whereIn('type', ['react_landing_page', 'module_home_page_data', 'module_vendor_registration_data'])->get();
        $data = [];
        foreach ($datas as $key => $value) {
            if (count($value->translations) > 0) {
                $cred = [
                    $value->key => $value->translations[0]['value'],
                ];
                array_push($data, $cred);
            } else {
                $cred = [
                    $value->key => $value->value,
                ];
                array_push($data, $cred);
            }
            if (isset($value->storage)) {

                $cred = [
                    $value->key . '_storage' => $value?->storage[0]?->value ?? 'public',
                ];
                array_push($data, $cred);
            }
        }
        $settings = [];
        foreach ($data as $single_data) {
            foreach ($single_data as $key => $single_value) {
                $settings[$key] = $single_value;
            }
        }

        $reviews = ReactTestimonial::get();
        $promotional_banners = [];
        $promotional_banners_data = ReactPromotionalBanner::where('status', 1)->get();
        foreach ($promotional_banners_data as $value) {
            $promotional_banners[] = $value->image_full_url;
        }

        $zones = Zone::where('status', 1)->get();
        $zones = self::zone_format($zones);

        $heroSection = [
            'header_title' => (isset($settings['header_title'])) ? $settings['header_title'] : null,
            'header_sub_title' => (isset($settings['header_sub_title'])) ? $settings['header_sub_title'] : null,
            'header_tag_line' => (isset($settings['header_tag_line'])) ? $settings['header_tag_line'] : null,
            'pick_location_title' => (isset($settings['pick_location_title'])) ? $settings['pick_location_title'] : null,
        ];
        $trustSection = [
            'trust_section_status' => (isset($settings['trust_section_status'])) ? (int)$settings['trust_section_status'] : 0,
            'cards' => [],
        ];

        for ($i = 1; $i <= 4; $i++) {
            $trustSection['cards'][] = [
                'status' => (int)($settings["trust_status_card_{$i}"] ?? 0),
                'title' => $settings["trust_title_card_{$i}"] ?? null,
                'sub_title' => $settings["trust_sub_title_card_{$i}"] ?? null,
                'image_full_url' => Helpers::get_full_url(
                    'trust_section',
                    $settings["trust_image_card_{$i}"] ?? null,
                    $settings["trust_image_card_{$i}_storage"] ?? 'public'
                ),
            ];
        }

        $availableZoneSection = [
            'available_zone_status' => (int)((isset($settings['available_zone_status'])) ? $settings['available_zone_status'] : 0),
            'available_zone_title' => (isset($settings['available_zone_title'])) ? $settings['available_zone_title'] : null,
            'available_zone_short_description' => (isset($settings['available_zone_short_description'])) ? $settings['available_zone_short_description'] : null,
            'available_zone_list' => $zones,
        ];
        $promotionalBannerSection = [
            'promotion_banner_section_status' => (int)((isset($settings['promotional_banner_section_status'])) ? $settings['promotional_banner_section_status'] : 0),
            'promotion_banners_full_url' => $promotional_banners ?? [],
        ];

        $userAppDownloadSection = [
            'download_user_app_section_status' => (int)((isset($settings['download_user_app_section_status'])) ? $settings['download_user_app_section_status'] : 0),
            'download_user_app_title' => (isset($settings['download_user_app_title'])) ? $settings['download_user_app_title'] : null,
            'download_user_app_sub_title' => (isset($settings['download_user_app_sub_title'])) ? $settings['download_user_app_sub_title'] : null,
            'download_user_app_image_full_url' => Helpers::get_full_url('download_user_app_image', (isset($settings['download_user_app_image'])) ? $settings['download_user_app_image'] : null, isset($settings['download_user_app_image_storage']) ? $settings['download_user_app_image_storage'] : 'public'),
            'download_user_app_button_title' => (isset($settings['download_user_app_button_title'])) ? $settings['download_user_app_button_title'] : null,
            'download_user_app_button_sub_title' => (isset($settings['download_user_app_button_sub_title'])) ? $settings['download_user_app_button_sub_title'] : null,
            'download_user_app_links' => (isset($settings['download_user_app_links'])) ? json_decode($settings['download_user_app_links'], true) : null,
            'download_business_app_links' => (isset($settings['download_business_app_links'])) ? json_decode($settings['download_business_app_links'], true) : null,
        ];

        $popularClientSection = [
            'popular_client_section_status' => (isset($settings['popular_client_section_status'])) ? (int)$settings['popular_client_section_status'] : 0,
            'popular_client_title' => (isset($settings['popular_client_title'])) ? $settings['popular_client_title'] : null,
            'popular_client_sub_title' => (isset($settings['popular_client_sub_title'])) ? $settings['popular_client_sub_title'] : null
        ];
        $popularClientImage= DataSetting::where('key', 'popular_client_image')->whereNotNull('value')->where('value','!=',0)->select('value')->get();
        foreach ($popularClientImage as $image) {
            $settings["popular_client_image"] = $image->value;
            $popularClientSection['cards'][] = [
                'image_full_url' => Helpers::get_full_url(
                    'popular_client_section',
                    $settings["popular_client_image"] ?? null,
                    $settings["popular_client_image_storage"] ?? 'public'
                ),
            ];
        }

        $sellerAppDownloadSection = [
            'download_seller_app_section_status' => (int)((isset($settings['download_seller_app_section_status'])) ? $settings['download_seller_app_section_status'] : 0),
            'download_seller_app_title' => (isset($settings['download_seller_app_title'])) ? $settings['download_seller_app_title'] : null,
            'download_seller_app_sub_title' => (isset($settings['download_seller_app_sub_title'])) ? $settings['download_seller_app_sub_title'] : null,
            'download_seller_app_content_button_title' => (isset($settings['download_seller_app_button_title'])) ? $settings['download_seller_app_button_title'] : null,
            'download_seller_app_image_full_url' => Helpers::get_full_url('download_seller_app_section', (isset($settings['download_seller_app_image'])) ? $settings['download_seller_app_image'] : null, isset($settings['download_seller_app_image_storage']) ? $settings['download_seller_app_image_storage'] : 'public'),
            'download_seller_app_button_title' => (isset($settings['download_seller_app_main_button_title'])) ? $settings['download_seller_app_main_button_title'] : null,
            'download_seller_app_button_sub_title' => (isset($settings['download_seller_app_main_button_sub_title'])) ? $settings['download_seller_app_main_button_sub_title'] : null,
            'download_seller_app_links' => (isset($settings['download_seller_app_links'])) ? json_decode($settings['download_seller_app_links'], true) : null,
            'download_business_app_links' => (isset($settings['download_business_app_links'])) ? json_decode($settings['download_business_app_links'], true) : null,
        ];

        $deliverymanAppDownloadSection = [
            'download_deliveryman_app_section_status' => (int)((isset($settings['download_dm_app_section_status'])) ? $settings['download_dm_app_section_status'] : 0),
            'download_dm_app_title' => (isset($settings['download_dm_app_title'])) ? $settings['download_dm_app_title'] : null,
            'download_dm_app_sub_title' => (isset($settings['download_dm_app_sub_title'])) ? $settings['download_dm_app_sub_title'] : null,
            'download_dm_app_content_button_title' => (isset($settings['download_dm_app_button_title'])) ? $settings['download_dm_app_button_title'] : null,
            'download_dm_app_image_full_url' => Helpers::get_full_url('download_dm_app_section', (isset($settings['download_dm_app_image'])) ? $settings['download_dm_app_image'] : null, isset($settings['download_dm_app_image_storage']) ? $settings['download_dm_app_image_storage'] : 'public'),
            'download_dm_app_button_title' => (isset($settings['download_dm_app_main_button_title'])) ? $settings['download_dm_app_main_button_title'] : null,
            'download_dm_app_button_sub_title' => (isset($settings['download_dm_app_main_button_sub_title'])) ? $settings['download_dm_app_main_button_sub_title'] : null,
            'download_dm_app_links' => (isset($settings['download_dm_app_links'])) ? json_decode($settings['download_dm_app_links'], true) : null,
            'download_business_app_links' => (isset($settings['download_business_app_links'])) ? json_decode($settings['download_business_app_links'], true) : null,
        ];

        $bannerSection = [
            'banner_section_status' => (int)((isset($settings['banner_section_status'])) ? $settings['banner_section_status'] : 0),
            'banner_iamge_full_url' => Helpers::get_full_url('banner_section', (isset($settings['banner'])) ? $settings['banner'] : null, isset($settings['banner_storage']) ? $settings['banner_storage'] : 'public'),
        ];

        $testimonialSection = [
            'testimonial_section_status' => (int)((isset($settings['testimonial_section_status'])) ? $settings['testimonial_section_status'] : 0),
            'testimonial_title' => (isset($settings['testimonial_title'])) ? $settings['testimonial_title'] : null,
            'testimonial_sub_title' => (isset($settings['testimonial_sub_title'])) ? $settings['testimonial_sub_title'] : null,
            'testimonial_button_title' => (isset($settings['testimonial_button_title'])) ? $settings['testimonial_button_title'] : null,
            'testimonial_list' => (isset($reviews)) ? $reviews : null,
        ];

        $gallerySection = [
            'gallery_section_status' => (int)((isset($settings['gallery_section_status'])) ? $settings['gallery_section_status'] : 0),
            'gallery_content_title' => (isset($settings['gallery_content_title'])) ? $settings['gallery_content_title'] : null,
            'gallery_content_sub_title' => (isset($settings['gallery_content_sub_title'])) ? $settings['gallery_content_sub_title'] : null,
        ];

        for ($i = 1; $i <= 4; $i++) {
            $gallerySection['cards'][] = [
                'status' => (int)((isset($settings["gallery_image_{$i}_status"])) ? $settings["gallery_image_{$i}_status"] : 0),
                'image_full_url' => Helpers::get_full_url(
                    'gallery_section',
                    $settings["gallery_image_{$i}"] ?? null,
                    $settings["gallery_image_{$i}_storage"] ?? 'public'
                ),
            ];
        }

        $highlightSection = [
            'highlight_section_status' => (int)((isset($settings['highlight_section_status'])) ? $settings['highlight_section_status'] : 0),
            'highlight_title' => (isset($settings['highlight_title'])) ? $settings['highlight_title'] : null,
            'highlight_sub_title' => (isset($settings['highlight_sub_title'])) ? $settings['highlight_sub_title'] : null,
            'highlight_button_title' =>  null,
            'highlight_image_full_url' => Helpers::get_full_url('highlight_section', (isset($settings['highlight_image'])) ? $settings['highlight_image'] : null, isset($settings['highlight_image_storage']) ? $settings['highlight_image_storage'] : 'public'),

        ];

        $faqs = FAQ::get();

        $faqSection = [
            'faq_section_status' => (int)((isset($settings['faq_section_status'])) ? $settings['faq_section_status'] : 0),
            'faq_title' => (isset($settings['faq_title'])) ? $settings['faq_title'] : null,
            'faq_list' => (isset($faqs)) ? $faqs : null,
        ];

        $footerSection = [
            'fixed_newsletter_title' => (isset($settings['fixed_newsletter_title'])) ? $settings['fixed_newsletter_title'] : null,
            'fixed_newsletter_sub_title' => (isset($settings['fixed_newsletter_sub_title'])) ? $settings['fixed_newsletter_sub_title'] : null,
            'fixed_footer_description' => (isset($settings['fixed_footer_description'])) ? $settings['fixed_footer_description'] : null,
            'fixed_promotional_banner_full_url' => Helpers::get_full_url('promotional_banner', (isset($settings['fixed_promotional_banner'])) ? $settings['fixed_promotional_banner'] : null, (isset($settings['fixed_promotional_banner_storage'])) ? $settings['fixed_promotional_banner_storage'] : 'public'),
        ];

        $companyIntroSection = [
            'company_title' => (isset($settings['company_title'])) ? $settings['company_title'] : null,
            'company_sub_title' => (isset($settings['company_sub_title'])) ? $settings['company_sub_title'] : null,
            'company_description' => (isset($settings['company_description'])) ? $settings['company_description'] : null,
            'company_button_name' => (isset($settings['company_button_name'])) ? $settings['company_button_name'] : null,
            'company_button_url' => (isset($settings['company_button_url'])) ? $settings['company_button_url'] : null,
        ];

        return response()->json(
            [
                'hero_section' => $heroSection,
                'trust_section' => $trustSection,
                'available_zone_section' => $availableZoneSection,
                'promotional_banner_section' => $promotionalBannerSection,
                'user_app_download_section' => $userAppDownloadSection,
                'popular_client_section' => $popularClientSection,
                'seller_app_download_section' => $sellerAppDownloadSection,
                'deliveryman_app_download_section' => $deliverymanAppDownloadSection,
                'banner_section' => $bannerSection,
                'testimonial_section' => $testimonialSection,
                'gallery_section' => $gallerySection,
                'highlight_section' => $highlightSection,
                'faq_section' => $faqSection,
                'footer_section' => $footerSection,
                'company_intro_section' => $companyIntroSection,


                'module_home_page_data_title' => (isset($settings['module_home_page_data_title'])) ? $settings['module_home_page_data_title'] : null,
                'module_home_page_data_sub_title' => (isset($settings['module_home_page_data_sub_title'])) ? $settings['module_home_page_data_sub_title'] : null,
                'module_home_page_data_image' => isset($settings['module_home_page_data_image']) ?

                    Helpers::get_full_url('react_landing', $settings['module_home_page_data_image'] ?? '', $settings['module_home_page_data_image_storage'] ?? 'public', 'upload_image_1') : '',

                'module_vendor_registration_data_title' => (isset($settings['module_vendor_registration_data_title'])) ? $settings['module_vendor_registration_data_title'] : null,
                'module_vendor_registration_data_sub_title' => (isset($settings['module_vendor_registration_data_sub_title'])) ? $settings['module_vendor_registration_data_sub_title'] : null,
                'module_vendor_registration_data_button_title' => (isset($settings['module_vendor_registration_data_button_title'])) ? $settings['module_vendor_registration_data_button_title'] : null,
                'module_vendor_registration_data_image' => isset($settings['module_vendor_registration_data_image']) ?
                    Helpers::get_full_url('react_landing', $settings['module_vendor_registration_data_image'] ?? '', $settings['module_vendor_registration_data_image_storage'] ?? 'public', 'upload_image_1') : '',

                'meta_title' => (isset($settings['meta_title'])) ? $settings['meta_title'] : null,
                'meta_description' => (isset($settings['meta_description'])) ? $settings['meta_description'] : null,
                'meta_image' =>Helpers::get_full_url('landing/meta_image', (isset($settings['meta_image'])) ? $settings['meta_image'] : null, (isset($settings['meta_image_storage'])) ? $settings['meta_image_storage'] : 'public'),

            ]
        );
    }

    public function flutter_landing_page()
    {
        $datas = DataSetting::with('translations')->where('type', 'flutter_landing_page')->get();
        $data = [];
        foreach ($datas as $key => $value) {
            if (count($value->translations) > 0) {
                $cred = [
                    $value->key => $value->translations[0]['value'],
                ];
                array_push($data, $cred);
            } else {
                $cred = [
                    $value->key => $value->value,
                ];
                array_push($data, $cred);
            }
            if (isset($value->storage)) {

                $cred = [
                    $value->key . '_storage' => $value?->storage[0]?->value ?? 'public',
                ];
                array_push($data, $cred);
            }
        }
        $settings = [];
        foreach ($data as $single_data) {
            foreach ($single_data as $key => $single_value) {
                $settings[$key] = $single_value;
            }
        }

        $zones = Zone::where('status', 1)->get();
        $zones = self::zone_format($zones);

        $criterias = FlutterSpecialCriteria::where('status', 1)->get();

        $awsUrl = config('filesystems.disks.s3.url');
        $awsBucket = config('filesystems.disks.s3.bucket');
        $awsBaseURL = rtrim($awsUrl, '/') . '/' . ltrim($awsBucket . '/');

        return response()->json(
            [
                'base_urls' => [
                    'fixed_header_image' => asset('storage/app/public/fixed_header_image'),
                    'special_criteria_image' => asset('storage/app/public/special_criteria'),
                    'download_user_app_image' => asset('storage/app/public/download_user_app_image'),
                ],

                's3_base_urls' => [
                    'fixed_header_image' => $awsBaseURL . 'fixed_header_image',
                    'special_criteria_image' => $awsBaseURL . 'special_criteria',
                    'download_user_app_image' => $awsBaseURL . 'download_user_app_image',
                ],

                'fixed_header_title' => (isset($settings['fixed_header_title'])) ? $settings['fixed_header_title'] : null,
                'fixed_header_sub_title' => (isset($settings['fixed_header_sub_title'])) ? $settings['fixed_header_sub_title'] : null,
                'fixed_header_image' => (isset($settings['fixed_header_image'])) ? $settings['fixed_header_image'] : null,
                'fixed_header_image_full_url' => Helpers::get_full_url('fixed_header_image', (isset($settings['fixed_header_image'])) ? $settings['fixed_header_image'] : null, (isset($settings['fixed_header_image_storage'])) ? $settings['fixed_header_image_storage'] : 'public'),
                'fixed_module_title' => (isset($settings['fixed_module_title'])) ? $settings['fixed_module_title'] : null,
                'fixed_module_sub_title' => (isset($settings['fixed_module_sub_title'])) ? $settings['fixed_module_sub_title'] : null,
                'fixed_location_title' => (isset($settings['fixed_location_title'])) ? $settings['fixed_location_title'] : null,
                'join_seller_title' => (isset($settings['join_seller_title'])) ? $settings['join_seller_title'] : null,
                'join_seller_sub_title' => (isset($settings['join_seller_sub_title'])) ? $settings['join_seller_sub_title'] : null,
                'join_seller_button_name' => (isset($settings['join_seller_button_name'])) ? $settings['join_seller_button_name'] : null,
                'join_seller_status' => (int)((isset($settings['join_seller_flutter_status'])) ? $settings['join_seller_flutter_status'] : 0),
                'join_delivery_man_title' => (isset($settings['join_delivery_man_title'])) ? $settings['join_delivery_man_title'] : null,
                'join_delivery_man_sub_title' => (isset($settings['join_delivery_man_sub_title'])) ? $settings['join_delivery_man_sub_title'] : null,
                'join_delivery_man_button_name' => (isset($settings['join_delivery_man_button_name'])) ? $settings['join_delivery_man_button_name'] : null,
                'join_delivery_man_status' => (int)((isset($settings['join_DM_flutter_status'])) ? $settings['join_DM_flutter_status'] : 0),

                'download_user_app_title' => (isset($settings['download_user_app_title'])) ? $settings['download_user_app_title'] : null,
                'download_user_app_sub_title' => (isset($settings['download_user_app_sub_title'])) ? $settings['download_user_app_sub_title'] : null,
                'download_user_app_image' => (isset($settings['download_user_app_image'])) ? $settings['download_user_app_image'] : null,
                'download_user_app_image_full_url' => Helpers::get_full_url('download_user_app_image', (isset($settings['download_user_app_image'])) ? $settings['download_user_app_image'] : null, (isset($settings['download_user_app_image_storage'])) ? $settings['download_user_app_image_storage'] : 'public'),

                'special_criterias' => (isset($criterias)) ? $criterias : null,

                'download_user_app_links' => (isset($settings['download_user_app_links'])) ? json_decode($settings['download_user_app_links'], true) : null,
                'available_zone_status' => (int)((isset($settings['available_zone_status'])) ? $settings['available_zone_status'] : 0),
                'available_zone_title' => (isset($settings['available_zone_title'])) ? $settings['available_zone_title'] : null,
                'available_zone_short_description' => (isset($settings['available_zone_short_description'])) ? $settings['available_zone_short_description'] : null,
                'available_zone_image' => (isset($settings['available_zone_image'])) ? $settings['available_zone_image'] : null,
                'available_zone_image_full_url' => Helpers::get_full_url('available_zone_image', (isset($settings['available_zone_image'])) ? $settings['available_zone_image'] : null, (isset($settings['available_zone_image_storage'])) ? $settings['available_zone_image_storage'] : 'public'),
                'available_zone_list' => $zones,
            ]
        );
    }


    public function offline_payment_method_list(Request $request)
    {
        $data = OfflinePaymentMethod::where('status', 1)->get();
        $data = $data->count() > 0 ? $data : null;

        return response()->json($data, 200);
    }

    private function zone_format($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $storage[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'display_name' => $item['display_name'] ? $item['display_name'] : $item['name'],
                'modules' => $item->modules->pluck('module_name'),
            ];
        }
        $data = $storage;

        return $data;
    }

    public function direction_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origin_lat' => 'required',
            'origin_lng' => 'required',
            'destination_lat' => 'required',
            'destination_lng' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $apiKey = $this->map_api_key;
        $url = 'https://routes.googleapis.com/directions/v2:computeRoutes';

        $data = [
            'origin' => [
                'location' => [
                    'latLng' => [
                        'latitude' => $request['origin_lat'],
                        'longitude' => $request['origin_lng'],
                    ],
                ],
            ],
            'destination' => [
                'location' => [
                    'latLng' => [
                        'latitude' => $request['destination_lat'],
                        'longitude' => $request['destination_lng'],
                    ],
                ],
            ],
            'travelMode' => strtoupper($request['mode'] ?? 'DRIVE'),
            'routingPreference' => 'TRAFFIC_AWARE',
        ];

        $headers = [
            'Content-Type: application/json',
            "X-Goog-Api-Key: $apiKey",
            'X-Goog-FieldMask: routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => $error];
        }

        return json_decode($response, true);
    }

    public function parcel_cancellation_reason(Request $request)
    {
        $limit = $request->query('limit', 25);
        $offset = $request->query('offset', 1);

        $reasons = ParcelCancellationReason::where('status', 1)
            ->select('id', 'reason', 'user_type', 'cancellation_type')
            ->when($request->user_type, function ($query) use ($request) {
                $query->where('user_type', $request->user_type);
            })
            ->when($request->cancellation_type, function ($query) use ($request) {
                $query->where('cancellation_type', $request->cancellation_type);
            })
            ->paginate($limit, ['*'], 'page', $offset);

        $data = [
            'total_size' => $reasons->total(),
            'limit' => $limit,
            'offset' => $offset,
            'data' => $reasons->getCollection()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reason' => $item->reason,
                    'user_type' => $item->user_type,
                    'cancellation_type' => $item->cancellation_type,
                ];
            }),
        ];

        return response()->json($data, 200);
    }

    public function analyticScripts()
    {
        $analytics = Cache::rememberForever('analytic_script', function () {
            return AnalyticScript::where('is_active', 1)->select(['type', 'script_id'])->get()->toArray();
        });

        return response()->json($analytics ?? [], 200);
    }

    public function getPageMetaData(Request $request)
    {
        $pages = Helpers::seoPageList();

        if ($request->page_name && in_array($request->page_name, $pages)) {
            if ($request->page_name == 'category_list' && $request->id) {
                $data = Category::where(fn($q) =>$q->where('id', request('id'))->orWhere('slug', request('id')))->select(['id', 'name', 'meta_title', 'meta_description', 'meta_image', 'meta_data'])->first();
                $data = $this->formateSeoData($data);
            } else {
                $data = PageSeoData::where('status', 1)->where('page_name',$request->page_name)->select(['id', 'title', 'description', 'image', 'meta_data'])->first();
                $data = $this->formateSeoData($data, 'pageData');
            }
        }
        return response()->json($data ?? [], 200);
    }

    private function formateSeoData($data, $type = null)
    {
        $data = [
            'title' => $type == 'pageData' ? $data?->title : $data?->meta_title ?? null,
            'description' => $type == 'pageData' ? $data?->description : $data?->meta_description ?? null,
            'image_full_url' => $type == 'pageData' ?  $data?->image_full_url : $data?->meta_image_full_url,
            'meta_data' => $data?->meta_data ?? [],
        ];
        return $data;
    }
}
