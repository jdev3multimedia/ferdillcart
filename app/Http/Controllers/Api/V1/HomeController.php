<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\DataSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function terms_and_conditions(Request $request)
    {
        $current_language = $request->header('X-localization') ?? 'en';
        $data = self::get_settings_localization('terms_and_conditions',$current_language);
        return response()->json($data);
    }

    public function about_us(Request $request)
    {
        $current_language = $request->header('X-localization') ?? 'en';
        $data = self::get_settings_localization('about_us',$current_language);
        return response()->json($data);
    }

    public function privacy_policy(Request $request)
    {
        $current_language = $request->header('X-localization') ?? 'en';
        $data = self::get_settings_localization('privacy_policy',$current_language);
        return response()->json($data);
    }

    public function refund_policy(Request $request)
    {
        $current_language = $request->header('X-localization') ?? 'en';
        $data = self::get_settings_localization('refund_policy',$current_language);
        return response()->json($data);
    }

    public function shipping_policy(Request $request)
    {
        $current_language = $request->header('X-localization') ?? 'en';
        $data = self::get_settings_localization('shipping_policy',$current_language);
        return response()->json($data);
    }

    public function cancelation(Request $request)
    {
        $current_language = $request->header('X-localization') ?? 'en';
        $data = self::get_settings_localization('cancellation_policy',$current_language);
        return response()->json($data);
    }

    public static function get_settings_localization($name,$lang)
    {
        $data = DataSetting::withoutGlobalScope('translate')->with(['translations' => function ($query) use ($lang) {
            return $query->where('locale', $lang);
        }])->where(['key' => $name])->first();
        if($data && count($data->translations)>0){
            $data = $data->translations[0]['value'];
        }else{
            $data = $data ? $data->value: '';
        }
        return $data;
    }
}
