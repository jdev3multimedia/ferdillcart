<?php

namespace App\Http\Controllers\Admin;

use App\Models\Allergy;
use App\Models\GenericName;
use App\Models\Nutrition;
use App\Models\Store;
use App\Models\Campaign;
use App\Models\Translation;
use Illuminate\Support\Str;
use App\Models\ItemCampaign;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use App\Exports\ItemCampaignExport;
use App\Exports\BasicCampaignExport;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    function index($type)
    {
        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];
        return view('admin-views.campaign.'.$type.'.index', compact('productWiseTax', 'taxVats'));
    }

    function list(Request $request, $type)
    {
        $key = explode(' ', $request['search']);
        if($type=='basic')
        {
            $campaigns=Campaign::with('module')->where('module_id', Config::get('module.current_module_id'))
            ->when(isset($key ), function ($q) use ($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate(config('default_pagination'));
        }
        else{
            $campaigns=ItemCampaign::where('module_id', Config::get('module.current_module_id'))
            ->when(isset($key ), function ($q) use ($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate(config('default_pagination'));
        }

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];

        return view('admin-views.campaign.'.$type.'.list', compact('campaigns','productWiseTax', 'taxVats'));
    }

    public function storeBasic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:campaigns|max:191',
            'description'=>'max:1000',
            'image' => 'required',
            'title.0' => 'required',
            'description.0' => 'required',
        ],[
            'title.0.required'=>translate('default_title_is_required'),
            'description.0.required'=>translate('default_description_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $campaign = new Campaign;
        $campaign->title = $request->title[array_search('default', $request->lang)];
        $campaign->description = $request->description[array_search('default', $request->lang)];
        $campaign->image = Helpers::upload('campaign/', 'png', $request->file('image'));
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->module_id = Config::get('module.current_module_id');
        $campaign->save();
        $data = [];
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if($default_lang == $key && !($request->title[$index])){
                if ($key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\Campaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'title',
                        'value' => $campaign->title,
                    ));
                }
            }else{
                if ($request->title[$index] && $key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\Campaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'title',
                        'value' => $request->title[$index],
                    ));
                }
            }

            if($default_lang == $key && !($request->description[$index])){
                if (isset($campaign->description) && $key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\Campaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'description',
                        'value' => $campaign->description,
                    ));
                }
            }else{
                if ($request->description[$index] && $key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\Campaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'description',
                        'value' => $request->description[$index],
                    ));
                }
            }
        }

        Translation::insert($data);

        return response()->json([], 200);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|max:191',
            'description' => 'max:1000',
            'title.0' => 'required',
            'description.0' => 'required',
        ],[
            'title.0.required'=>translate('default_title_is_required'),
            'description.0.required'=>translate('default_description_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $campaign->title = $request->title[array_search('default', $request->lang)];
        $campaign->description = $request->description[array_search('default', $request->lang)];
        $campaign->image = $request->has('image') ? Helpers::update('campaign/', $campaign->image, 'png', $request->file('image')) : $campaign->image;;
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->save();
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if($default_lang == $key && !($request->title[$index])){
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Campaign',
                            'translationable_id' => $campaign->id,
                            'locale' => $key,
                            'key' => 'title'
                        ],
                        ['value' => $campaign->title]
                    );
                }
            }else{

                if ($request->title[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Campaign',
                            'translationable_id' => $campaign->id,
                            'locale' => $key,
                            'key' => 'title'
                        ],
                        ['value' => $request->title[$index]]
                    );
                }
            }
            if($default_lang == $key && !($request->description[$index])){
                if (isset($campaign->description) && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Campaign',
                            'translationable_id' => $campaign->id,
                            'locale' => $key,
                            'key' => 'description'
                        ],
                        ['value' => $campaign->description]
                    );
                }

            }else{

                if ($request->description[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Campaign',
                            'translationable_id' => $campaign->id,
                            'locale' => $key,
                            'key' => 'description'
                        ],
                        ['value' => $request->description[$index]]
                    );
                }
            }
        }

        return response()->json([], 200);
    }

    public function storeItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191|unique:item_campaigns',
            'image' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric|between:0.01,999999999999.99',
            'store_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_date' => 'required',
            'start_date' => 'required',
            'veg' => 'required',
            'description'=>'max:1000',
            'title.0' => 'required',
            'description.0' => 'required',
        ], [
            'category_id.required' => translate('messages.select_category'),
            'title.0.required'=>translate('default_title_is_required'),
            'description.0.required'=>translate('default_description_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('messages.discount_can_not_be_more_than_or_equal'));
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $campaign = new ItemCampaign;

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

        $campaign->category_ids = json_encode($category);
        $campaign->category_id = $request->sub_category_id?$request->sub_category_id:$request->category_id;
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $item);
            }
        }
        $campaign->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
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
                if ($option['min'] > 0 &&  $option['min'] > $option['max']) {
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (!isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for') . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for') . $option['name']);
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

        $campaign->admin_id = auth('admin')->id();
        $campaign->title = $request->title[array_search('default', $request->lang)];
        $campaign->description = $request->description[array_search('default', $request->lang)];
        $campaign->image = Helpers::upload('campaign/', 'png', $request->file('image'));
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->variations = json_encode($variations);
        $campaign->food_variations = json_encode($food_variations);
        $campaign->price = $request->price;
        $campaign->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $campaign->discount_type = $request->discount_type;
        $campaign->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $campaign->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $campaign->store_id = $request->store_id;
        $campaign->veg = $request->veg;
        $campaign->module_id= Config::get('module.current_module_id');
        $campaign->maximum_cart_quantity = $request->maximum_cart_quantity;
        $campaign->stock= $request->current_stock;
        $campaign->unit_id = $request->unit;
        $campaign->save();
        $campaign->nutritions()->sync($nutrition_ids);
        $campaign->allergies()->sync($allergy_ids);
        if($campaign->module->module_type == 'pharmacy') {
            $campaign->generic()->sync($generic_ids);
        }


        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'product_wise') {
                foreach ($request['tax_ids'] ?? [] as $tax_id) {
                    \Modules\TaxModule\Entities\Taxable::create(
                        [
                            'taxable_type' => ItemCampaign::class,
                            'taxable_id' => $campaign->id,
                            'system_tax_setup_id' => $SystemTaxVat->id,
                            'tax_id' => $tax_id
                        ],
                    );
                }
            }
        }


        $data = [];
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if($default_lang == $key && !($request->title[$index])){
                if ($key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\ItemCampaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'title',
                        'value' => $campaign->title,
                    ));
                }
            }else{
                if ($request->title[$index] && $key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\ItemCampaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'title',
                        'value' => $request->title[$index],
                    ));
                }
            }

            if($default_lang == $key && !($request->description[$index])){
                if (isset($campaign->description) && $key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\ItemCampaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'description',
                        'value' => $campaign->description,
                    ));
                }
            }else{
                if ($request->description[$index] && $key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\ItemCampaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'description',
                        'value' => $request->description[$index],
                    ));
                }
            }
        }

        Translation::insert($data);

        return response()->json([], 200);
    }

    public function updateItem(ItemCampaign $campaign, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|array',
            'category_id' => 'required',
            'price' => 'required|numeric|between:0.01,999999999999.99',
            'veg' => 'required',
            'description.*'=>'max:1000',
            'title.0' => 'required',
            'description.0' => 'required',
        ],[
            'title.0.required'=>translate('default_title_is_required'),
            'description.0.required'=>translate('default_description_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('messages.discount_can_not_be_more_than_or_equal'));
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
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

        $campaign->category_ids = json_encode($category);
        $campaign->category_id = $request->sub_category_id?$request->sub_category_id:$request->category_id;
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $item);
            }
        }
        $campaign->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
            }
        }

        $food_variations = [];
        if (isset($request->options)) {
            foreach (array_values($request->options) as $key => $option) {
                $temp_variation['name'] = $option['name'];
                $temp_variation['type'] = $option['type'];
                $temp_variation['min'] = $option['min'] ?? 0;
                $temp_variation['max'] = $option['max'] ?? 0;
                if ($option['min'] > 0 &&  $option['min'] > $option['max']) {
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (!isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for') . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for') . $option['name']);
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
        $slug = Str::slug($request->title[array_search('default', $request->lang)]);
        $campaign->slug = $campaign->slug? $campaign->slug :"{$slug}{$campaign->id}";
        $campaign->title = $request->title[array_search('default', $request->lang)];
        $campaign->description = $request->description[array_search('default', $request->lang)];
        $campaign->image = $request->has('image') ? Helpers::update('campaign/', $campaign->image, 'png', $request->file('image')) : $campaign->image;
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->variations = json_encode($variations);
        $campaign->food_variations = json_encode($food_variations);
        $campaign->price = $request->price;
        $campaign->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $campaign->discount_type = $request->discount_type;
        $campaign->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $campaign->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $campaign->veg = $request->veg;
        $campaign->unit_id = $request->unit;
        $campaign->maximum_cart_quantity = $request->maximum_cart_quantity;
        $campaign->stock= $request->current_stock;
        $campaign->save();
        $campaign->nutritions()->sync($nutrition_ids);
        $campaign->allergies()->sync($allergy_ids);
        if($campaign->module->module_type == 'pharmacy') {
            $campaign->generic()->sync($generic_ids);
        }
        $default_lang = str_replace('_', '-', app()->getLocale());

        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'product_wise') {
                $campaign->taxVats()->delete();
                foreach ($request['tax_ids'] ?? [] as $tax_id) {
                    \Modules\TaxModule\Entities\Taxable::create(
                        [
                            'taxable_type' => ItemCampaign::class,
                            'taxable_id' => $campaign->id,
                            'system_tax_setup_id' => $SystemTaxVat->id,
                            'tax_id' => $tax_id
                        ],
                    );
                }
            }
        }

        foreach ($request->lang as $index => $key) {
            if($default_lang == $key && !($request->title[$index])){
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\ItemCampaign',
                            'translationable_id' => $campaign->id,
                            'locale' => $key,
                            'key' => 'title'
                        ],
                        ['value' => $campaign->title]
                    );
                }
            }else{

                if ($request->title[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\ItemCampaign',
                            'translationable_id' => $campaign->id,
                            'locale' => $key,
                            'key' => 'title'
                        ],
                        ['value' => $request->title[$index]]
                    );
                }
            }
            if($default_lang == $key && !($request->description[$index])){
                if (isset($campaign->description) && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\ItemCampaign',
                            'translationable_id' => $campaign->id,
                            'locale' => $key,
                            'key' => 'description'
                        ],
                        ['value' => $campaign->description]
                    );
                }

            }else{

                if ($request->description[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\ItemCampaign',
                            'translationable_id' => $campaign->id,
                            'locale' => $key,
                            'key' => 'description'
                        ],
                        ['value' => $request->description[$index]]
                    );
                }
            }
        }

        return response()->json([], 200);
    }

    public function edit($type, $campaign)
    {
        if($type=='basic')
        {
            $campaign = Campaign::withoutGlobalScope('translate')->findOrFail($campaign);
            return view('admin-views.campaign.'.$type.'.edit', compact('campaign'));
        }
        else
        {
            $campaign = ItemCampaign::withoutGlobalScope('translate')->findOrFail($campaign);
            $temp = $campaign->category;
            if($temp?->position)
            {
                $sub_category = $temp;
                $category = $temp->parent;
            }
            else
            {
                $category = $temp;
                $sub_category = null;
            }

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];
        $taxVatIds = $productWiseTax ? $campaign->taxVats()->pluck('tax_id')->toArray() : [];
            return view('admin-views.campaign.'.$type.'.edit', compact('campaign','sub_category','category','taxVats', 'productWiseTax', 'taxVatIds'));
        }

    }

    public function view($type, $campaign)
    {
        if($type=='basic')
        {
            $campaign = Campaign::Running()->where('id',$campaign)->first();
            if(!$campaign){
                Toastr::error(translate('messages.campaign_is_expired'));
                return back();
            }
            $stores = $campaign->stores()->paginate(config('default_pagination'));
            $store_ids = [];
            foreach($campaign->stores as $store)
            {
                $store_ids[] = $store->id;
            }
            return view('admin-views.campaign.basic.view', compact('campaign', 'stores', 'store_ids'));
        }
        else
        {
            $campaign = ItemCampaign::findOrFail($campaign);
        }
        return view('admin-views.campaign.item.view', compact('campaign'));

    }

    public function status($type, $id, $status)
    {
        if($type=='item')
        {
            $campaign = ItemCampaign::findOrFail($id);
        }
        else{
            $campaign = Campaign::findOrFail($id);
        }
        $campaign->status = $status;
        $campaign->save();
        Toastr::success(translate('messages.campaign_status_updated'));
        return back();
    }

    public function delete(Campaign $campaign)
    {

        Helpers::check_and_delete('campaign/' , $campaign->image);

        $campaign->translations()->delete();
        $campaign->delete();
        Toastr::success(translate('messages.campaign_deleted_successfully'));
        return back();
    }
    public function delete_item(ItemCampaign $campaign)
    {

        Helpers::check_and_delete('campaign/' , $campaign->image);

        $campaign->translations()->delete();
        $campaign?->taxVats()->delete();
        $campaign?->carts()?->delete();
        $campaign->delete();
        Toastr::success(translate('messages.campaign_deleted_successfully'));
        return back();
    }

    public function remove_store(Campaign $campaign, $store)
    {
        $campaign->stores()->detach($store);
        $campaign->save();
        try
        {
                    $push_notification_status= Helpers::getNotificationStatusData('store','store_campaign_join_rejaction','push_notification_status',$store->id);
                    $store_push_notification_title= translate('Campaign_Request_Rejected') ;
                    $store_push_notification_description= translate('Campaign_Request_Has_Been_Rejected_By_Admin') ;


                if($push_notification_status  &&  $store?->vendor?->firebase_token){

                    $data = [
                        'title' => $store_push_notification_title,
                        'description' => $store_push_notification_description,
                        'order_id' => '',
                        'image' => '',
                        'data_id'=> $campaign->id,
                        'type' => 'campaign',
                        'order_status' => '',
                    ];
                    Helpers::send_push_notif_to_device($store?->vendor?->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $store->vendor_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

            if(config('mail.status') && Helpers::get_mail_status('campaign_deny_mail_status_store') == '1' &&  Helpers::getNotificationStatusData('store','store_campaign_join_rejaction','mail_status',$store->id )) {
                Mail::to($store->vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorCampaignRequestMail($store->name,'denied'));
            }
        }
        catch(\Exception $e)
        {
            info($e->getMessage());
        }
        Toastr::success(translate('messages.store_remove_from_campaign'));
        return back();
    }
    public function addstore(Request $request, Campaign $campaign)
    {
        $campaign->stores()->attach($request->store_id,['campaign_status' => 'confirmed']);
        $campaign->save();
        Toastr::success(translate('messages.store_added_to_campaign'));
        return back();
    }

    public function store_confirmation($campaign,$store_id,$status)
    {
        $campaign = Campaign::findOrFail($campaign);
        $campaign->stores()->updateExistingPivot($store_id,['campaign_status' => $status]);
        $campaign->save();
        try
        {
            $store=Store::find($store_id);

            if ( $status == 'confirmed') {
                    $push_notification_status= Helpers::getNotificationStatusData('store','store_campaign_join_approval','push_notification_status',$store->id);
                    $store_push_notification_description= translate('Campaign_Request_Has_Been_Approved_By_Admin') ;
                    $store_push_notification_title= translate('Campaign_Request_Approved') ;
                }
                else{
                    $push_notification_status= Helpers::getNotificationStatusData('store','store_campaign_join_rejaction','push_notification_status',$store->id);
                    $store_push_notification_title= translate('Campaign_Request_Rejected') ;
                    $store_push_notification_description= translate('Campaign_Request_Has_Been_Rejected_By_Admin') ;
                }

                if($push_notification_status  &&  $store?->vendor?->firebase_token){

                    $data = [
                        'title' => $store_push_notification_title,
                        'description' => $store_push_notification_description,
                        'order_id' => '',
                        'image' => '',
                        'type' => 'campaign',
                        'data_id'=> $campaign->id,
                        'order_status' => '',
                    ];
                    Helpers::send_push_notif_to_device($store?->vendor?->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $store->vendor_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }



            if(config('mail.status') && Helpers::get_mail_status('campaign_deny_mail_status_store') == '1' && $status == 'rejected' &&  Helpers::getNotificationStatusData('store','store_campaign_join_rejaction','mail_status',$store->id )) {
                Mail::to($store->vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorCampaignRequestMail($store->name,'denied'));
            }

            if(config('mail.status') && Helpers::get_mail_status('campaign_approve_mail_status_store') == '1' && $status == 'confirmed' &&  Helpers::getNotificationStatusData('store','store_campaign_join_approval','mail_status',$store->id )) {
                Mail::to($store->vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorCampaignRequestMail($store->name,'approved'));
            }
        }
        catch(\Exception $e)
        {
            info($e->getMessage());
        }
        Toastr::success(translate('messages.store_added_to_campaign'));
        return back();
    }


    public function basic_campaign_export(Request $request){
        $key = explode(' ', $request['search']);
        $campaigns=Campaign::with('module')->where('module_id', Config::get('module.current_module_id'))
        ->when(isset($key ), function ($q) use ($key){
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->get();
        if($request->type == 'csv'){
            return Excel::download(new BasicCampaignExport($campaigns,$request['search']), 'Campaign.csv');
        }
        return Excel::download(new BasicCampaignExport($campaigns,$request['search']), 'Campaign.xlsx');
    }

    public function item_campaign_export(Request $request){
        $key = explode(' ', $request['search']);
        $campaigns=ItemCampaign::where('module_id', Config::get('module.current_module_id'))
        ->when(isset($key ), function ($q) use ($key){
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->get();

        $campaign='Item';
        if(Config::get('module.current_module_type')== 'food'){
            $campaign='Food';
        }

        if($request->type == 'csv'){
            return Excel::download(new ItemCampaignExport($campaigns,$request['search']), $campaign.'Campaign.csv');
        }
        return Excel::download(new ItemCampaignExport($campaigns,$request['search']), $campaign.'Campaign.xlsx');
    }

}
