<?php

namespace App\Services;
class CashBackService
{
    public function getAddData(Object $request): array
    {
        $customerId  = $request->customer_id ?? ['all'];
        return [
            "title" => $request->title[array_search('default', $request->lang)],
            'customer_id' =>  json_encode($customerId),
            "cashback_type" => $request->cashback_type,
            "same_user_limit" => $request->same_user_limit,
            "cashback_amount" => $request->cashback_amount,
            "min_purchase" => $request->min_purchase != null ? $request->min_purchase : 0,
            "max_discount" => $request->max_discount != null ? $request->max_discount : 0,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
        ];
    }

    public function getUpdateData(Object $request): array
    {
        $customerId  = $request->customer_id ?? ['all'];
        return [
            "title" => $request->title[array_search('default', $request->lang)],
            'customer_id' =>  json_encode($customerId),
            "cashback_type" => $request->cashback_type,
            "same_user_limit" => $request->same_user_limit,
            "cashback_amount" => $request->cashback_amount,
            "min_purchase" => $request->min_purchase != null ? $request->min_purchase : 0,
            "max_discount" => $request->max_discount != null ? $request->max_discount : 0,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
        ];
    }

}
