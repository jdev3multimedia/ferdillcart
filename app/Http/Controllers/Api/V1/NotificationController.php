<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;

class NotificationController extends Controller
{
    public function get_notifications(Request $request){

        Helpers::setZoneIds($request);
        $zone_id= $request->header('zoneId');
        $zone_id= json_decode($zone_id,true)  ?? [];

        try {
            $notifications = Notification::active()->where('tergat', 'customer')->where(function($q)use($zone_id){
                $q->whereNull('zone_id')->orWhereIn('zone_id', $zone_id);
            })->where('updated_at', '>=', \Carbon\Carbon::today()->subDays(15))->get();
            $notifications->append('data');

            $user_notifications = UserNotification::where('user_id', $request->user()->id)->where('updated_at', '>=', \Carbon\Carbon::today()->subDays(15))->get();
            $notifications =  $notifications->merge($user_notifications);
            return response()->json($notifications, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

}
