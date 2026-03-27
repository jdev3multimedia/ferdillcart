<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ExternalConfigurationController extends Controller
{
    public function index()
    {
        if(auth('admin')->user()->role_id != 1){
            Toastr::warning(translate('messages.access_denied'));
            return back();
        }
        return view('admin-views.external-configuration.external-index');
    }
    public function updateDrivemondConfiguration(Request $request)
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }
        if (array_key_exists('activation_mode',$request->all())){
            DB::table('external_configurations')->updateOrInsert(['key' => 'activation_mode'], [
                'value' => 1
            ]);
        }else{
            DB::table('external_configurations')->updateOrInsert(['key' => 'activation_mode'], [
                'value' => 0
            ]);
        }
        DB::table('external_configurations')->updateOrInsert(['key' => 'drivemond_base_url'], [
            'value' => $request['drivemond_base_url']
        ]);

        DB::table('external_configurations')->updateOrInsert(['key' => 'drivemond_token'], [
            'value' => $request['drivemond_token']
        ]);
        DB::table('external_configurations')->updateOrInsert(['key' => 'system_self_token'], [
            'value' => $request['system_self_token']
        ]);
        $activationMode = DB::table('external_configurations')->where('key', 'activation_mode')->first();
        if ($activationMode && $activationMode->value==1) {
            $response = Http::get($request['drivemond_base_url'].'/api/configurations');
            if ($response->status()==200){
                $driveMondConfig = $response->json();
                DB::table('external_configurations')->updateOrInsert(['key' => 'drivemond_business_name'], [
                    'value' => $driveMondConfig['business_name']
                ]);
                DB::table('external_configurations')->updateOrInsert(['key' => 'drivemond_business_logo'], [
                    'value' => $driveMondConfig['logo']
                ]);
                DB::table('external_configurations')->updateOrInsert(['key' => 'drivemond_app_url_ios'], [
                    'value' => $driveMondConfig['app_url_ios']
                ]);

                DB::table('external_configurations')->updateOrInsert(['key' => 'drivemond_app_url_android'], [
                    'value' => $driveMondConfig['app_url_android']
                ]);


                Toastr::success(translate('messages.successfully_updated_to_changes_restart_app'));
                return back();
            }
            DB::table('external_configurations')->updateOrInsert(['key' => 'activation_mode'], [
                'value' => 0
            ]);
            Toastr::warning(translate('messages.something_went_wrong,please_check_drivemond_base_url'));
            return back();
        }
        Toastr::success(translate('messages.successfully_updated_to_changes_restart_app'));
        return back();
    }

}
