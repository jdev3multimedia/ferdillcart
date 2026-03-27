<?php

namespace App\Services;
class WalletBonusService
{
    public function getAddData(Object $request): array
    {
        return [
            "title" => $request->title[array_search('default', $request->lang)],
            "description" => $request->description[array_search('default', $request->lang)],
            "bonus_type" => $request->bonus_type,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "minimum_add_amount" => $request->minimum_add_amount != null ? $request->minimum_add_amount : 0,
            "maximum_bonus_amount" => $request->maximum_bonus_amount != null ? $request->maximum_bonus_amount : 0,
            "bonus_amount" => $request->bonus_amount,
            "status" =>  1,
        ];
    }

    public function getUpdateData(Object $request): array
    {
        return [
            "title" => $request->title[array_search('default', $request->lang)],
            "description" => $request->description[array_search('default', $request->lang)],
            "bonus_type" => $request->bonus_type,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "minimum_add_amount" => $request->minimum_add_amount != null ? $request->minimum_add_amount : 0,
            "maximum_bonus_amount" => $request->maximum_bonus_amount != null ? $request->maximum_bonus_amount : 0,
            "bonus_amount" => $request->bonus_amount,
        ];
    }

}
