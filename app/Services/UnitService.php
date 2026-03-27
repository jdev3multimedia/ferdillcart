<?php

namespace App\Services;

class UnitService
{

    public function getAddData(Object $request): array
    {
        return [
            'unit' => $request->unit[array_search('default', $request->lang)],
        ];
    }

    public function processExportData(Object $collection): array
    {
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                translate('messages.id') => $item['id'],
                translate('messages.unit') => $item['unit'],
            ];
        }
        return $data;
    }

}
