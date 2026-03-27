<?php

namespace Modules\AI\app\Services\Products\Response;

use Modules\AI\app\Services\Products\Resource\ProductResource;
use Modules\AI\app\Traits\ConversationTrait;

class ProductResponse
{
    use ConversationTrait;
    protected ProductResource $ProductResource;
    public function __construct()
    {
        $this->ProductResource = new ProductResource();
    }

    public function titleAutoFill(string $result)
    {
        $response["data"]["title"] = $result;
        return response()->json($response);
    }
    public function discriptionAutoFill(string $result)
    {
        $response["data"]["description"] = $result;
        return response()->json($response);
    }

    public function productGeneralSetupAutoFill(string $result, $storeId, $moduleType = null)
    {
        $resource = $this->ProductResource->productGeneralSetupData($storeId, $moduleType);

        $data = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }

        if (empty($data['category_name']) || !is_string($data['category_name'])) {
            throw new \InvalidArgumentException('The "category_name" field is required and must be a non-empty string.');
        }

        $processedData = self::productGeneralSetconvertNamesToIds($data, $resource);
        if (!$processedData['success']) {
            return $processedData;
        }
        $data = $processedData['data'];

        $fields = [
            'sub_category_name',
            'addon',
            'addonsNames',
            'nutrition',
            'allergy',
            'product_type',
            'search_tags'
        ];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                $data[$field] = null;
            }
        }


        $response['data'] = $data;
        return  $response;
    }

    public function productPriceOthersAutoFill($result)
    {
        $response = [];
        $data = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        $fields = [
            'unit_price',
            'minimum_order_quantity',
            'discount_amount',
        ];

        $errors = [];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                $errors[$field] = "$field is required.";
            }
        }

        if (!empty($errors)) {
            return response()->json(
                $this->formatAIGenerationValidationErrors($errors),
                422
            );
        }

        $response['data'] = $data;
        return response()->json($response);
    }
    public function productPriceOthersAutoFillApi($result)
    {
        $data = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        $fields = [
            'unit_price',
            'minimum_order_quantity',
            'discount_amount',
        ];

        $errors = [];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                $errors[$field] = "$field is required.";
            }
        }

        if (!empty($errors)) {
            return  $this->formatAIGenerationValidationErrors($errors);
        }
        return $data;
    }
    public function productseoAutoFill($result)
    {
        $response = [];
        $data = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }

        $fields = [
            'meta_title',
            'meta_description',
            'meta_index',
            'meta_no_follow',
            'meta_no_image_index',
            'meta_no_archive',
            'meta_no_snippet',
            'meta_max_snippet',
            'meta_max_snippet_value',
            'meta_max_video_preview',
            'meta_max_video_preview_value',
            'meta_max_image_preview',
            'meta_max_image_preview_value',
        ];

        $errors = [];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                $errors[$field] = "$field is required.";
            }
        }

        if (!empty($errors)) {
            return response()->json(
                $this->formatAIGenerationValidationErrors($errors),
                422
            );
        }

        $response['data'] = $data;
        return response()->json($response);
    }
    public function productseoAutoFillApi($result)
    {
        $data = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        $fields = [
            'meta_title',
            'meta_description',
            'meta_index',
            'meta_no_follow',
            'meta_no_image_index',
            'meta_no_archive',
            'meta_no_snippet',
            'meta_max_snippet',
            'meta_max_snippet_value',
            'meta_max_video_preview',
            'meta_max_video_preview_value',
            'meta_max_image_preview',
            'meta_max_image_preview_value',
        ];
        $errors = [];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                $errors[$field] = "$field is required.";
            }
        }

        if (!empty($errors)) {
            return  $this->formatAIGenerationValidationErrors($errors);
        }
        return $data;
    }

    public function variationSetupAutoFill(string $result)
    {
        $result = preg_replace('/```[a-z]*\n?|\n?```/', '', trim($result));
        $data = json_decode($result, true);
        $response = [
            'data' => $data,
        ];

        if (!empty($errors)) {
            return response()->json(
                $this->formatAIGenerationValidationErrors($errors),
                422
            );
        }

        $response['status'] = 'success';
        return response()->json($response);
    }

    public function analyzeImageAutoFill(string $result)
    {
        $response["data"]["title"] = $result;
        return response()->json($response);
    }
    public function generateTitleSuggestions(string $result)
    {
        $response["data"] = json_decode($result, true);
        return response()->json($response);
    }
}
