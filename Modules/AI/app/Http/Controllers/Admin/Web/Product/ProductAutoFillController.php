<?php

namespace Modules\AI\app\Http\Controllers\Admin\Web\Product;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\StoreConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\AI\app\Services\Products\Action\ProductAutoFillService;
use Modules\AI\app\Services\Products\Response\ProductResponse;
use Illuminate\Support\Facades\Validator;
use Modules\AI\app\Traits\ConversationTrait;

class ProductAutoFillController extends Controller
{

    use ConversationTrait;
    public function __construct(
        private  ProductAutoFillService $productAutoFillService,
        private ProductResponse $productResponse,
    ) {
        if (env('APP_MODE') == 'demo') {
            $ip = request()->header('x-forwarded-for');
            $cacheKey = "restricted_ip_" . $ip;

            $hits = Cache::store('file')->get($cacheKey, 0);

            if ($hits >= 10) {
                abort(403, translate('Demo Mode Restriction: This feature can only be accessed 10 times in demo mode. Further attempts are disabled to maintain a fair demo experience.'));
            }

            Cache::store('file')->forever($cacheKey, $hits + 1);
        }
    }

    public function titleAutoFill(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string',
            'langCode' => 'nullable|string|max:20',
        ], [
            'name.required' => translate('Please provide a product name so the AI can generate a suitable title or description'),
            'name.max' => translate('The product name may not exceed 255 characters.'),
        ]);
        // Helpers::get_business_settings('image_upload_limit_for_ai');
        $increment = false;
        if ($request->requestType == 'vendor' && $request->store_id) {
            $StoreConfig = StoreConfig::firstOrNew(['store_id' => $request->store_id]);

            $section_wise_ai_limit =  Helpers::get_business_settings('section_wise_ai_limit');
            if (($section_wise_ai_limit == 0 || $section_wise_ai_limit == null) || ($section_wise_ai_limit <=  $StoreConfig?->section_wise_ai_use_count)) {
                abort(403, translate('You have reached the limit of AI usage.'));
            }
            $increment = true;
        }


        $result = $this->productAutoFillService->titleAutoFill(
            $request->name,
            $request->langCode,
            $request->module_type
        );

        if ($increment == true) {
            if ($StoreConfig->exists) {
                $StoreConfig->increment('section_wise_ai_use_count');
            } else {
                $StoreConfig->section_wise_ai_use_count = 1;
                $StoreConfig->save();
            }
        }

        return $this->productResponse->titleAutoFill($result);
    }
    public function descriptionAutoFill(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'langCode' => 'nullable|string|max:20',
        ], [
            'name.required' => translate('Please provide a product name so the AI can generate a suitable title or description'),
            'name.max' => translate('The product name may not exceed 255 characters.'),
        ]);

        $increment = false;
        if ($request->requestType == 'vendor' && $request->store_id) {
                        $StoreConfig = StoreConfig::firstOrNew(['store_id' => $request->store_id]);

            $section_wise_ai_limit =  Helpers::get_business_settings('section_wise_ai_limit');
            if (($section_wise_ai_limit == 0 || $section_wise_ai_limit == null) || ($section_wise_ai_limit <=  $StoreConfig?->section_wise_ai_use_count)) {
                abort(403, translate('You have reached the limit of AI usage.'));
            }
            $increment = true;
        }

        $result = $this->productAutoFillService->descriptionAutoFill(
            $request->name,
            $request->langCode,
            $request->module_type
        );

        if ($increment == true) {
            if ($StoreConfig->exists) {
                $StoreConfig->increment('section_wise_ai_use_count');
            } else {
                $StoreConfig->section_wise_ai_use_count = 1;
                $StoreConfig->save();
            }
        }


        return $this->productResponse->discriptionAutoFill($result);
    }
    public function GeneralSetupAutoFill(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ], [
            'name.required' => translate('Please provide a default name so the AI can generate a Data.'),
            'name.max' => translate('The product name may not exceed 255 characters.'),
            'description.required' => translate('Please provide a default description so the AI can generate a Data.'),
        ]);

        $increment = false;
        if ($request->requestType == 'vendor' && $request->store_id) {
                        $StoreConfig = StoreConfig::firstOrNew(['store_id' => $request->store_id]);

            $section_wise_ai_limit =  Helpers::get_business_settings('section_wise_ai_limit');
            if (($section_wise_ai_limit == 0 || $section_wise_ai_limit == null) || ($section_wise_ai_limit <=  $StoreConfig?->section_wise_ai_use_count)) {
                abort(403, translate('You have reached the limit of AI usage.'));
            }
            $increment = true;
        }

        $result = $this->productAutoFillService->generalSetupAutoFill(
            $request->name,
            $request->description,
            $request->store_id,
            $request->module_type
        );

        if ($increment == true) {
             if ($StoreConfig->exists) {
                $StoreConfig->increment('section_wise_ai_use_count');
            } else {
                $StoreConfig->section_wise_ai_use_count = 1;
                $StoreConfig->save();
            }
        }


        return $this->productResponse->productGeneralSetupAutoFill($result, $request->store_id, $request->module_type);
    }

    public function PriceOthersAutoFill(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ],
            [
            'name.required' => translate('Please provide a default name so the AI can generate a Data.'),
            'name.max' => translate('The product name may not exceed 255 characters.'),
            'description' => translate('Please provide a default description so the AI can generate a Data.'),
            ]);

        $increment = false;
        if ($request->requestType == 'vendor' && $request->store_id) {
                        $StoreConfig = StoreConfig::firstOrNew(['store_id' => $request->store_id]);

            $section_wise_ai_limit =  Helpers::get_business_settings('section_wise_ai_limit');
            if (($section_wise_ai_limit == 0 || $section_wise_ai_limit == null) || ($section_wise_ai_limit <=  $StoreConfig?->section_wise_ai_use_count)) {
                abort(403, translate('You have reached the limit of AI usage.'));
            }
            $increment = true;
        }

        $result = $this->productAutoFillService->PriceOthersAutoFill(
            $request->name,
            $request->description,
        );


        if ($increment == true) {
             if ($StoreConfig->exists) {
                $StoreConfig->increment('section_wise_ai_use_count');
            } else {
                $StoreConfig->section_wise_ai_use_count = 1;
                $StoreConfig->save();
            }
        }

        return $this->productResponse->productPriceOthersAutoFill($result);
    }

    public function seoSectionAutoFill(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $increment = false;
        if ($request->requestType == 'vendor' && $request->store_id) {
                        $StoreConfig = StoreConfig::firstOrNew(['store_id' => $request->store_id]);

            $section_wise_ai_limit =  Helpers::get_business_settings('section_wise_ai_limit');
            if (($section_wise_ai_limit == 0 || $section_wise_ai_limit == null) || ($section_wise_ai_limit <=  $StoreConfig?->section_wise_ai_use_count)) {
                abort(403, translate('You have reached the limit of AI usage.'));
            }
            $increment = true;
        }
        $result = $this->productAutoFillService->seoSectionAutoFill(
            $request->name,
            $request->description,
        );


        if ($increment == true) {
             if ($StoreConfig->exists) {
                $StoreConfig->increment('section_wise_ai_use_count');
            } else {
                $StoreConfig->section_wise_ai_use_count = 1;
                $StoreConfig->save();
            }
        }

        return $this->productResponse->productseoAutoFill($result);
    }


    public function variationSetupAutoFill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'required',
        ],[
            'name.required' => translate('Please provide a default name so the AI can generate a Data.'),
            'name.max' => translate('The product name may not exceed 255 characters.'),
            'description.required' => translate('Please provide a default description so the AI can generate a Data.'),
        ]);
        $increment = false;
        if ($request->requestType == 'vendor' && $request->store_id) {
                        $StoreConfig = StoreConfig::firstOrNew(['store_id' => $request->store_id]);

            $section_wise_ai_limit =  Helpers::get_business_settings('section_wise_ai_limit');
            if (($section_wise_ai_limit == 0 || $section_wise_ai_limit == null) || ($section_wise_ai_limit <=  $StoreConfig?->section_wise_ai_use_count)) {
                abort(403, translate('You have reached the limit of AI usage.'));
            }
            $increment = true;
        }

        $description = $request->input('description');
        $this->descriptionEmptyValidation($description, $validator);
        if ($validator->fails()) {
            return response()->json(
                $this->inputValidationErrors($validator->errors()->toArray()),
                422
            );
        }


        $result = $this->productAutoFillService->variationSetupAutoFill(
            $request->name,
            $request->description,
        );

        if ($increment == true) {
             if ($StoreConfig->exists) {
                $StoreConfig->increment('section_wise_ai_use_count');
            } else {
                $StoreConfig->section_wise_ai_use_count = 1;
                $StoreConfig->save();
            }
        }
        return $this->productResponse->variationSetupAutoFill($result);
    }
    public function analyzeImageAutoFill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:1024',
        ], [
            'image.required' => translate('Image is required for analysis.'),
            'image.image' => translate('The uploaded file must be an image.'),
            'image.mimes' => translate('Only JPEG, PNG, JPG, and GIF images are allowed.'),
            'image.max' => translate('Image size must not exceed 1MB.'),
        ]);
        if ($validator->fails()) {
            return response()->json(
                $this->inputValidationErrors($validator->errors()->toArray()),
                422
            );
        }

        $increment = false;
        if ($request->requestType == 'image' && $request->store_id) {
                        $StoreConfig = StoreConfig::firstOrNew(['store_id' => $request->store_id]);

            $image_upload_limit_for_ai =  Helpers::get_business_settings('image_upload_limit_for_ai');
            if (($image_upload_limit_for_ai == 0 || $image_upload_limit_for_ai == null) || ($image_upload_limit_for_ai <=  $StoreConfig?->image_wise_ai_use_count)) {
                abort(403, translate('You have reached the limit of AI usage via Image.'));
            }
            $increment = true;
        }

        if ($increment == true) {
             if ($StoreConfig->exists) {
                $StoreConfig->increment('section_wise_ai_use_count');
            } else {
                $StoreConfig->section_wise_ai_use_count = 1;
                $StoreConfig->save();
            }
        }


        $extension = $request->image->getClientOriginalExtension();
        $imageName = Helpers::upload(dir: 'product/ai_product_image', format: $extension, image: $request->image);

        $imageUrl = $this->ai_product_image_full_path($imageName);

        // dd($imageUrl);
        // this is for the local development purpose start

    //    $imageUrl = "https://powermaccenter.com/cdn/shop/files/iPhone_16_Pink_PDP_Image_Position_1__en-WW.jpg";

        // this is for the local development purpose end

        $result = $this->productAutoFillService->imageAnalysisAutoFill(
            imageUrl: $imageUrl,
        );

        Helpers::check_and_delete(dir: 'product/ai_product_image/', old_image: $imageName);

        return $this->productResponse->analyzeImageAutoFill($result);
    }

    public function generateTitleSuggestions(Request $request)
    {
        $validated = $request->validate([
            'keywords' => 'required|string|max:255',
        ]);
        $increment = false;
        if ($request->requestType == 'vendor' && $request->store_id) {
                        $StoreConfig = StoreConfig::firstOrNew(['store_id' => $request->store_id]);

            $section_wise_ai_limit =  Helpers::get_business_settings('section_wise_ai_limit');
            if (($section_wise_ai_limit == 0 || $section_wise_ai_limit == null) || ($section_wise_ai_limit <=  $StoreConfig?->section_wise_ai_use_count)) {
                abort(403, translate('You have reached the limit of AI usage.'));
            }
            $increment = true;
        }
        $keywords = array_map('trim', explode(',', $request->keywords));
        $result = $this->productAutoFillService->generateTitleSuggestions($keywords);


        if ($increment == true) {
             if ($StoreConfig->exists) {
                $StoreConfig->increment('section_wise_ai_use_count');
            } else {
                $StoreConfig->section_wise_ai_use_count = 1;
                $StoreConfig->save();
            }
        }

        return $this->productResponse->generateTitleSuggestions($result);
    }


    public function getOtherVariationData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        $increment_image = false;
        $increment = false;
        if ($request->store_id) {
                        $StoreConfig = StoreConfig::firstOrNew(['store_id' => $request->store_id]);

            if ($request->requestType == 'image') {
                $image_upload_limit_for_ai =  Helpers::get_business_settings('image_upload_limit_for_ai');
                 if (($image_upload_limit_for_ai == 0 || $image_upload_limit_for_ai == null) || ($image_upload_limit_for_ai <=  $StoreConfig?->image_wise_ai_use_count)) {

                    return response()->json([
                        'errors' => [
                            ['code' => 'order', 'message' => translate('You have reached the limit of AI usage via Image.')]
                        ]
                    ], 403);
                }
                $increment_image = true;
            } else {
                $section_wise_ai_limit =  Helpers::get_business_settings('section_wise_ai_limit');
                if (($section_wise_ai_limit == 0 || $section_wise_ai_limit == null) || ($section_wise_ai_limit <=  $StoreConfig?->section_wise_ai_use_count)) {

                    return response()->json([
                        'errors' => [
                            ['code' => 'order', 'message' => translate('You have reached the limit of AI usage.')]
                        ]
                    ], 403);
                }
                $increment = true;
            }
        }


        $description = $request->input('description');
        $this->descriptionEmptyValidation($description, $validator);
        if ($validator->fails()) {
            return response()->json(
                $this->inputValidationErrors($validator->errors()->toArray()),
                422
            );
        }


        $result = $this->productAutoFillService->otherVariationSetupAutoFill(
            $request->name ,
            $request->description,
            $moduleType?? null
        );

        if ($increment == true) {
             if ($StoreConfig->exists) {
                $StoreConfig->increment('section_wise_ai_use_count');
            } else {
                $StoreConfig->section_wise_ai_use_count = 1;
                $StoreConfig->save();
            }
        } elseif ($increment_image == true) {
            if ($StoreConfig->exists) {
                $StoreConfig->increment('image_wise_ai_use_count');
            } else {
                $StoreConfig->image_wise_ai_use_count = 1;
                $StoreConfig->save();
            }
        }


        return $this->productResponse->variationSetupAutoFill($result);
    }
}
