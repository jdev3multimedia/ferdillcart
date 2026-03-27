<?php

namespace Modules\TaxModule\Services;


use Illuminate\Support\Facades\DB;
use Modules\TaxModule\Entities\OrderTax;
use Modules\TaxModule\Entities\SystemTaxSetup;
use Modules\TaxModule\Entities\Taxable;
use Modules\TaxModule\Entities\Tax;
use Modules\TaxModule\Traits\VatTaxConfiguration;

class CalculateTaxService
{
    use VatTaxConfiguration;
    public static function getCalculatedTax(
        float $amount,
        array $productIds,
        string $taxPayer = 'vendor',
        bool $storeData = false,
        array $additionalCharges = [],
        array $addonIds = [],
        $orderId = null,
        $countryCode = null,
        $storeId = null,
    ) {
        $systemTaxVat = SystemTaxSetup::with('additionalData')
            ->when($countryCode, fn($query) => $query->where('country_code', $countryCode))
            ->where('tax_payer', $taxPayer)
            ->where('is_active', 1)
            ->first();

        if (!$systemTaxVat || !$systemTaxVat->is_active) {
            return self::emptyTaxResult();
        }

        if ($systemTaxVat->is_included) {
            return array_merge(self::emptyTaxResult(), ['include' => 1]);
        }

        try {

            $taxType = $systemTaxVat->tax_type;
            $totalTaxamount = 0;
            $orderTaxIds = [];

            $additionalDatas = self::processAdditionalCharges(
                $systemTaxVat,
                $additionalCharges,
                $taxPayer,
                $storeData,
                $orderId,
                $countryCode,
                $totalTaxamount,
                $orderTaxIds,
                $storeId
            );

            $productWiseData = [];
            $addonWiseData = [];

            if (in_array($taxType, ['product_wise', 'category_wise'])) {
                [$productWiseData, $addonWiseData] = self::processProductAndAddonTaxes(
                    taxType: $taxType,
                    systemTaxVat: $systemTaxVat,
                    productIds: $productIds,
                    addonIds: $addonIds,
                    taxPayer: $taxPayer,
                    storeData: $storeData,
                    orderId: $orderId,
                    countryCode: $countryCode,
                    totalTaxamount: $totalTaxamount,
                    orderTaxIds: $orderTaxIds,
                    storeId: $storeId
                );
            } else {
                $orderWiseData = self::calculateTax(
                    systemTaxVat: $systemTaxVat,
                    amount: $amount,
                    taxIds: $systemTaxVat->tax_ids,
                    taxPayer: $taxPayer,
                    storeData: $storeData,
                    orderId: $orderId,
                    countryCode: $countryCode,
                    storeId: $storeId
                );
                $orderWiseData['totalTaxamount'] += $totalTaxamount;
                $orderWiseData['productWiseData'] = self::getProductwiseData($productIds, $systemTaxVat->is_included, $orderWiseData['totalTaxPercent']);
                $orderWiseData['taxType'] = $taxType;
                $orderWiseData['additionalDatas'] = $additionalDatas;
                $orderWiseData['addonWiseData'] = $addonWiseData;
                $orderWiseData['orderTaxIds'] = array_merge($orderTaxIds, $orderWiseData['orderTaxIds']);

                return $orderWiseData;
            }

            return [
                'include' => $systemTaxVat->is_included,
                'totalTaxPercent' => 0,
                'totalTaxamount' => $totalTaxamount,
                'taxType' => $taxType,
                'productWiseData' => $productWiseData,
                'additionalDatas' => $additionalDatas,
                'addonWiseData' => $addonWiseData,
                'orderTaxIds' => $orderTaxIds,
            ];
        } catch (\Throwable $th) {
            if ($storeData) {
                DB::rollBack();
            }
            return array_merge(self::emptyTaxResult(), [
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ]);
        }
    }

    private static function emptyTaxResult(): array
    {
        return ['include' => null, 'totalTaxPercent' => 0, 'totalTaxamount' => 0];
    }

    private static function processAdditionalCharges($systemTaxVat, $additionalCharges, $taxPayer, $storeData, $orderId, $countryCode, &$totalTaxamount, &$orderTaxIds, $storeId): array
    {
        $results = [];

        $availableAdditions = $systemTaxVat->additionalData()->where('is_active', 1)->select('name', 'tax_ids')->get();

        foreach ($availableAdditions as $additionalData) {
            $chargeName = $additionalData->name;
            if (isset($additionalCharges[$chargeName])) {
                $taxOnAdd = self::calculateTax(
                    systemTaxVat: $systemTaxVat,
                    amount: $additionalCharges[$chargeName],
                    taxIds: $additionalData->tax_ids,
                    taxPayer: $taxPayer,
                    storeData: $storeData,
                    orderId: $orderId,
                    countryCode: $countryCode,
                    tax_on: $chargeName,
                    storeId: $storeId
                );

                $taxOnAdd['additionalData'] = $chargeName;
                $results[] = $taxOnAdd;
                $totalTaxamount += $taxOnAdd['totalTaxamount'];
                $orderTaxIds = array_merge($orderTaxIds, $taxOnAdd['orderTaxIds']);
            }
        }

        return $results;
    }

    private static function processProductAndAddonTaxes(
        $taxType,
        $systemTaxVat,
        $productIds,
        $addonIds,
        $taxPayer,
        $storeData,
        $orderId,
        $countryCode,
        &$totalTaxamount,
        &$orderTaxIds,
        $storeId
    ): array {
        $productWiseData = [];
        $addonWiseData = [];

        if ($systemTaxVat?->tax_payer == 'parcel') {
            $dataType = self::getClassNames('parcel_category');
        }  else {
            $dataType = self::getClassNames($taxType === 'product_wise' ? 'product' : 'category');
        }

        foreach ($productIds as $product) {

            if($product['is_campaign_item'] == true ){
                $dataType = self::getClassNames($taxType === 'product_wise' ? 'campaign_product' : 'category');
            }
            $dataId = $taxType === 'product_wise' ? $product['id'] : $product['category_id'];
            $taxVatIds = Taxable::where('taxable_type', $dataType)
                ->where('taxable_id', $dataId)
                ->where('system_tax_setup_id', $systemTaxVat->id)
                ->pluck('tax_id')
                ->toArray();

            $taxData = self::calculateTax(
                systemTaxVat: $systemTaxVat,
                amount: $product['after_discount_final_price'],
                taxIds: $taxVatIds,
                taxPayer: $taxPayer,
                storeData: $storeData,
                orderId: $orderId,
                countryCode: $countryCode,
                data_id: $dataId,
                data_type: $dataType,
                quantity: $product['quantity'],
                storeId: $storeId
            );
            $taxData['product_id'] = $product['id'];
            $productWiseData[] = $taxData;
            $totalTaxamount += $taxData['totalTaxamount'];
            $orderTaxIds = array_merge($orderTaxIds, $taxData['orderTaxIds']);
        }

        if (!empty($addonIds)) {
            $addonDataType = self::getClassNames($taxType === 'product_wise' ? 'addon' : 'addon_category');

            foreach ($addonIds as $addon) {

                $addonDataId = $taxType === 'product_wise' ? $addon['addon_id'] : $addon['category_id'];
                $addonTaxVatIds = Taxable::where('taxable_type', $addonDataType)
                    ->where('taxable_id', $addonDataId)
                    ->where('system_tax_setup_id', $systemTaxVat->id)
                    ->pluck('tax_id')
                    ->toArray();

                $addonTaxData = self::calculateTax(
                    systemTaxVat: $systemTaxVat,
                    amount: $addon['after_discount_final_price'],
                    taxIds: $addonTaxVatIds,
                    taxPayer: $taxPayer,
                    storeData: $storeData,
                    orderId: $orderId,
                    countryCode: $countryCode,
                    data_id: $addonDataId,
                    data_type: $addonDataType,
                    quantity: $addon['quantity'],
                    storeId: $storeId
                );

                $addonTaxData['addon_id'] = $addon['addon_id'];
                $addonWiseData[] = $addonTaxData;
                $totalTaxamount += $addonTaxData['totalTaxamount'];
                $orderTaxIds = array_merge($orderTaxIds, $addonTaxData['orderTaxIds']);
            }
        }

        return [$productWiseData, $addonWiseData];
    }




    protected static function calculateTax($systemTaxVat, $amount, $taxIds, $taxPayer = 'vendor', $tax_on = 'basic', $quantity = 1, $storeId = null, $storeData = null, $orderId = null, $countryCode = null, $data_id = null, $data_type = null)
    {
        $taxRatePercent = Tax::whereIn('id', $taxIds)->where('is_active', 1)->select('id', 'name', 'tax_rate')->get();
        $totalTaxPercent = 0;
        $totalTaxamount = 0;
        $orderTaxIds = [];
        foreach ($taxRatePercent as $taxRate) {
            $taxData = self::getTaxAmount(amount: $amount, taxRatePercent: $taxRate->tax_rate, isInclude: $systemTaxVat->is_included);
            $totalTaxPercent += $taxRate->tax_rate;
            $taxAmount = $taxData['taxAmount'];
            $totalTaxamount += $taxAmount;

            if ($storeData) {
                $orderTaxData = new OrderTax();
                $orderTaxData->tax_name = $taxRate->name;
                $orderTaxData->tax_type = $systemTaxVat->tax_type;
                $orderTaxData->tax_on = $tax_on;
                $orderTaxData->tax_rate = $taxRate->tax_rate;
                $orderTaxData->tax_amount = $taxAmount;
                $orderTaxData->before_tax_amount = $taxData['originalAmount'];
                $orderTaxData->after_tax_amount = $taxData['totalAmount'];
                $orderTaxData->tax_payer = $taxPayer;
                $orderTaxData->country_code = $countryCode;
                $orderTaxData->order_id = $orderId;
                $orderTaxData->order_type = self::getClassNames($taxPayer == 'rental_provider' ?  'trip' : 'order');
                $orderTaxData->tax_id = $taxRate->id;
                $orderTaxData->system_tax_setup_id = $systemTaxVat->id;
                $orderTaxData->taxable_id = $data_id;
                $orderTaxData->taxable_type = $data_type;
                $orderTaxData->store_id = $storeId;
                $orderTaxData->store_type = self::getClassNames('store');
                $orderTaxData->quantity = $quantity;
                $orderTaxData->save();
                $orderTaxIds[] = $orderTaxData->id;
            }
        }



        return  ['include' => $systemTaxVat?->is_included, 'totalTaxPercent' => $totalTaxPercent, 'totalTaxamount' => $totalTaxamount, 'orderTaxIds' => $orderTaxIds];
    }

    protected static function getTaxAmount($amount, $taxRatePercent, $isInclude = false)
    {
        if ($amount > 0 && $taxRatePercent > 0) {
            $taxAmount = ($amount * $taxRatePercent) / (100 + ($isInclude ? $taxRatePercent : 0));
            $totalAmount = $isInclude ? ($amount - $taxAmount) : ($amount + $taxAmount);
            return ['taxAmount' => $taxAmount, 'originalAmount' => $amount, 'totalAmount' => $totalAmount];
        }

        return ['taxAmount' => 0, 'originalAmount' => $amount, 'totalAmount' => $amount, 'taxRatePercent' => $taxRatePercent];
    }

    public static function updateOrderTaxData($orderId, array $orderTaxIds)
    {
        if (count($orderTaxIds) > 0) {
            OrderTax::whereIn('id', $orderTaxIds)->update(['order_id' => $orderId]);
            return true;
        }
        return false;
    }


    public static function getProductwiseData(array $productIds, $isInclude, $totalTaxPercent)
    {
        $result = [];
        foreach ($productIds as $product) {
            $result[] = [
                'include'         => $isInclude,
                'totalTaxPercent' => $totalTaxPercent,
                'totalTaxamount'  => self::getTaxAmount($product['after_discount_final_price'], $totalTaxPercent, $isInclude)['taxAmount'],
                'orderTaxIds'     => [],
                'product_id'      => $product['id'],
            ];
        }

        return $result;
    }
}
