<?php

namespace Modules\TaxModule\Http\Controllers\Api\V1;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\TaxModule\Entities\Tax;
use Modules\TaxModule\Services\CalculateTaxService;

class TaxController extends Controller
{
    public function getTaxVatList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|numeric',
            'offset' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $this->error_processor($validator)], 403);
        }

        $data = Tax::where('is_active', 1)->select('id', 'name', 'tax_rate')->latest()->paginate($request->limit ?? 50, ['*'], 'page', $request->offset ?? 1);
        return response()->json($data->items(), 200);
    }

    public function getCalculateTax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'totalProductAmount' => 'required|numeric',
            'productIds' => 'required',
            'categoryIds' => 'required',
            'quantity' => 'required',
            'additionalCharges' => 'nullable',
            'orderId' => 'nullable',
            'countryCode' => 'nullable',
            'taxPayer' => 'nullable',
            'addonIds' => 'nullable',
            'addonQuantity' => 'nullable',
            'addonCategoryIds' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $this->error_processor($validator)], 403);
        }

        $data = CalculateTaxService::getCalculatedTax(
            amount: $request->totalProductAmount,
            productIds: json_decode($request->productIds, true) ?? [],
            categoryIds: json_decode($request->categoryIds, true) ?? [],
            quantity: json_decode($request->quantity, true) ?? [],
            addonIds: json_decode($request->addonIds, true) ?? [],
            addonQuantity: json_decode($request->addonQuantity, true) ?? [],
            addonCategoryIds: json_decode($request->addonCategoryIds, true) ?? [],
            storeData: false,
            additionalCharges: json_decode($request?->additionalCharges, true) ?? [],
            taxPayer: $request->taxPayer ?? 'vendor',
            orderId: $request?->orderId,
            countryCode: $request?->countryCode
        );
        return response()->json($data);
    }


    private function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => translate($error[0])]);
        }
        return $err_keeper;
    }
}
