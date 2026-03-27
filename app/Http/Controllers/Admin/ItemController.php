<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Exports\ItemListExport;
use App\Exports\ItemReviewExport;
use App\Exports\StoreItemExport;
use App\Http\Controllers\Controller;
use App\Models\Allergy;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CommonCondition;
use App\Models\EcommerceItemDetails;
use App\Models\GenericName;
use App\Models\Item;
use App\Models\ItemCampaign;
use App\Models\ItemSeoData;
use App\Models\Nutrition;
use App\Models\PharmacyItemDetails;
use App\Models\Review;
use App\Models\Store;
use App\Models\Tag;
use App\Models\TempProduct;
use App\Models\Translation;
use App\Scopes\StoreScope;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where(['position' => 0])->get();

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];

        return view('admin-views.product.index', compact('categories', 'productWiseTax', 'taxVats'));
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name.0' => 'required',
            'name.*' => 'max:191',
            'category_id' => 'required',
            'image' => [
                Rule::requiredIf(function () use ($request) {
                    return Config::get('module.current_module_type') != 'food' && $request?->product_gellary == null;
                }),
            ],
            'price' => 'required|numeric|between:.01,999999999999.99',
            'discount' => 'required|numeric|min:0',
            'store_id' => 'required',
            'description.*' => 'max:1000',
            'name.0' => 'required',
            'description.0' => 'required',
        ], [
            'description.*.max' => translate('messages.description_length_warning'),
            'name.0.required' => translate('messages.item_name_required'),
            'category_id.required' => translate('messages.category_required'),
            'image.required' => translate('messages.thumbnail image is required'),
            'name.0.required' => translate('default_name_is_required'),
            'description.0.required' => translate('default_description_is_required'),
        ]);
        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('Discount amount must be less than 100% or unit price'));
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $images = [];

        if ($request->item_id && $request?->product_gellary == 1) {
            $item_data = Item::withoutGlobalScope(StoreScope::class)->findOrfail($request->item_id);
            if (! $request->has('image')) {

                $oldDisk = 'public';
                if ($item_data->storage && count($item_data->storage) > 0) {
                    foreach ($item_data->storage as $value) {
                        if ($value['key'] == 'image') {
                            $oldDisk = $value['value'];
                        }
                    }
                }
                $oldPath = "product/{$item_data->image}";
                $newFileNamethumb = Carbon::now()->toDateString().'-'.uniqid().'.png';
                $newPath = "product/{$newFileNamethumb}";
                $dir = 'product/';
                $newDisk = Helpers::getDisk();

                try {
                    if ($newDisk == 's3' && $item_data->image) {
                        Storage::disk($newDisk)->put($newPath, Storage::disk($oldDisk)->get($oldPath));
                    } else {
                        if (Storage::disk($oldDisk)->exists($oldPath)) {
                            if (! Storage::disk($newDisk)->exists($dir)) {
                                Storage::disk($newDisk)->makeDirectory($dir);
                            }
                            $fileContents = Storage::disk($oldDisk)->get($oldPath);
                            Storage::disk($newDisk)->put($newPath, $fileContents);
                        }
                    }
                } catch (\Exception $e) {
                }
            }
            foreach ($item_data->images as $key => $value) {
                if (! in_array(is_array($value) ? $value['img'] : $value, explode(',', $request->removedImageKeys))) {
                    $value = is_array($value) ? $value : ['img' => $value, 'storage' => 'public'];
                    $oldDisk = $value['storage'];
                    $oldPath = "product/{$value['img']}";
                    $newFileName = Carbon::now()->toDateString().'-'.uniqid().'.png';
                    $newPath = "product/{$newFileName}";
                    $dir = 'product/';
                    $newDisk = Helpers::getDisk();
                    try {
                        if ($newDisk == 's3') {
                            Storage::disk($newDisk)->put($newPath, Storage::disk($oldDisk)->get($oldPath));
                        } else {
                            if (Storage::disk($oldDisk)->exists($oldPath)) {
                                if (! Storage::disk($newDisk)->exists($dir)) {
                                    Storage::disk($newDisk)->makeDirectory($dir);
                                }
                                $fileContents = Storage::disk($oldDisk)->get($oldPath);
                                Storage::disk($newDisk)->put($newPath, $fileContents);
                            }
                        }

                    } catch (\Exception $e) {
                    }
                    $images[] = ['img' => $newFileName, 'storage' => Helpers::getDisk()];
                }
            }
        }

        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(',', $request->tags);
        }
        if (isset($tags)) {
            foreach ($tags as $key => $value) {
                $tag = Tag::firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                array_push($tag_ids, $tag->id);
            }
        }

        $nutrition_ids = [];
        if ($request->nutritions != null) {
            $nutritions = $request->nutritions;
        }
        if (isset($nutritions)) {
            foreach ($nutritions as $key => $value) {
                $nutrition = Nutrition::firstOrNew(
                    ['nutrition' => $value]
                );
                $nutrition->save();
                array_push($nutrition_ids, $nutrition->id);
            }
        }
        $generic_ids = [];
        if ($request->generic_name != null) {
            $generic_name = GenericName::firstOrNew(
                ['generic_name' => $request->generic_name]
            );
            $generic_name->save();
            array_push($generic_ids, $generic_name->id);
        }

        $allergy_ids = [];
        if ($request->allergies != null) {
            $allergies = $request->allergies;
        }
        if (isset($allergies)) {
            foreach ($allergies as $key => $value) {
                $allergy = Allergy::firstOrNew(
                    ['allergy' => $value]
                );
                $allergy->save();
                array_push($allergy_ids, $allergy->id);
            }
        }

        $item = new Item;
        $item->name = $request->name[array_search('default', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }
        $item->category_ids = json_encode($category);
        $item->category_id = $request->sub_category_id ? $request->sub_category_id : $request->category_id;
        $item->description = $request->description[array_search('default', $request->lang)];

        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_'.$no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp['name'] = 'choice_'.$no;
                $temp['title'] = $request->choice[$key];
                $temp['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $temp);
            }
        }
        $item->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_'.$no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        // Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $temp) {
                    if ($k > 0) {
                        $str .= '-'.str_replace(' ', '', $temp);
                    } else {
                        $str .= str_replace(' ', '', $temp);
                    }
                }
                $temp = [];
                $temp['type'] = $str;
                $temp['price'] = abs($request['price_'.str_replace('.', '_', $str)]);

                if ($request->discount_type == 'amount' && $temp['price'] < $request->discount) {
                    $validator->getMessageBag()->add('unit_price', translate('Variation price must be greater than discount amount'));

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }

                $temp['stock'] = abs($request['stock_'.str_replace('.', '_', $str)]);
                array_push($variations, $temp);
            }
        }
        // combinations end

        if (! empty($request->file('item_images'))) {
            foreach ($request->item_images as $img) {
                $image_name = Helpers::upload('product/', 'png', $img);
                $images[] = ['img' => $image_name, 'storage' => Helpers::getDisk()];
            }
        }
        // food variation
        $food_variations = [];
        if (isset($request->options)) {
            foreach (array_values($request->options) as $key => $option) {

                $temp_variation['name'] = $option['name'];
                $temp_variation['type'] = $option['type'];
                $temp_variation['min'] = $option['min'] ?? 0;
                $temp_variation['max'] = $option['max'] ?? 0;
                $temp_variation['required'] = $option['required'] ?? 'off';
                if ($option['min'] > 0 && $option['min'] > $option['max']) {
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (! isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for').$option['name']);

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for').$option['name']);

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp_value = [];

                foreach (array_values($option['values']) as $value) {
                    if (isset($value['label'])) {
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    array_push($temp_value, $temp_option);
                }
                $temp_variation['values'] = $temp_value;
                array_push($food_variations, $temp_variation);
            }
        }

        $item->food_variations = json_encode($food_variations);
        $item->variations = json_encode($variations);
        $item->price = $request->price;
        $item->image = $request->has('image') ? Helpers::upload('product/', 'png', $request->file('image')) : $newFileNamethumb ?? null;
        $item->available_time_starts = $request->available_time_starts ?? '00:00:00';
        $item->available_time_ends = $request->available_time_ends ?? '23:59:59';
        $item->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $item->discount_type = $request->discount_type;
        $item->unit_id = $request->unit;
        $item->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $item->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $item->store_id = $request->store_id;
        $item->maximum_cart_quantity = $request->maximum_cart_quantity;
        $item->veg = $request->veg ?? 0;
        $item->module_id = Config::get('module.current_module_id');
        $module_type = Config::get('module.current_module_type');
        if ($module_type == 'grocery') {
            $item->organic = $request->organic ?? 0;
        }
        $item->stock = $request->current_stock ?? 0;
        $item->images = $images;
        $item->is_halal = $request->is_halal ?? 0;
        $item->save();
        $item->tags()->sync($tag_ids);
        $item->nutritions()->sync($nutrition_ids);
        $item->allergies()->sync($allergy_ids);
        if ($module_type == 'pharmacy') {
            $item_details = new PharmacyItemDetails;
            $item_details->item_id = $item->id;
            $item_details->common_condition_id = $request->condition_id;
            $item_details->is_basic = $request->basic ?? 0;
            $item_details->is_prescription_required = $request->is_prescription_required ?? 0;
            $item_details->save();
            $item->generic()->sync($generic_ids);
        }
        if ($module_type == 'ecommerce') {
            $item_details = new EcommerceItemDetails;
            $item_details->item_id = $item->id;
            $item_details->brand_id = $request->brand_id;
            $item_details->save();
        }

        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'product_wise') {
                foreach ($request['tax_ids'] ?? [] as $tax_id) {
                    \Modules\TaxModule\Entities\Taxable::create(
                        [
                            'taxable_type' => Item::class,
                            'taxable_id' => $item->id,
                            'system_tax_setup_id' => $SystemTaxVat->id,
                            'tax_id' => $tax_id,
                        ],
                    );
                }
            }
        }

        Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'Item', data_id: $item->id, data_value: $item->name);
        Helpers::add_or_update_translations(request: $request, key_data: 'description', name_field: 'description', model_name: 'Item', data_id: $item->id, data_value: $item->description);
        if ($module_type == 'ecommerce') {
            $this->addOrUpdateMetaData($request, $item->id);
        }

        return response()->json(['success' => translate('messages.product_added_successfully')], 200);
    }

    public function view($id)
    {
        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $product = Item::withoutGlobalScope(StoreScope::class)->with($productWiseTax ? ['taxVats.tax'] : [])->where(['id' => $id])->firstOrFail();

        $reviews = Review::where(['item_id' => $id])->latest()->paginate(config('default_pagination'));

        return view('admin-views.product.view', compact('product', 'reviews', 'productWiseTax'));
    }

    public function edit(Request $request, $id)
    {
        $temp_product = false;
        if ($request->temp_product) {
            $product = TempProduct::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->with('store', 'category', 'module')->findOrFail($id);
            $temp_product = true;
        } else {
            $product = Item::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->with('store', 'category', 'module')->findOrFail($id);
        }
        if (! $product) {
            Toastr::error(translate('messages.item_not_found'));

            return back();
        }
        $temp = $product->category;
        if ($temp?->position) {
            $sub_category = $temp;
            $category = $temp->parent;
        } else {
            $category = $temp;
            $sub_category = null;
        }

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];
        $taxVatIds = $productWiseTax ? $product->taxVats()->pluck('tax_id')->toArray() : [];

        return view('admin-views.product.edit', compact('product', 'sub_category', 'category', 'temp_product', 'productWiseTax', 'taxVats', 'taxVatIds'));
    }

    public function status(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->findOrFail($request->id);
        $product->status = $request->status;
        $product->save();
        Toastr::success(translate('messages.item_status_updated'));

        return back();
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'array',
            'name.0' => 'required',
            'name.*' => 'max:191',
            'category_id' => 'required',
            'price' => 'required|numeric|between:.01,999999999999.99',
            'store_id' => 'required',
            'description' => 'array',
            'description.*' => 'max:1000',
            'discount' => 'required|numeric|min:0',
            'name.0' => 'required',
            'description.0' => 'required',
        ], [
            'description.*.max' => translate('messages.description_length_warning'),
            'category_id.required' => translate('messages.category_required'),
            'name.0.required' => translate('default_name_is_required'),
            'description.0.required' => translate('default_description_is_required'),
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('Discount amount must be less than 100% or unit price'));
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $item = Item::withoutGlobalScope(StoreScope::class)->find($id);
        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(',', $request->tags);
        }
        if (isset($tags)) {
            foreach ($tags as $key => $value) {
                $tag = Tag::firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                array_push($tag_ids, $tag->id);
            }
        }
        $nutrition_ids = [];
        if ($request->nutritions != null) {
            $nutritions = $request->nutritions;
        }
        if (isset($nutritions)) {
            foreach ($nutritions as $key => $value) {
                $nutrition = Nutrition::firstOrNew(
                    ['nutrition' => $value]
                );
                $nutrition->save();
                array_push($nutrition_ids, $nutrition->id);
            }
        }
        $allergy_ids = [];
        if ($request->allergies != null) {
            $allergies = $request->allergies;
        }
        if (isset($allergies)) {
            foreach ($allergies as $key => $value) {
                $allergy = Allergy::firstOrNew(
                    ['allergy' => $value]
                );
                $allergy->save();
                array_push($allergy_ids, $allergy->id);
            }
        }

        $generic_ids = [];
        if ($request->generic_name != null) {
            $generic_name = GenericName::firstOrNew(
                ['generic_name' => $request->generic_name]
            );
            $generic_name->save();
            array_push($generic_ids, $generic_name->id);
        }

        $item->name = $request->name[array_search('default', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }

        $images = $item['images'];
        if (! $request?->temp_product) {
            foreach ($item->images as $key => $value) {
                if (in_array(is_array($value) ? $value['img'] : $value, explode(',', $request->removedImageKeys))) {
                    $value = is_array($value) ? $value : ['img' => $value, 'storage' => 'public'];
                    Helpers::check_and_delete('product/', $value['img']);
                    unset($images[$key]);
                }
            }
            $images = array_values($images);
            if ($request->has('item_images')) {
                foreach ($request->item_images as $img) {
                    $image = Helpers::upload('product/', 'png', $img);
                    array_push($images, ['img' => $image, 'storage' => Helpers::getDisk()]);
                }
            }
        }

        $item->category_id = $request->sub_category_id ? $request->sub_category_id : $request->category_id;
        $item->category_ids = json_encode($category);
        $item->description = $request->description[array_search('default', $request->lang)];

        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_'.$no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp['name'] = 'choice_'.$no;
                $temp['title'] = $request->choice[$key];
                $temp['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $temp);
            }
        }
        $item->choice_options = $request->has('attribute_id') ? json_encode($choice_options) : json_encode([]);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_'.$no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        // Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $temp) {
                    if ($k > 0) {
                        $str .= '-'.str_replace(' ', '', $temp);
                    } else {
                        $str .= str_replace(' ', '', $temp);
                    }
                }
                $temp = [];
                $temp['type'] = $str;
                $temp['price'] = abs($request['price_'.str_replace('.', '_', $str)]);

                if ($request->discount_type == 'amount' && $temp['price'] < $request->discount) {
                    $validator->getMessageBag()->add('unit_price', translate('Variation price must be greater than discount amount'));

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp['stock'] = abs($request['stock_'.str_replace('.', '_', $str)]);
                array_push($variations, $temp);
            }
        }
        // combinations end

        $food_variations = [];
        if (isset($request->options)) {
            foreach (array_values($request->options) as $key => $option) {
                $temp_variation['name'] = $option['name'];
                $temp_variation['type'] = $option['type'];
                $temp_variation['min'] = $option['min'] ?? 0;
                $temp_variation['max'] = $option['max'] ?? 0;
                if ($option['min'] > 0 && $option['min'] > $option['max']) {
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (! isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for').$option['name']);

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for').$option['name']);

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp_variation['required'] = $option['required'] ?? 'off';
                $temp_value = [];
                foreach (array_values($option['values']) as $value) {
                    if (isset($value['label'])) {
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    array_push($temp_value, $temp_option);
                }
                $temp_variation['values'] = $temp_value;
                array_push($food_variations, $temp_variation);
            }
        }
        $slug = Str::slug($request->name[array_search('default', $request->lang)]);
        $item->slug = $item->slug ? $item->slug : "{$slug}{$item->id}";
        $item->food_variations = json_encode($food_variations);
        $item->variations = $request->has('attribute_id') ? json_encode($variations) : json_encode([]);
        $item->price = $request->price;
        $item->image = $request->has('image') ? Helpers::update('product/', $item->image, 'png', $request->file('image')) : $item->image;
        $item->available_time_starts = $request->available_time_starts ?? '00:00:00';
        $item->available_time_ends = $request->available_time_ends ?? '23:59:59';

        $item->discount = $request->discount;
        $item->discount_type = $request->discount_type;
        $item->unit_id = $request->unit;
        $item->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $item->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $item->store_id = $request->store_id;
        $item->maximum_cart_quantity = $request->maximum_cart_quantity;
        // $item->module_id= $request->module_id;
        $item->stock = $request->current_stock ?? 0;
        $item->is_halal = $request->is_halal ?? 0;
        $item->organic = $request->organic ?? 0;
        $item->veg = $request->veg ?? 0;
        $item->images = $images;
        if (Helpers::get_mail_status('product_approval') && $request?->temp_product) {

            $images = $item->temp_product?->images ?? [];

            if ($request->removedImageKeys) {
                foreach ($images as $key => $value) {
                    if (in_array(is_array($value) ? $value['img'] : $value, explode(',', $request->removedImageKeys))) {
                        unset($images[$key]);
                    }
                }
                $images = array_values($images);
            }

            foreach ($images as $k => $value) {
                $value = is_array($value) ? $value : ['img' => $value, 'storage' => 'public'];
                $oldDisk = $value['storage'];
                $oldPath = "product/{$value['img']}";
                $newFileName = Carbon::now()->toDateString().'-'.uniqid().'.png';
                $newPath = "product/{$newFileName}";
                $dir = 'product/';
                $newDisk = Helpers::getDisk();
                try {
                    if (Storage::disk($oldDisk)->exists($oldPath)) {
                        if (! Storage::disk($newDisk)->exists($dir)) {
                            Storage::disk($newDisk)->makeDirectory($dir);
                        }
                        $fileContents = Storage::disk($oldDisk)->get($oldPath);
                        Storage::disk($newDisk)->put($newPath, $fileContents);
                        unset($images[$k]);
                    }
                } catch (\Exception $e) {
                }
                $images[] = ['img' => $newFileName, 'storage' => Helpers::getDisk()];
            }

            $images = array_values($images);

            if ($request->has('item_images')) {
                foreach ($request->item_images as $img) {
                    $image = Helpers::upload('product/', 'png', $img);
                    array_push($images, ['img' => $image, 'storage' => Helpers::getDisk()]);
                }
            }

            $item->images = $images;

            $item->temp_product?->translations()->delete();
            $item?->pharmacy_item_details()?->delete();
            if ($item->module->module_type == 'pharmacy') {
                DB::table('pharmacy_item_details')->where('temp_product_id', $item->temp_product?->id)->update([
                    'item_id' => $item->id,
                    'temp_product_id' => null,
                ]);
            }
            $item->temp_product?->taxVats()->delete();
            if ($item->module->module_type == 'ecommerce') {
                $itemSeo = ItemSeoData::where('temp_item_id', $item->temp_product?->id)->first();
                if ($itemSeo) {
                    if ($item->seoData) {
                        $item->seoData->delete();
                    }
                    $itemSeo->item_id = $item->id;
                    $itemSeo->temp_item_id = null;
                    $itemSeo->save();
                }
            }
            $item->temp_product?->seoData()->delete();

            $item->temp_product?->delete();
            $item->is_approved = 1;
            try {

                if (Helpers::getNotificationStatusData('store', 'store_product_approve', 'push_notification_status', $item?->store->id) && $item?->store?->vendor?->firebase_token) {
                    $data = [
                        'title' => translate('product_approved'),
                        'description' => translate('Product_Request_Has_Been_Approved_By_Admin'),
                        'order_id' => '',
                        'image' => '',
                        'type' => 'product_approve',
                        'order_status' => '',
                    ];
                    Helpers::send_push_notif_to_device($item?->store?->vendor?->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $item?->store?->vendor_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if (config('mail.status') && Helpers::get_mail_status('product_approve_mail_status_store') == '1' &&  Helpers::getNotificationStatusData('store', 'store_product_approve', 'mail_status', $item?->store?->id)) {
                    Mail::to($item?->store?->vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorProductMail($item?->store?->name, 'approved'));
                }
            } catch (\Exception $e) {
                info($e->getMessage());
            }
        }
        $item->save();
        $item->tags()->sync($tag_ids);
        $item->nutritions()->sync($nutrition_ids);
        $item->allergies()->sync($allergy_ids);
        if ($item->module->module_type == 'pharmacy') {
            $item->generic()->sync($generic_ids);
            DB::table('pharmacy_item_details')
                ->updateOrInsert(
                    ['item_id' => $item->id],
                    [
                        'common_condition_id' => $request->condition_id,
                        'is_basic' => $request->basic ?? 0,
                        'is_prescription_required' => $request->is_prescription_required ?? 0,
                    ]
                );
        }
        if ($item->module->module_type == 'ecommerce') {
            DB::table('ecommerce_item_details')
                ->updateOrInsert(
                    ['item_id' => $item->id],
                    [
                        'brand_id' => $request->brand_id,
                    ]
                );
        }

        if (addon_published_status('TaxModule')) {
            $taxVatIds = $item->taxVats()->pluck('tax_id')->toArray() ?? [];
            $newTaxVatIds = array_map('intval', $request['tax_ids'] ?? []);
            sort($newTaxVatIds);
            sort($taxVatIds);
            if ($newTaxVatIds != $taxVatIds) {
                $item->taxVats()->delete();
                $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
                if ($SystemTaxVat?->tax_type == 'product_wise') {
                    foreach ($request['tax_ids'] ?? [] as $tax_id) {
                        \Modules\TaxModule\Entities\Taxable::create(
                            [
                                'taxable_type' => Item::class,
                                'taxable_id' => $item->id,
                                'system_tax_setup_id' => $SystemTaxVat->id,
                                'tax_id' => $tax_id,
                            ],
                        );
                    }
                }
            }
        }

        Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'Item', data_id: $item->id, data_value: $item->name);
        Helpers::add_or_update_translations(request: $request, key_data: 'description', name_field: 'description', model_name: 'Item', data_id: $item->id, data_value: $item->description);
        if ($item->module->module_type == 'ecommerce') {
            $this->addOrUpdateMetaData($request, $item->id);
        }

        return response()->json(['success' => translate('messages.product_updated_successfully')], 200);
    }

    public function delete(Request $request)
    {

        if ($request?->temp_product) {
            $product = TempProduct::withoutGlobalScope(StoreScope::class)->find($request->id);
        } else {
            $product = Item::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->find($request->id);
            $product?->temp_product?->translations()?->delete();
            $product?->temp_product()?->delete();
            $product?->carts()?->delete();
        }

        if ($product->image) {
            Helpers::check_and_delete('product/', $product['image']);
        }
        foreach ($product->images as $value) {
            $value = is_array($value) ? $value : ['img' => $value, 'storage' => 'public'];
            Helpers::check_and_delete('product/', $value['img']);
        }
        $product?->translations()->delete();
        $product?->taxVats()->delete();

        $product->delete();
        Toastr::success(translate('messages.product_deleted_successfully'));

        return back();
    }

    public function variant_combination(Request $request)
    {
        $options = [];
        $price = $request->price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_'.$no;
                $my_str = implode('', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $result = [[]];
        foreach ($options as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }

        $data = [];
        foreach ($result as $combination) {
            $str = '';
            foreach ($combination as $key => $item) {
                if ($key > 0) {
                    $str .= '-'.str_replace(' ', '', $item);
                } else {
                    $str .= str_replace(' ', '', $item);
                }
            }

            $price_field = 'price_'.$str;
            $stock_field = 'stock_'.$str;
            $item_price = $request->input($price_field);
            $item_stock = $request->input($stock_field);

            $data[] = [
                'name' => $str,
                'price' => $item_price ?? $price,
                'stock' => $item_stock ?? 1,
            ];
        }
        $combinations = $result;
        $stock = $request->stock == 'true' ? true : false;

        return response()->json([
            'view' => view('admin-views.product.partials._variant-combinations', compact('combinations', 'price', 'product_name', 'stock', 'data'))->render(),
            'length' => count($combinations),
            'stock' => $stock,
            'combinations' => $combinations,
        ]);
    }

    public function variant_price(Request $request)
    {
        if ($request->item_type == 'item') {
            $product = Item::withoutGlobalScope(StoreScope::class)->find($request->id);
        } else {
            $product = ItemCampaign::find($request->id);
        }
        // $product = Item::withoutGlobalScope(StoreScope::class)->find($request->id);
        if (isset($product->module_id) && $product->module->module_type == 'food' && $product->food_variations) {
            $price = $product->price;
            $addon_price = 0;
            if ($request['addon_id']) {
                foreach ($request['addon_id'] as $id) {
                    $addon_price += $request['addon-price'.$id] * $request['addon-quantity'.$id];
                }
            }
            $product_variations = json_decode($product->food_variations, true);
            if ($request->variations && $product_variations && count($product_variations)) {

                $price += Helpers::food_variation_price($product_variations, $request->variations);
            } else {
                $price = $product->price - Helpers::product_discount_calculate($product, $product->price, $product->store)['discount_amount'];
            }
        } else {
            $str = '';
            $quantity = 0;
            $price = 0;
            $addon_price = 0;

            foreach (json_decode($product->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-'.str_replace(' ', '', $request[$choice->name]);
                } else {
                    $str .= str_replace(' ', '', $request[$choice->name]);
                }
            }

            if ($request['addon_id']) {
                foreach ($request['addon_id'] as $id) {
                    $addon_price += $request['addon-price'.$id] * $request['addon-quantity'.$id];
                }
            }

            if ($str != null) {
                $count = count(json_decode($product->variations));
                for ($i = 0; $i < $count; $i++) {
                    if (json_decode($product->variations)[$i]->type == $str) {
                        $price = json_decode($product->variations)[$i]->price - Helpers::product_discount_calculate($product, json_decode($product->variations)[$i]->price, $product->store)['discount_amount'];
                    }
                }
            } else {
                $price = $product->price - Helpers::product_discount_calculate($product, $product->price, $product->store)['discount_amount'];
            }
        }

        return ['price' => Helpers::format_currency(($price * $request->quantity) + $addon_price)];
    }

    public function get_categories(Request $request)
    {
        $key = explode(' ', $request['q']);
        $cat = Category::when(isset($request->module_id), function ($query) use ($request) {
            $query->where('module_id', $request->module_id);
        })
            ->when($request->sub_category, function ($query) {
                $query->where('position', '>', '0');
            })
            ->where(['parent_id' => $request->parent_id])
            ->when(isset($key), function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            })
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'text' => $category->name,
                ];
            });

        return response()->json($cat);
    }

    public function get_items(Request $request)
    {
        $items = Item::withoutGlobalScope(StoreScope::class)->with('store')
            ->when($request->zone_id, function ($q) use ($request) {
                $q->whereHas('store', function ($query) use ($request) {
                    $query->where('zone_id', $request->zone_id);
                });
            })
            ->when($request->module_id, function ($q) use ($request) {
                $q->where('module_id', $request->module_id);
            })->get();
        $res = '';
        if (count($items) > 0 && ! $request->data) {
            $res = '<option value="'. 0 .'" disabled selected>---Select---</option>';
        }

        foreach ($items as $row) {
            $res .= '<option value="'.$row->id.'" ';
            if ($request->data) {
                $res .= in_array($row->id, $request->data) ? 'selected ' : '';
            }
            $res .= '>'.$row->name.' ('.$row->store->name.')'.'</option>';
        }

        return response()->json([
            'options' => $res,
        ]);
    }

    public function get_items_flashsale(Request $request)
    {
        $items = Item::withoutGlobalScope(StoreScope::class)->with('store')->active()
            ->when($request->zone_id, function ($q) use ($request) {
                $q->whereHas('store', function ($query) use ($request) {
                    $query->where('zone_id', $request->zone_id);
                });
            })
            ->when($request->module_id, function ($q) use ($request) {
                $q->where('module_id', $request->module_id);
            })->whereDoesntHave('flashSaleItems.flashSale', function ($query) {
                $now = now();
                $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            })->get();
        $res = '';
        if (count($items) > 0 && ! $request->data) {
            $res = '<option value="'. 0 .'" disabled selected>---Select---</option>';
        }

        foreach ($items as $row) {
            $selected = '';

            if (! empty($request->data) && in_array($row->id, (array) $request->data)) {
                $selected = 'selected';
            }

            $storeName = $row->store->name ?? '';
            $stock = $row->stock ?? 0;

            $res .= '<option value="'.e($row->id).'" '.$selected.'>'
                .e($row->name).' ('.translate('Stock:').' '.e($stock).')'
                .($storeName ? ' ('.e($storeName).')' : '')
                .'</option>';
        }

        return response()->json([
            'options' => $res,
        ]);
    }

    public function list(Request $request)
    {
        $store_id = $request->query('store_id', 'all');
        $category_id = $request->query('category_id', 'all');
        $sub_category_id = $request->query('sub_category_id', 'all');
        $zone_id = $request->query('zone_id', 'all');
        $condition_id = $request->query('condition_id', 'all');
        $brand_id = $request->query('brand_id', 'all');

        $type = $request->query('type', 'all');
        $key = explode(' ', $request['search']);
        $items = Item::withoutGlobalScope(StoreScope::class)
            ->when($request->query('module_id', null), function ($query) use ($request) {
                return $query->module($request->query('module_id'));
            })
            ->when(is_numeric($store_id), function ($query) use ($store_id) {
                return $query->where('store_id', $store_id);
            })
            ->when(is_numeric($sub_category_id), function ($query) use ($sub_category_id) {
                return $query->where('category_id', $sub_category_id);
            })
            ->when(is_numeric($category_id), function ($query) use ($category_id) {
                return $query->whereHas('category', function ($q) use ($category_id) {
                    return $q->whereId($category_id)->orWhere('parent_id', $category_id);
                });
            })
            ->when(is_numeric($zone_id), function ($query) use ($zone_id) {
                return $query->whereHas('store', function ($q) use ($zone_id) {
                    return $q->where('zone_id', $zone_id);
                });
            })
            ->when(is_numeric($condition_id), function ($query) use ($condition_id) {
                return $query->whereHas('pharmacy_item_details', function ($q) use ($condition_id) {
                    return $q->where('common_condition_id', $condition_id);
                });
            })
            ->when(is_numeric($brand_id), function ($query) use ($brand_id) {
                return $query->whereHas('ecommerce_item_details', function ($q) use ($brand_id) {
                    return $q->where('brand_id', $brand_id);
                });
            })
            ->when($request['search'], function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%")->orWhereHas('category', function ($q) use ($value) {
                            return $q->where('name', 'like', "%{$value}%");
                        });
                    }
                });
            })
            ->where('is_approved', 1)
            ->module(Config::get('module.current_module_id'))
            ->type($type)
            ->latest()->paginate(config('default_pagination'));
        $store = $store_id != 'all' ? Store::findOrFail($store_id) : null;
        $category = $category_id != 'all' ? Category::findOrFail($category_id) : null;
        $sub_category = $sub_category_id != 'all' ? Category::findOrFail($sub_category_id) : null;
        $condition = $condition_id != 'all' ? CommonCondition::findOrFail($condition_id) : [];
        $brand = $brand_id != 'all' ? Brand::findOrFail($brand_id) : [];

        $taxData = Helpers::getTaxSystemType(getTaxVatList: false);
        $productWiseTax = $taxData['productWiseTax'];

        return view('admin-views.product.list', compact('items', 'store', 'category', 'type', 'sub_category', 'condition', 'productWiseTax'));
    }

    public function remove_image(Request $request)
    {

        if ($request?->temp_product) {
            $item = TempProduct::withoutGlobalScope(StoreScope::class)->find($request['id']);
        } else {
            $item = Item::withoutGlobalScope(StoreScope::class)->find($request['id']);
        }

        $array = [];
        if (count($item['images']) < 2) {
            Toastr::warning(translate('all_image_delete_warning'));

            return back();
        }

        Helpers::check_and_delete('product/', $request['name']);

        foreach ($item['images'] as $image) {
            if (is_array($image)) {
                if ($image['img'] != $request['name']) {
                    array_push($array, $image);
                }
            } else {
                if ($image != $request['name']) {
                    array_push($array, $image);
                }
            }
        }

        if ($request?->temp_product) {
            TempProduct::withoutGlobalScope(StoreScope::class)->where('id', $request['id'])->update([
                'images' => json_encode($array),
            ]);
        } else {
            Item::withoutGlobalScope(StoreScope::class)->where('id', $request['id'])->update([
                'images' => json_encode($array),
            ]);
        }
        Toastr::success(translate('item_image_removed_successfully'));

        return back();
    }

    public function search(Request $request)
    {
        $view = 'admin-views.product.partials._table';
        $key = explode(' ', $request['search']);
        $store_id = $request->query('store_id', 'all');
        $category_id = $request->query('category_id', 'all');
        $items = Item::withoutGlobalScope(StoreScope::class)
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            })->when(is_numeric($store_id), function ($query) use ($store_id) {
                return $query->where('store_id', $store_id);
            })
            ->when(is_numeric($category_id), function ($query) use ($category_id) {
                return $query->whereHas('category', function ($q) use ($category_id) {
                    return $q->whereId($category_id)->orWhere('parent_id', $category_id);
                });
            })->module(Config::get('module.current_module_id'))->where('is_approved', 1);

        if (isset($request->product_gallery) && $request->product_gallery == 1) {
            $items = $items->limit(12)->get();
            $view = 'admin-views.product.partials._gallery';
        } else {
            $items = $items->latest()->limit(50)->get();
        }

        return response()->json([
            'count' => $items->count(),
            'view' => view($view, compact('items'))->render(),
        ]);
    }

    public function review_list(Request $request)
    {

        $key = explode(' ', $request['search']);
        $reviews = Review::with('item')
            ->when(isset($key), function ($query) use ($key, $request) {
                $query->where(function ($query) use ($key, $request) {

                    $query->whereHas('item', function ($query) use ($key) {
                        foreach ($key as $value) {
                            $query->where('name', 'like', "%{$value}%");
                        }
                    })->orWhereHas('customer', function ($query) use ($key) {
                        foreach ($key as $value) {
                            $query->where('f_name', 'like', "%{$value}%")->orwhere('l_name', 'like', "%{$value}%");
                        }
                    })->orwhere('rating', $request['search'])->orwhere('review_id', $request['search']);
                });
            })
            ->whereHas('item', function ($q) {
                return $q->where('module_id', Config::get('module.current_module_id'))->withoutGlobalScope(StoreScope::class);
            })

            ->latest()->paginate(config('default_pagination'));

        return view('admin-views.product.reviews-list', compact('reviews'));
    }

    public function reviews_status(Request $request)
    {
        $review = Review::find($request->id);
        $review->status = $request->status;
        $review->save();
        Toastr::success(translate('messages.review_visibility_updated'));

        return back();
    }

    // public function review_search(Request $request)
    // {
    //     $key = explode(' ', $request['search']);
    //     $reviews = Review::with('item')
    //     ->when(isset($key), function($query) use($key){
    //         $query->whereHas('item', function ($query) use ($key) {
    //             foreach ($key as $value) {
    //                 $query->where('name', 'like', "%{$value}%");
    //             }
    //         });
    //     })
    //     ->whereHas('item', function ($q) use ($request) {
    //         return $q->where('module_id', Config::get('module.current_module_id'))->withoutGlobalScope(StoreScope::class);
    //     })->limit(50)->get();
    //     return response()->json([
    //         'count' => count($reviews),
    //         'view' => view('admin-views.product.partials._review-table', compact('reviews'))->render()
    //     ]);
    // }

    public function reviews_export(Request $request)
    {
        $key = explode(' ', $request['search']);
        $reviews = Review::with('item')
            ->when(isset($key), function ($query) use ($key) {
                $query->whereHas('item', function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->where('name', 'like', "%{$value}%");
                    }
                });
            })
            ->whereHas('item', function ($q) {
                return $q->where('module_id', Config::get('module.current_module_id'))->withoutGlobalScope(StoreScope::class);
            })

            ->latest()->get();

        $data = [
            'data' => $reviews,
            'search' => $request['search'] ?? null,
        ];
        $typ = 'Item';
        if (Config::get('module.current_module_type') == 'food') {
            $typ = 'Food';
        }
        if ($request->type == 'csv') {
            return Excel::download(new ItemReviewExport($data), $typ.'Review.csv');
        }

        return Excel::download(new ItemReviewExport($data), $typ.'Review.xlsx');
    }

    public function item_wise_reviews_export(Request $request)
    {
        $reviews = Review::where(['item_id' => $request->id])->latest()->get();
        $Item = Item::where('id', $request->id)->first()?->category_ids;
        $data = [
            'type' => 'single',
            'category' => \App\CentralLogics\Helpers::get_category_name($Item),
            'data' => $reviews,
            'search' => $request['search'] ?? null,
            'store' => $request['store'] ?? null,
        ];
        $typ = 'ItemWise';
        if (Config::get('module.current_module_type') == 'food') {
            $typ = 'FoodWise';
        }
        if ($request->type == 'csv') {
            return Excel::download(new ItemReviewExport($data), $typ.'Review.csv');
        }

        return Excel::download(new ItemReviewExport($data), $typ.'Review.xlsx');
    }

    public function bulk_import_index()
    {
        $module_type = Config::get('module.current_module_type');

        return view('admin-views.product.bulk-import', compact('module_type'));
    }

    public function bulk_import_data(Request $request)
    {
        $request->validate([
            'products_file' => 'required|max:2048',
        ]);
        $module_id = Config::get('module.current_module_id');
        $module_type = Config::get('module.current_module_type');

        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error(translate('messages.you_have_uploaded_a_wrong_format_file'));

            return back();
        }
        if ($request->button == 'import') {
            $data = [];
            try {
                foreach ($collections as $collection) {
                    if ($collection['Id'] === '' || $collection['Name'] === '' || $collection['CategoryId'] === '' || $collection['SubCategoryId'] === '' || $collection['Price'] === '' || $collection['StoreId'] === '' || $collection['ModuleId'] === '' || $collection['Discount'] === '' || $collection['DiscountType'] === '') {
                        Toastr::error(translate('messages.please_fill_all_required_fields'));

                        return back();
                    }
                    if (isset($collection['Price']) && ($collection['Price'] < 0)) {
                        Toastr::error(translate('messages.Price_must_be_greater_then_0_on_id').' '.$collection['Id']);

                        return back();
                    }
                    if (isset($collection['Discount']) && ($collection['Discount'] < 0)) {
                        Toastr::error(translate('messages.Discount_must_be_greater_then_0_on_id').' '.$collection['Id']);

                        return back();
                    }
                    if (data_get($collection, 'Image') != '' && strlen(data_get($collection, 'Image')) > 30) {
                        Toastr::error(translate('messages.Image_name_must_be_in_30_char._on_id').' '.$collection['Id']);

                        return back();
                    }
                    try {
                        $t1 = Carbon::parse($collection['AvailableTimeStarts']);
                        $t2 = Carbon::parse($collection['AvailableTimeEnds']);
                        if ($t1->gt($t2)) {
                            Toastr::error(translate('messages.AvailableTimeEnds_must_be_greater_then_AvailableTimeStarts_on_id').' '.$collection['Id']);

                            return back();
                        }
                    } catch (\Exception $e) {
                        info(["line___{$e->getLine()}", $e->getMessage()]);
                        Toastr::error(translate('messages.Invalid_AvailableTimeEnds_or_AvailableTimeStarts_on_id').' '.$collection['Id']);

                        return back();
                    }
                    array_push($data, [
                        'name' => $collection['Name'],
                        'description' => $collection['Description'],
                        'image' => $collection['Image'],
                        'images' => $collection['Images'] ?? json_encode([]),
                        'category_id' => $collection['SubCategoryId'] ? $collection['SubCategoryId'] : $collection['CategoryId'],
                        'category_ids' => json_encode([['id' => $collection['CategoryId'], 'position' => 1], ['id' => $collection['SubCategoryId'], 'position' => 2]]),
                        'unit_id' => is_int($collection['UnitId']) ? $collection['UnitId'] : null,
                        'stock' => is_numeric($collection['Stock']) ? abs($collection['Stock']) : 0,
                        'price' => $collection['Price'],
                        'discount' => $collection['Discount'],
                        'discount_type' => $collection['DiscountType'],
                        'available_time_starts' => $collection['AvailableTimeStarts'] ?? '00:00:00',
                        'available_time_ends' => $collection['AvailableTimeEnds'] ?? '23:59:59',
                        'variations' => $module_type == 'food' ? json_encode([]) : $collection['Variations'] ?? json_encode([]),
                        'choice_options' => $module_type == 'food' ? json_encode([]) : $collection['ChoiceOptions'] ?? json_encode([]),
                        'food_variations' => $module_type == 'food' ? $collection['Variations'] ?? json_encode([]) : json_encode([]),
                        'add_ons' => $collection['AddOns'] ? ($collection['AddOns'] == '' ? json_encode([]) : $collection['AddOns']) : json_encode([]),
                        'attributes' => $collection['Attributes'] ? ($collection['Attributes'] == '' ? json_encode([]) : $collection['Attributes']) : json_encode([]),
                        'store_id' => $collection['StoreId'],
                        'module_id' => $module_id,
                        'status' => $collection['Status'] == 'active' ? 1 : 0,
                        'veg' => $collection['Veg'] == 'yes' ? 1 : 0,
                        'recommended' => $collection['Recommended'] == 'yes' ? 1 : 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                info(["line___{$e->getLine()}", $e->getMessage()]);
                Toastr::error($e->getMessage());

                return back();
            }
            try {
                DB::beginTransaction();
                $chunkSize = 100;
                $chunk_items = array_chunk($data, $chunkSize);
                foreach ($chunk_items as $key => $chunk_item) {
                    //                    DB::table('items')->insert($chunk_item);
                    foreach ($chunk_item as $item) {
                        $insertedId = DB::table('items')->insertGetId($item);
                        Helpers::updateStorageTable(get_class(new Item), $insertedId, $item['image']);
                        if ($module_type === 'pharmacy') {
                            DB::table('pharmacy_item_details')->insert([
                                'item_id' => $insertedId,
                                'is_prescription_required' =>
                                    $collections[$key * $chunkSize + $key]['IsPrescriptionRequired'] ?? 0,
                                'common_condition_id' =>
                                    $collections[$key * $chunkSize + $key]['CommonConditions'] ?? 0,
                                'is_basic' =>
                                    $collections[$key * $chunkSize + $key]['IsBasic'] ?? 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                info(["line___{$e->getLine()}", $e->getMessage()]);
                Toastr::error($e->getMessage());

                return back();
            }
            Toastr::success(translate('messages.product_imported_successfully', ['count' => count($data)]));

            return back();
        }
        $data = [];
        try {
            foreach ($collections as $collection) {
                if ($collection['Id'] === '' || $collection['Name'] === '' || $collection['CategoryId'] === '' || $collection['SubCategoryId'] === '' || $collection['Price'] === '' || $collection['StoreId'] === '' || $collection['ModuleId'] === '' || $collection['Discount'] === '' || $collection['DiscountType'] === '') {
                    Toastr::error(translate('messages.please_fill_all_required_fields'));

                    return back();
                }
                if (isset($collection['Price']) && ($collection['Price'] < 0)) {
                    Toastr::error(translate('messages.Price_must_be_greater_then_0').' '.$collection['Id']);

                    return back();
                }
                if (isset($collection['Discount']) && ($collection['Discount'] < 0)) {
                    Toastr::error(translate('messages.Discount_must_be_greater_then_0').' '.$collection['Id']);

                    return back();
                }
                if (isset($collection['Discount']) && ($collection['Discount'] > 100)) {
                    Toastr::error(translate('messages.Discount_must_be_less_then_100').' '.$collection['Id']);

                    return back();
                }
                if (data_get($collection, 'Image') != '' && strlen(data_get($collection, 'Image')) > 30) {
                    Toastr::error(translate('messages.Image_name_must_be_in_30_char_on_id').' '.$collection['Id']);

                    return back();
                }
                try {
                    $t1 = Carbon::parse($collection['AvailableTimeStarts']);
                    $t2 = Carbon::parse($collection['AvailableTimeEnds']);
                    if ($t1->gt($t2)) {
                        Toastr::error(translate('messages.AvailableTimeEnds_must_be_greater_then_AvailableTimeStarts_on_id').' '.$collection['Id']);

                        return back();
                    }
                } catch (\Exception $e) {
                    info(["line___{$e->getLine()}", $e->getMessage()]);
                    Toastr::error(translate('messages.Invalid_AvailableTimeEnds_or_AvailableTimeStarts_on_id').' '.$collection['Id']);

                    return back();
                }
                array_push($data, [
                    'id' => $collection['Id'],
                    'name' => $collection['Name'],
                    'description' => $collection['Description'],
                    'image' => $collection['Image'],
                    'images' => $collection['Images'] ?? json_encode([]),
                    'category_id' => $collection['SubCategoryId'] ? $collection['SubCategoryId'] : $collection['CategoryId'],
                    'category_ids' => json_encode([['id' => $collection['CategoryId'], 'position' => 1], ['id' => $collection['SubCategoryId'], 'position' => 2]]),
                    'unit_id' => is_int($collection['UnitId']) ? $collection['UnitId'] : null,
                    'stock' => is_numeric($collection['Stock']) ? abs($collection['Stock']) : 0,
                    'price' => $collection['Price'],
                    'discount' => $collection['Discount'],
                    'discount_type' => $collection['DiscountType'],
                    'available_time_starts' => $collection['AvailableTimeStarts'] ?? '00:00:00',
                    'available_time_ends' => $collection['AvailableTimeEnds'] ?? '23:59:59',
                    'variations' => $module_type == 'food' ? json_encode([]) : $collection['Variations'] ?? json_encode([]),
                    'choice_options' => $module_type == 'food' ? json_encode([]) : $collection['ChoiceOptions'] ?? json_encode([]),
                    'food_variations' => $module_type == 'food' ? $collection['Variations'] ?? json_encode([]) : json_encode([]),
                    'add_ons' => $collection['AddOns'] ? ($collection['AddOns'] == '' ? json_encode([]) : $collection['AddOns']) : json_encode([]),
                    'attributes' => $collection['Attributes'] ? ($collection['Attributes'] == '' ? json_encode([]) : $collection['Attributes']) : json_encode([]),
                    'store_id' => $collection['StoreId'],
                    'module_id' => $module_id,
                    'status' => $collection['Status'] == 'active' ? 1 : 0,
                    'veg' => $collection['Veg'] == 'yes' ? 1 : 0,
                    'recommended' => $collection['Recommended'] == 'yes' ? 1 : 0,
                    'updated_at' => now(),
                ]);
            }
            $id = $collections->pluck('Id')->toArray();
            if (Item::whereIn('id', $id)->doesntExist()) {
                Toastr::error(translate('messages.Item_doesnt_exist_at_the_database'));

                return back();
            }
        } catch (\Exception $e) {
            info(["line___{$e->getLine()}", $e->getMessage()]);
            Toastr::error($e->getMessage());

            return back();
        }
        try {
            DB::beginTransaction();
            $chunkSize = 100;
            $chunk_items = array_chunk($data, $chunkSize);
            foreach ($chunk_items as $key => $chunk_item) {
                //                DB::table('items')->upsert($chunk_item, ['id', 'module_id'], ['name', 'description', 'image', 'images', 'category_id', 'category_ids', 'unit_id', 'stock', 'price', 'discount', 'discount_type', 'available_time_starts', 'available_time_ends','choice_options', 'variations', 'food_variations', 'add_ons', 'attributes', 'store_id', 'status', 'veg', 'recommended']);
                foreach ($chunk_item as $item) {
                    if (isset($item['id']) && DB::table('items')->where('id', $item['id'])->exists()) {
                        DB::table('items')->where('id', $item['id'])->update($item);
                        Helpers::updateStorageTable(get_class(new Item), $item['id'], $item['image']);
                    } else {
                        $insertedId = DB::table('items')->insertGetId($item);
                        Helpers::updateStorageTable(get_class(new Item), $insertedId, $item['image']);
                    }

                    if ($module_type === 'pharmacy') {

                        $isPrescriptionRequired = $collections[$key * $chunkSize + $key]['IsPrescriptionRequired'] ?? 0;
                        $commonConditionId = $collections[$key * $chunkSize + $key]['CommonConditions'] ?? 0;
                        $isBasic = $collections[$key * $chunkSize + $key]['IsBasic'] ?? 0;
                        if (
                            DB::table('pharmacy_item_details')
                                ->where('item_id', $item['id'])
                                ->exists()
                        ) {
                            DB::table('pharmacy_item_details')
                                ->where('item_id', $item['id'])
                                ->update([
                                    'is_prescription_required' => $isPrescriptionRequired,
                                    'common_condition_id' => $commonConditionId,
                                    'is_basic' => $isBasic,
                                    'updated_at' => now(),
                                ]);
                        } else {
                            DB::table('pharmacy_item_details')
                                ->insert([
                                    'item_id' => $item['id'],
                                    'is_prescription_required' => $isPrescriptionRequired,
                                    'common_condition_id' => $commonConditionId,
                                    'is_basic' => $isBasic,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            info(["line___{$e->getLine()}", $e->getMessage()]);
            Toastr::error($e->getMessage());

            return back();
        }
        Toastr::success(translate('messages.product_imported_successfully', ['count' => count($data)]));

        return back();
    }

    public function bulk_export_index()
    {
        return view('admin-views.product.bulk-export');
    }

    public function bulk_export_data(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'start_id' => 'required_if:type,id_wise',
            'end_id' => 'required_if:type,id_wise',
            'from_date' => 'required_if:type,date_wise',
            'to_date' => 'required_if:type,date_wise',
        ]);
        $module_type = Config::get('module.current_module_type');
        $products = Item::when($request['type'] == 'date_wise', function ($query) use ($request) {
            $query->whereBetween('created_at', [$request['from_date'].' 00:00:00', $request['to_date'].' 23:59:59']);
        })
            ->when($request['type'] == 'id_wise', function ($query) use ($request) {
                $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
            })
            ->module(Config::get('module.current_module_id'))
            ->withoutGlobalScope(StoreScope::class)->get();

        return (new FastExcel(ProductLogic::format_export_items(Helpers::Export_generator($products), $module_type)))->download('Items.xlsx');
    }

    public function get_variations(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->find($request['id']);

        return response()->json([
            'view' => view('admin-views.product.partials._get_stock_data', compact('product'))->render(),
        ]);
    }

    public function get_stock(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->find($request['id']);

        return response()->json([
            'view' => view('admin-views.product.partials._get_stock_data', compact('product'))->render(),
        ]);
    }

    public function stock_update(Request $request)
    {
        $variations = [];
        $stock_count = $request['current_stock'];
        if ($request->has('type')) {
            foreach ($request['type'] as $key => $str) {
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_'.$key.'_'.str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_'.$key.'_'.str_replace('.', '_', $str)]);
                array_push($variations, $item);
            }
        }

        $product = Item::withoutGlobalScope(StoreScope::class)->find($request['product_id']);

        $product->stock = $stock_count ?? 0;
        $product->variations = json_encode($variations);
        $product->save();
        Toastr::success(translate('messages.Stock_updated_successfully'));

        return back();
    }

    public function search_vendor(Request $request)
    {
        $key = explode(' ', $request['search']);
        if ($request->has('store_id')) {

            $foods = Item::withoutGlobalScope(StoreScope::class)
                ->where('store_id', $request->store_id)
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                })->limit(50)->get();

            return response()->json([
                'count' => count($foods),
                'view' => view('admin-views.vendor.view.partials._product', compact('foods'))->render(),
            ]);
        }
        $foods = Item::withoutGlobalScope(StoreScope::class)->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->where('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();

        return response()->json([
            'count' => count($foods),
            'view' => view('admin-views.vendor.view.partials._product', compact('foods'))->render(),
        ]);
    }

    public function store_item_export(Request $request)
    {
        $key = explode(' ', request()->search);
        $model = app('\\App\\Models\\Item');
        if ($request?->table && $request?->table == 'TempProduct') {
            $model = app('\\App\\Models\\TempProduct');
        }

        $foods = $model->withoutGlobalScope(StoreScope::class)->where('store_id', $request->store_id)
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                });
            })
            ->when($request?->sub_tab == 'active-items', function ($q) {
                $q->where('status', 1);
            })
            ->when($request?->sub_tab == 'inactive-items', function ($q) {
                $q->where('status', 0);
            })
            ->when($request?->sub_tab == 'pending-items', function ($q) {
                $q->where('is_rejected', 0);
            })
            ->when($request?->sub_tab == 'rejected-items', function ($q) {
                $q->where('is_rejected', 1);
            })
            ->latest()->get();

        // dd($request?->sub_tab,$foods,);

        $store = Store::where('id', $request->store_id)->select(['name', 'zone_id'])->first();
        $typ = 'Item';
        if (Config::get('module.current_module_type') == 'food') {
            $typ = 'Food';
        }

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $data = [
            'sub_tab' => $request?->sub_tab,
            'data' => $foods,
            'search' => $request['search'] ?? null,
            'zone' => Helpers::get_zones_name($store->zone_id),
            'store_name' => $store->name,
            'productWiseTax' => $productWiseTax,
        ];
        if ($request->type == 'csv') {
            return Excel::download(new StoreItemExport($data), $typ.'List.csv');
        }

        return Excel::download(new StoreItemExport($data), $typ.'List.xlsx');

    }

    public function export(Request $request)
    {
        $store_id = $request->query('store_id', 'all');
        $category_id = $request->query('category_id', 'all');
        $sub_category_id = $request->query('sub_category_id', 'all');
        $zone_id = $request->query('zone_id', 'all');

        $model = app('\\App\\Models\\Item');
        if ($request?->table && $request?->table == 'TempProduct') {
            $model = app('\\App\\Models\\TempProduct');
        }

        $type = $request->query('type', 'all');
        $key = explode(' ', $request['search']);
        $item = $model->withoutGlobalScope(StoreScope::class)
            ->when($request->query('module_id', null), function ($query) use ($request) {
                return $query->module($request->query('module_id'));
            })
            ->when(is_numeric($store_id), function ($query) use ($store_id) {
                return $query->where('store_id', $store_id);
            })
            ->when(is_numeric($sub_category_id), function ($query) use ($sub_category_id) {
                return $query->where('category_id', $sub_category_id);
            })
            ->when(is_numeric($category_id), function ($query) use ($category_id) {
                return $query->whereHas('category', function ($q) use ($category_id) {
                    return $q->whereId($category_id)->orWhere('parent_id', $category_id);
                });
            })
            ->when(is_numeric($zone_id), function ($query) use ($zone_id) {
                return $query->whereHas('store', function ($q) use ($zone_id) {
                    return $q->where('zone_id', $zone_id);
                });
            })
            ->when($request['search'], function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                });
            })
            ->approved()
            ->module(Config::get('module.current_module_id'))
            ->type($type)
            ->with('category', 'store')
            ->type($type)->latest()->get();

        $format_type = 'Item';
        if (Config::get('module.current_module_type') == 'food') {
            $format_type = 'Food';
        }

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];

        $data = [
            'table' => $request?->table,
            'data' => $item,
            'search' => $request['search'] ?? null,
            'store' => $store_id != 'all' ? Store::findOrFail($store_id)?->name : null,
            'category' => $category_id != 'all' ? Category::findOrFail($category_id)?->name : null,
            'module_name' => Helpers::get_module_name(Config::get('module.current_module_id')),
            'productWiseTax' => $productWiseTax,
        ];
        if ($request->type == 'csv') {
            return Excel::download(new ItemListExport($data), $format_type.'List.csv');
        }

        return Excel::download(new ItemListExport($data), $format_type.'List.xlsx');

    }

    public function search_store(Request $request, $store_id)
    {
        $key = explode(' ', $request['search']);
        $foods = Item::withoutGlobalScope(StoreScope::class)
            ->where('store_id', $store_id)
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            })->limit(50)->get();

        return response()->json([
            'count' => count($foods),
            'view' => view('admin-views.vendor.view.partials._product', compact('foods'))->render(),
        ]);
    }

    public function food_variation_generator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'options' => 'required',
        ]);

        $food_variations = [];
        if (isset($request->options)) {
            foreach (array_values($request->options) as $key => $option) {

                $temp_variation['name'] = $option['name'];
                $temp_variation['type'] = $option['type'];
                $temp_variation['min'] = $option['min'] ?? 0;
                $temp_variation['max'] = $option['max'] ?? 0;
                $temp_variation['required'] = $option['required'] ?? 'off';
                if ($option['min'] > 0 && $option['min'] > $option['max']) {
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (! isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for').$option['name']);

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for').$option['name']);

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp_value = [];

                foreach (array_values($option['values']) as $value) {
                    if (isset($value['label'])) {
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    array_push($temp_value, $temp_option);
                }
                $temp_variation['values'] = $temp_value;
                array_push($food_variations, $temp_variation);
            }
        }

        return response()->json([
            'variation' => json_encode($food_variations),
        ]);
    }

    public function variation_generator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'choice' => 'required',
        ]);
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_'.$no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));

                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp['name'] = 'choice_'.$no;
                $temp['title'] = $request->choice[$key];
                $temp['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $temp);
            }
        }

        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_'.$no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        // Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $temp) {
                    if ($k > 0) {
                        $str .= '-'.str_replace(' ', '', $temp);
                    } else {
                        $str .= str_replace(' ', '', $temp);
                    }
                }
                $temp = [];
                $temp['type'] = $str;
                $temp['price'] = abs($request['price_'.str_replace('.', '_', $str)]);
                $temp['stock'] = abs($request['stock_'.str_replace('.', '_', $str)]);
                array_push($variations, $temp);
            }
        }
        // combinations end

        return response()->json([
            'choice_options' => json_encode($choice_options),
            'variation' => json_encode($variations),
            'attributes' => $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]),
        ]);
    }

    public function approval_list(Request $request)
    {
        abort_if(Helpers::get_mail_status('product_approval') != 1, 404);
        $store_id = $request->query('store_id', 'all');
        $category_id = $request->query('category_id', 'all');
        $sub_category_id = $request->query('sub_category_id', 'all');
        $zone_id = $request->query('zone_id', 'all');
        $type = $request->query('type', 'all');
        $filter = $request->query('filter');
        $key = explode(' ', $request['search']);
        $from = $request->query('from');
        $to = $request->query('to');

        $items = TempProduct::withoutGlobalScope(StoreScope::class)
            ->when($request->query('module_id', null), function ($query) use ($request) {
                return $query->module($request->query('module_id'));
            })
            ->when(is_numeric($store_id), function ($query) use ($store_id) {
                return $query->where('store_id', $store_id);
            })
            ->when(is_numeric($sub_category_id), function ($query) use ($sub_category_id) {
                return $query->where('category_id', $sub_category_id);
            })
            ->when(is_numeric($category_id), function ($query) use ($category_id) {
                return $query->whereHas('category', function ($q) use ($category_id) {
                    return $q->whereId($category_id)->orWhere('parent_id', $category_id);
                });
            })
            ->when(is_numeric($zone_id), function ($query) use ($zone_id) {
                return $query->whereHas('store', function ($q) use ($zone_id) {
                    return $q->where('zone_id', $zone_id);
                });
            })
            ->when($request['search'], function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($filter) && $filter == 'pending', function ($query) {
                return $query->where('is_rejected', 0);
            })
            ->when(isset($filter) && $filter == 'rejected', function ($query) {
                return $query->where('is_rejected', 1);
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && isset($filter) && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('updated_at', [$from.' 00:00:00', $to.' 23:59:59']);
            })

            ->module(Config::get('module.current_module_id'))
            ->type($type)
            ->orderBy('is_rejected', 'asc')
            ->orderBy('updated_at', 'desc')
            ->paginate(config('default_pagination'));
        $store = $store_id != 'all' ? Store::findOrFail($store_id) : null;
        $category = $category_id != 'all' ? Category::findOrFail($category_id) : null;
        $sub_categories = $category_id != 'all' ? Category::where('parent_id', $category_id)->get(['id', 'name']) : [];

        return view('admin-views.product.approv_list', compact('items', 'store', 'category', 'type', 'sub_categories', 'filter'));
    }

    public function requested_item_view($id)
    {
        $product = TempProduct::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->with(['translations', 'store', 'unit'])->findOrFail($id);

        return view('admin-views.product.requested_product_view', compact('product'));
    }

    public function deny(Request $request)
    {
        $data = TempProduct::withoutGlobalScope(StoreScope::class)->findOrfail($request->id);
        $data->is_rejected = 1;
        $data->note = $request->note;
        $data->save();
        Toastr::success(translate('messages.Product_denied'));

        try {

            if (Helpers::getNotificationStatusData('store', 'store_product_reject', 'push_notification_status', $data?->store->id) && $data?->store?->vendor?->firebase_token) {
                $ndata = [
                    'title' => translate('product_rejected'),
                    'description' => translate('Product_Request_Has_Been_Rejected_By_Admin'),
                    'order_id' => '',
                    'image' => '',
                    'type' => 'product_rejected',
                    'order_status' => '',
                ];
                Helpers::send_push_notif_to_device($data?->store?->vendor?->firebase_token, $ndata);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($ndata),
                    'vendor_id' => $data?->store?->vendor_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            if (config('mail.status') && Helpers::get_mail_status('product_deny_mail_status_store')  == '1' &&  Helpers::getNotificationStatusData('store', 'store_product_reject', 'mail_status', $data?->store?->id)) {
                Mail::to($data?->store?->vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorProductMail($data?->store?->name, 'denied'));
            }
        } catch (\Exception $e) {
            info($e->getMessage());
        }

        return to_route('admin.item.approval_list');
    }

    public function approved(Request $request)
    {
        $data = TempProduct::withoutGlobalScope(StoreScope::class)->findOrfail($request->id);

        $item = Item::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->with('translations')->findOrfail($data->item_id);

        $item->name = $data->name;
        $item->description = $data->description;

        if ($item->image && $data->image != null && $item->image !== $data->image) {
            Helpers::check_and_delete('product/', $item['image']);
        }

        foreach ($item->images as $value) {
            $value = is_array($value) ? $value : ['img' => $value, 'storage' => 'public'];
            Helpers::check_and_delete('product/', $value['img']);
        }

        $item->image = $data->image;
        $item->images = $data->images;
        $item->store_id = $data->store_id;
        $item->module_id = $data->module_id;
        $item->unit_id = $data->unit_id;

        $item->category_id = $data->category_id;
        $item->category_ids = $data->category_ids;

        $item->choice_options = $data->choice_options;
        $item->food_variations = $data->food_variations;
        $item->variations = $data->variations;
        $item->add_ons = $data->add_ons;
        $item->attributes = $data->attributes;

        $item->price = $data->price;
        $item->discount = $data->discount;
        $item->discount_type = $data->discount_type;

        $item->available_time_starts = $data->available_time_starts;
        $item->available_time_ends = $data->available_time_ends;
        $item->maximum_cart_quantity = $data->maximum_cart_quantity;
        $item->veg = $data->veg;

        $item->organic = $data->organic;
        $item->is_halal = $data->is_halal;
        $item->stock = $data->stock;
        $item->is_approved = 1;

        $item->save();
        $item->tags()->sync(json_decode($data->tag_ids));
        $item->nutritions()->sync(json_decode($data->nutrition_ids));
        $item->allergies()->sync(json_decode($data->allergy_ids));
        $item->generic()->sync(json_decode($data->generic_ids));

        $item?->pharmacy_item_details()?->delete();
        $item?->seoData()?->delete();

        if ($item->module->module_type == 'pharmacy') {
            DB::table('pharmacy_item_details')->where('temp_product_id', $data->id)->update([
                'item_id' => $item->id,
                'temp_product_id' => null,
            ]);
        }
        if ($item->module->module_type == 'ecommerce') {
            DB::table('ecommerce_item_details')->where('temp_product_id', $data->id)->update([
                'item_id' => $item->id,
                'temp_product_id' => null,
            ]);

            DB::table('item_seo_data')->where('temp_item_id', $data->id)->update([
                'item_id' => $item->id,
                'temp_item_id' => null,
            ]);

        }

        $item?->translations()?->delete();
        $item?->taxVats()?->delete();
        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'product_wise') {
                \Modules\TaxModule\Entities\Taxable::where('taxable_type', 'App\Models\TempProduct')->where('taxable_id', $data->id)
                    ->update(['taxable_type' => 'App\Models\Item', 'taxable_id' => $item->id]);
            }
        }

        Translation::where('translationable_type', 'App\Models\TempProduct')->where('translationable_id', $data->id)->update([
            'translationable_type' => 'App\Models\Item',
            'translationable_id' => $item->id,
        ]);

        $data->delete();

        try {

            if (Helpers::getNotificationStatusData('store', 'store_product_approve', 'push_notification_status', $item?->store->id) && $item?->store?->vendor?->firebase_token) {
                $data = [
                    'title' => translate('product_approved'),
                    'description' => translate('Product_Request_Has_Been_Approved_By_Admin'),
                    'order_id' => '',
                    'image' => '',
                    'type' => 'product_approve',
                    'order_status' => '',
                ];
                Helpers::send_push_notif_to_device($item?->store?->vendor?->firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'vendor_id' => $item?->store?->vendor_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            if (config('mail.status') && Helpers::get_mail_status('product_approve_mail_status_store') == '1' &&  Helpers::getNotificationStatusData('store', 'store_product_approve', 'mail_status', $item?->store?->id)) {
                Mail::to($item?->store?->vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorProductMail($item?->store?->name, 'approved'));
            }
        } catch (\Exception $e) {
            info($e->getMessage());
        }
        Toastr::success(translate('messages.Product_approved'));

        return to_route('admin.item.approval_list');
    }

    public function product_gallery(Request $request)
    {
        $store_id = $request->query('store_id', 'all');
        $category_id = $request->query('category_id', 'all');
        $type = $request->query('type', 'all');
        $key = explode(' ', $request['search']);
        $items = Item::withoutGlobalScope(StoreScope::class)
            ->when($request->query('module_id', null), function ($query) use ($request) {
                return $query->module($request->query('module_id'));
            })
            ->when(is_numeric($store_id), function ($query) use ($store_id) {
                return $query->where('store_id', $store_id);
            })
            ->when(is_numeric($category_id), function ($query) use ($category_id) {
                return $query->whereHas('category', function ($q) use ($category_id) {
                    return $q->whereId($category_id)->orWhere('parent_id', $category_id);
                });
            })
            ->when($request['search'], function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                });
            })
            ->orderByRaw('FIELD(name, ?) DESC', [$request['name']])
            ->where('is_approved', 1)
            ->module(Config::get('module.current_module_id'))
            ->type($type)
            // ->latest()->paginate(config('default_pagination'));
            ->inRandomOrder()->limit(12)->get();
        $store = $store_id != 'all' ? Store::findOrFail($store_id) : null;
        $category = $category_id != 'all' ? Category::findOrFail($category_id) : null;

        return view('admin-views.product.product_gallery', compact('items', 'store', 'category', 'type'));
    }

    private function addOrUpdateMetaData(Request $request, $item_id)
    {
        $itemMetaData = ItemSeoData::updateOrCreate([
            'item_id' => $item_id,
        ]);

        $imageFile = $request->hasFile('meta_image') ? $request->file('meta_image') : $itemMetaData->image;
        $originalExtension = $request->hasFile('meta_image') ? $imageFile->getClientOriginalExtension() : 'png';
        if ($request->has('meta_image_deleted') && $request->meta_image_deleted == 1) {
            Helpers::check_and_delete('item_meta_data/', $itemMetaData->image);
            $itemMetaData->image = null;
        }

        $itemMetaData->title = $request->meta_title;
        $itemMetaData->description = $request->meta_description;
        $itemMetaData->image = $request->file('meta_image') ? Helpers::upload(dir: 'item_meta_data/', format: $originalExtension, image: $imageFile) : $itemMetaData->image;
        $itemMetaData->meta_data = Helpers::formatMetaData($request->all(), $itemMetaData->meta_data);

        $itemMetaData->save();

        return true;
    }
}
