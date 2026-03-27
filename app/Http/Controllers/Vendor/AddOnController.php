<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\AddonCategory;

class AddOnController extends Controller
{
    public function index(Request $request)
    {
        $key = explode(' ', $request['search']);
        $language = getWebConfig('language');
        $addons = AddOn::orderBy('name')
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })

            ->paginate(config('default_pagination'));

        $addonCategories = AddonCategory::where(function ($query) {
            $query->where('module_id', Helpers::get_store_data()->module_id)->orWhereNull('module_id');
        })->where('status', 1)->select('id', 'name')->get();

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];

        return view('vendor-views.addon.index', compact('addons', 'addonCategories', 'productWiseTax', 'taxVats', 'language'));
    }

    public function store(Request $request)
    {
        if (!Helpers::get_store_data()->item_section) {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }
        $request->validate([
            'name' => 'required|array',
            'name.*' => 'max:191',
            'price' => 'required|numeric|between:0,999999999999.99',
            'name.0' => 'required',
            'category_id' => 'required',
        ], [
            'name.required' => translate('messages.Name is required!'),
            'name.0.required' => translate('default_name_is_required'),
        ]);

        $addon = new AddOn();
        $addon->name = $request->name[array_search('default', $request->lang)];
        $addon->price = $request->price;
        $addon->store_id = \App\CentralLogics\Helpers::get_store_id();
        $addon->addon_category_id = $request->category_id;
        $addon->save();

        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'product_wise') {
                foreach ($request['tax_ids'] ?? [] as $tax_id) {
                    \Modules\TaxModule\Entities\Taxable::create(
                        [
                            'taxable_type' => AddOn::class,
                            'taxable_id' => $addon->id,
                            'system_tax_setup_id' => $SystemTaxVat->id,
                            'tax_id' => $tax_id
                        ],
                    );
                }
            }
        }

        Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'AddOn', data_id: $addon->id, data_value: $addon->name);

        Toastr::success(translate('messages.addon_added_successfully'));
        return back();
    }

    public function edit($id)
    {
        if (!Helpers::get_store_data()->item_section) {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }
        $addon = AddOn::withoutGlobalScope('translate')->with('translations')->findOrFail($id);

        $language = getWebConfig('language');

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];
        $taxVatIds =  $productWiseTax ? $addon->taxVats()->pluck('tax_id')->toArray(): [];

        $addonCategories = AddonCategory::where(function ($query) {
            $query->where('module_id', Helpers::get_store_data()->module_id)->orWhereNull('module_id');
        })->where('status', 1)->select('id', 'name')->get();

        return response()->json([
            'view' => view('vendor-views.addon.edit', compact('addon', 'addonCategories', 'taxVats', 'productWiseTax', 'language', 'taxVatIds'))->render(),
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!Helpers::get_store_data()->item_section) {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }
        $request->validate([
            'name' => 'required|max:191',
            'price' => 'required|numeric|between:0,999999999999.99',
            'name.0' => 'required',
        ], [
            'name.required' => translate('messages.Name is required!'),
            'name.0.required' => translate('default_name_is_required'),
        ]);

        $addon = AddOn::find($id);
        $addon->name = $request->name[array_search('default', $request->lang)];
        $addon->price = $request->price;
        $addon->addon_category_id = $request->category_id;
        $addon->save();

        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'product_wise') {
                $addon->taxVats()->delete();
                foreach ($request['tax_ids'] ?? [] as $tax_id) {
                    \Modules\TaxModule\Entities\Taxable::create(
                        [
                            'taxable_type' => AddOn::class,
                            'taxable_id' => $addon->id,
                            'system_tax_setup_id' => $SystemTaxVat->id,
                            'tax_id' => $tax_id
                        ],
                    );
                }
            }
        }
        Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'AddOn', data_id: $addon->id, data_value: $addon->name);
        Toastr::success(translate('messages.addon_updated_successfully'));
        return redirect(route('vendor.addon.add-new'));
    }

    public function delete(Request $request)
    {
        if (!Helpers::get_store_data()->item_section) {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }
        $addon = AddOn::find($request->id);
        $addon?->translations()->delete();
        $addon?->taxVats()->delete();
        $addon->delete();
        Toastr::success(translate('messages.addon_deleted_successfully'));
        return back();
    }
}
