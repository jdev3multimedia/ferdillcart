<?php

namespace App\Services;

class CouponService
{

    public function getAddData(Object $request, int|string $moduleId): array
    {
        $data  = '';
        $customerId  = $request->customer_ids ?? ['all'];
        if($request->coupon_type == 'zone_wise')
        {
            $data = $request->zone_ids;
        }
        else if($request->coupon_type == 'store_wise')
        {
            $data = $request->store_ids;
        }
        return [
            'title' => $request->title[array_search('default', $request->lang)],
            'code' => $request->code,
            'limit' => $request->coupon_type=='first_order'?1:$request->limit,
            'coupon_type' => $request->coupon_type,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
            'max_discount' => $request->max_discount != null ? $request->max_discount : 0,
            'discount' => $request->discount_type == 'amount' ? $request->discount : $request['discount'],
            'discount_type' => $request->discount_type??'',
            'status' =>  1,
            'created_by' =>  'admin',
            'data' =>  json_encode($data),
            'customer_id' =>  json_encode($customerId),
            'module_id' => $moduleId,
            'store_id' => is_array($data) && $request->coupon_type == 'store_wise' ? $data[0] : null ,
        ];
    }


}
