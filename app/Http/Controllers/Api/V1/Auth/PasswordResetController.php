<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use App\Models\Setting;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Carbon;
use App\Models\BusinessSetting;
use App\CentralLogics\SMS_module;
use Illuminate\Support\Facades\DB;
use App\Mail\UserPasswordResetMail;
use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Modules\Gateways\Traits\SmsGateway;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    public function reset_password_request(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'verification_method' => 'required|in:phone,email',
            'phone' => 'required_if:verification_method,phone',
            'email' => 'required_if:verification_method,email',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $firebase_otp_verification = BusinessSetting::where('key', 'firebase_otp_verification')->first()->value??0;

        $customer = User::when(isset($request->phone) ,function($query) use ($request){
            return $query->where('phone', $request->phone);
        })->when(isset($request->email) ,function($query) use ($request){
            return $query->where('email', $request->email);
        })->first();


        if (isset($customer)) {
            if($firebase_otp_verification)
            {
                return response()->json(['message' => translate('messages.otp_sent_successfull')], 200);
            }

            $otp_interval_time= 60;
            $password_verification_data= PasswordReset::
            when(isset($request->phone) ,function($query) use ($request){
                return $query->where('phone', $request->phone);
            })
            ->when(isset($request->email) ,function($query) use ($request){
                return $query->where('email', $request->email);
            })->first();



            if(isset($password_verification_data) &&  Carbon::parse($password_verification_data->created_at)->DiffInSeconds() < $otp_interval_time){
                $time= $otp_interval_time - Carbon::parse($password_verification_data->created_at)->DiffInSeconds();
                $errors = [];
                array_push($errors, ['code' => 'otp', 'message' =>  translate('messages.please_try_again_after_').$time.' '.translate('messages.seconds')]);
                return response()->json([
                    'errors' => $errors
                ], 405);
            }


            $token = rand(100000,999999);
            if(env('APP_MODE') == 'test'){
                $token = '123456';
            }
               DB::table('password_resets')->updateOrInsert(isset($request->phone) ? ['phone' => $request->phone] : ['email' => $request->email],
               [
                'token' => $token,
                'created_at' => now(),
            ]);

            if(env('APP_MODE') == 'test'){
                return response()->json(['message' => translate('messages.Use_test_OTP')], 200);
            }

            $is_sms_active= Setting::whereJsonContains('live_values->status','1')->where('settings_type', 'sms_config')->exists();
            if($is_sms_active ){
                $response =null;
                $published_status =0;
                $payment_published_status = config('get_payment_publish_status');
                if (isset($payment_published_status[0]['is_published'])) {
                    $published_status = $payment_published_status[0]['is_published'];
                }

                if($published_status == 1){
                    $response = SmsGateway::send($request['phone'],$token);
                }else{
                    $response = SMS_module::send($request['phone'],$token);
                }

                if($response == 'success'){
                    return response()->json(['message' => translate('messages.Otp_Successfully_Sent_To_Your_Phone')], 200);
                } else {
                    return response()->json([
                        'errors' => [
                            ['code' => 'otp', 'message' => translate('messages.failed_to_send_sms')]
                    ]], 403);
                }
            }
            elseif(config('mail.status')){
                try {
                    $mailResponse=null;
                        if (Helpers::get_mail_status('forget_password_mail_status_user') == '1' && $customer['email']) {
                            Mail::to($customer?->getRawOriginal('email'))->send(new UserPasswordResetMail($token,$customer['f_name']));
                            $mailResponse='success';
                        }
                } catch (\Throwable $th) {
                        $mailResponse=null;
                        info($th->getMessage());
                    }

                    if ($mailResponse == 'success') {
                        return response()->json(['message' => translate('messages.Otp_Successfully_Sent_To_Your_Mail')], 200);
                    } else  {
                        return response()->json([
                            'errors' => [
                                ['code' => 'otp', 'message' => translate('messages.failed_to_send_mail')]
                        ]], 403);
                    }
                }
                else
                {
                    return response()->json([
                        'errors' => [
                            ['code' => 'otp', 'message' => translate('messages.failed_to_send_otp')]
                    ]], 403);
                }

        }
        return response()->json(['errors' => [
            ['code' => 'not-found', 'message' =>  translate('messages.user_not_found!')]
        ]], 404);
    }

    public function verify_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_token'=> 'required',
            'verification_method' => 'required|in:phone,email',
            'phone' => 'nullable|required_if:verification_method,phone|min:9',
            'email' => 'nullable|required_if:verification_method,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user=User::when(isset($request->phone) ,function($query) use ($request){
            return $query->where('phone', $request->phone);
        })->when(isset($request->email) ,function($query) use ($request){
            return $query->where('email', $request->email);
        })->first();

        if (!isset($user)) {
            return response()->json(['errors' => [
                ['code' => 'not-found', 'message' => translate('Phone_number_not_found!')]
            ]], 404);
        }

        if(env('APP_MODE')=='test')
        {
            if($request['reset_token']=="123456")
            {
                return response()->json(['message'=>"OTP found, you can proceed"], 200);
            }
            return response()->json(['errors' => [
                ['code' => 'invalid', 'message' => translate('Invalid OTP.')]
            ]], 400);
        }

        $data = DB::table('password_resets')->where(['token' => $request['reset_token']])->where(isset($request->phone) ? ['phone' => $request->phone] : ['email' => $request->email])->first();
        if (isset($data)) {
            return response()->json(['message'=> translate('OTP_found,_you_can_proceed')], 200);
        } else{
            // $otp_hit = BusinessSetting::where('key', 'max_otp_hit')->first();
            // $max_otp_hit =isset($otp_hit) ? $otp_hit->value : 5 ;
            $max_otp_hit = 5;
            // $otp_hit_time = BusinessSetting::where('key', 'max_otp_hit_time')->first();
            // $max_otp_hit_time = isset($otp_hit_time) ? $otp_hit_time->value : 30 ;
            $max_otp_hit_time = 60; // seconds
            $temp_block_time = 600; // seconds
            $verification_data= DB::table('password_resets')->where(isset($request->phone) ? ['phone' => $request->phone] : ['email' => $request->email])->first();

            if(isset($verification_data)){
                $time= $temp_block_time - Carbon::parse($verification_data->temp_block_time)->DiffInSeconds();

                if(isset($verification_data->temp_block_time ) && Carbon::parse($verification_data->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                    $time= $temp_block_time - Carbon::parse($verification_data->temp_block_time)->DiffInSeconds();

                    $errors = [];
                    array_push($errors, ['code' => 'otp_block_time', 'message' => translate('messages.please_try_again_after_').CarbonInterval::seconds($time)->cascade()->forHumans()
                    ]);
                    return response()->json([
                        'errors' => $errors
                    ], 405);
                }

                if($verification_data->is_temp_blocked == 1 && Carbon::parse($verification_data->created_at)->DiffInSeconds() >= $max_otp_hit_time){
                    DB::table('password_resets')->updateOrInsert(isset($request->phone) ? ['phone' => $request->phone] : ['email' => $request->email],
                        [
                            'otp_hit_count' => 0,
                            'is_temp_blocked' => 0,
                            'temp_block_time' => null,
                            'created_at' => now(),
                        ]);
                }

                if($verification_data->otp_hit_count >= $max_otp_hit &&  Carbon::parse($verification_data->created_at)->DiffInSeconds() < $max_otp_hit_time &&  $verification_data->is_temp_blocked == 0){

                    DB::table('password_resets')->updateOrInsert(isset($request->phone) ? ['phone' => $request->phone] : ['email' => $request->email],
                        [
                            'is_temp_blocked' => 1,
                            'temp_block_time' => now(),
                            'created_at' => now(),
                        ]);
                    $errors = [];
                    array_push($errors, ['code' => 'otp_temp_blocked', 'message' => translate('messages.Too_many_attemps') ]);
                    return response()->json([
                        'errors' => $errors
                    ], 405);
                }
            }


            DB::table('password_resets')->updateOrInsert(isset($request->phone) ? ['phone' => $request->phone] : ['email' => $request->email],
                [
                    'otp_hit_count' => DB::raw('otp_hit_count + 1'),
                    'created_at' => now(),
                    'temp_block_time' => null,
                ]);
        }

        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => translate('Invalid OTP.')]
        ]], 400);
    }

    public function reset_password_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_token'=> 'required',
            'password' => ['required', Password::min(8)],
            'verification_method' => 'required|in:phone,email',
            'phone' => 'nullable|required_if:verification_method,phone|min:9|exists:users,phone',
            'email' => 'nullable|required_if:verification_method,email|exists:users,email',
            'confirm_password'=> 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        $user=User::when(isset($request->phone) ,function($query) use ($request){
            return $query->where('phone', $request->phone);
        })->when(isset($request->email) ,function($query) use ($request){
            return $query->where('email', $request->email);
        })->first();



        $data = PasswordReset::where(['token' => $request['reset_token']])->where(isset($request->phone) ? ['phone' => $request->phone] : ['email' => $request->email])->first();

        if(env('APP_MODE')=='test')
        {
            if($request['reset_token']=="123456")
            {
                $user->password = bcrypt($request['confirm_password']);
                $user->save();

                if (isset($data)) {
                    PasswordReset::where(['token' => $request['reset_token']])->where(isset($request->phone) ? ['phone' => $request->phone] : ['email' => $request->email])->delete();
                }
                return response()->json(['message' => translate('Password changed successfully.')], 200);
            }
            return response()->json([
                'message' => translate('OTP does not match')
            ], 404);
        }


        if (isset($data)) {
            if ($request['password'] == $request['confirm_password']) {
                $user->password = bcrypt($request['confirm_password']);
                $user->save();
                PasswordReset::where(['token' => $request['reset_token']])->where(isset($request->phone) ? ['phone' => $request->phone] : ['email' => $request->email])->delete();
                return response()->json(['message' => translate('Password changed successfully.')], 200);
            }
            return response()->json(['errors' => [
                ['code' => 'mismatch', 'message' => translate('Password did,t match!')]
            ]], 401);
        }
        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => translate('messages.invalid_otp')]
        ]], 400);
    }

    public function firebase_auth_verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sessionInfo' => 'required',
            'phoneNumber' => 'required',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        $webApiKey = BusinessSetting::where('key', 'firebase_web_api_key')->first()->value??'';

//        $firebaseOTPVerification = Helpers::get_business_settings('firebase_otp_verification');
//        $webApiKey = $firebaseOTPVerification ? $firebaseOTPVerification['web_api_key'] : '';

        $response = Http::post('https://identitytoolkit.googleapis.com/v1/accounts:signInWithPhoneNumber?key='. $webApiKey, [
            'sessionInfo' => $request->sessionInfo,
            'phoneNumber' => $request->phoneNumber,
            'code' => $request->code,
        ]);

        $responseData = $response->json();

        if (isset($responseData['error'])) {
            $errors = [];
            $errors[] = ['code' => "403", 'message' => $responseData['error']['message']];
            return response()->json(['errors' => $errors], 403);
        }

        $user = User::Where(['phone' => $request->phoneNumber])->first();

        if (isset($user)){
            if ($request['is_reset_token'] == 1){
                DB::table('password_resets')->updateOrInsert(['phone' => $user->phone],
                    [
                        'token' => $request->code,
                        'created_at' => now(),
                    ]);
                return response()->json(['message'=>"OTP found, you can proceed"], 200);
            }else{
                if ($user->is_phone_verified) {
                    return response()->json([
                        'message' => translate('messages.phone_number_is_already_verified')
                    ], 200);
                }
                $user->is_phone_verified = 1;
                $user->save();

                return response()->json([
                    'message' => translate('messages.phone_number_verified_successfully'),
                    'otp' => 'inactive'
                ], 200);
            }
        }

        return response()->json([
            'message' => translate('messages.not_found')
        ], 404);
    }
}
