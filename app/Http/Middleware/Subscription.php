<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;

class Subscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$module): Response
    {
        if (auth('vendor_employee')->check() || auth('vendor')->check()) {
            $store= Helpers::get_store_data();
            if($store->store_business_model== 'commission'){
                return $next($request);
            }


            elseif($store->store_business_model == 'unsubscribed') {
                Toastr::error(translate('messages.your_subscription_is_expired.You_can_only_process_your_on_going_orders.'));
                return back();
            }
            elseif($store->store_business_model == 'none') {
                Toastr::error(translate('Please_chose_a_business_plan_to_continue_your_services'));
                return back();
            }


            elseif($store->store_business_model == 'subscription') {
                    if($store->store_sub == null){
                        Toastr::error(translate('messages.you_are_not_subscribed_to_any_package'));
                        return back();
                    } else {
                    $store_sub=$store?->store_sub;

                    $modulePermissons = [
                        'reviews' => $store_sub?->review,
                        'pos' => $store_sub?->pos,
                        'deliveryman' => $store_sub?->self_delivery,
                        'chat' => $store_sub?->chat,
                    ];
                    if (in_array($module,['reviews','pos','deliveryman','chat']) ) {
                        if ($modulePermissons[$module] == 1) {
                            return $next($request);
                        } else {
                            Toastr::error(translate('messages.your_package_does_not_include_this_section'));
                            return back();
                        }
                    }
                }
            }


        }
        return $next($request);
    }
}
