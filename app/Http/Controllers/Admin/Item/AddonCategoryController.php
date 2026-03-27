<?php

namespace App\Http\Controllers\Admin\Item;

use App\CentralLogics\Helpers;
use App\Exports\AddonCategoryExport;
use App\Http\Controllers\Controller;
use App\Models\AddonCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class AddonCategoryController extends Controller
{

    public function index(Request   $request)
    {
        $key = explode(' ', $request['search']);

        $taxData = Helpers::getTaxSystemType();
        $categoryWiseTax = $taxData['categoryWiseTax'];
        $taxVats = $taxData['taxVats'];

        $categories = AddonCategory::where('module_id', Config::get('module.current_module_id'))
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->with('taxVats.tax')
            ->paginate(config('default_pagination'));

        $language = getWebConfig('language');
        return view('admin-views.addon.addon-category.index', compact('categories', 'language', 'categoryWiseTax', 'taxVats'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name.0' => 'required',
            'name.*' => 'max:255',
        ]);
        $addonCategory = new AddonCategory();
        $addonCategory->name = $request->name[array_search('default', $request->lang)];
        $addonCategory->module_id = Config::get('module.current_module_id');
        $addonCategory->save();

        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'category_wise') {
                foreach ($request['tax_ids'] ?? [] as $tax_id) {
                    \Modules\TaxModule\Entities\Taxable::create(
                        [
                            'taxable_type' => AddonCategory::class,
                            'taxable_id' => $addonCategory->id,
                            'system_tax_setup_id' => $SystemTaxVat->id,
                            'tax_id' => $tax_id
                        ],
                    );
                }
            }
        }
        Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'AddonCategory', data_id: $addonCategory->id, data_value: $addonCategory->name);

        Toastr::success(translate('messages.Addon_Category_added_successfully'));
        return back();
    }

    public function edit(Request $request)
    {
        $addonCategory = AddonCategory::withoutGlobalScope('translate')->with('translations')->findOrfail($request->id);
        $taxData = Helpers::getTaxSystemType();
        $categoryWiseTax = $taxData['categoryWiseTax'];
        $taxVats = $taxData['taxVats'];
        $taxVatIds = $categoryWiseTax ? $addonCategory->taxVats()->pluck('tax_id')->toArray() : [];

        $language = getWebConfig('language');
        return response()->json([
            'view' => view('admin-views.addon.addon-category._edit', compact('addonCategory', 'taxVats', 'categoryWiseTax', 'language', 'taxVatIds'))->render(),
        ]);
    }
    public function update(Request $request)
    {
        $addonCategory = AddonCategory::findOrfail($request->id);
        $addonCategory->name = $request->name[array_search('default', $request->lang)];
        $addonCategory->status = $request->status ?? 0;

        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'category_wise') {
                $addonCategory->taxVats()->delete();
                foreach ($request['tax_ids'] ?? [] as $tax_id) {
                    \Modules\TaxModule\Entities\Taxable::create(
                        [
                            'taxable_type' => AddonCategory::class,
                            'taxable_id' => $addonCategory->id,
                            'system_tax_setup_id' => $SystemTaxVat->id,
                            'tax_id' => $tax_id
                        ],
                    );
                }
            }
        }

        $addonCategory->save();
        Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'AddonCategory', data_id: $addonCategory->id, data_value: $addonCategory->name);

        return back();
    }

    public function status(Request $request)
    {
        $addonCategory = AddonCategory::findOrFail($request->id);
        $addonCategory->status =  !$addonCategory->status;
        $addonCategory->save();
        Toastr::success(translate('messages.status_updated'));
        return back();
    }

    public function delete(Request $request)
    {
        $addonCategory = AddonCategory::findOrfail($request->id);
        $addonCategory?->translations()->delete();
        $addonCategory?->taxVats()->delete();
        $addonCategory->delete();
        Toastr::success(translate('messages.Addon_Category_deleted_successfully'));
        return back();
    }


    public function exportAddonCategories(Request $request)
    {
        try {
            $key = explode(' ', $request['search']);
            $categories = AddonCategory::where('module_id', Config::get('module.current_module_id'))
                ->when(isset($key), function ($q) use ($key) {
                    $q->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('name', 'like', "%{$value}%");
                        }
                    });
                })
                ->with('taxVats.tax')->get();
            $taxData = Helpers::getTaxSystemType();
            $categoryWiseTax = $taxData['categoryWiseTax'];


            $data = [
                'data' => $categories,
                'search' => $request['search'] ?? null,
                'categoryWiseTax' => $categoryWiseTax
            ];
            if ($request->type == 'csv') {
                return Excel::download(new AddonCategoryExport($data), 'AddonCategories.csv');
            }
            return Excel::download(new AddonCategoryExport($data), 'AddonCategories.xlsx');
        } catch (\Exception $e) {
            Toastr::error("line___{$e->getLine()}", $e->getMessage());
            info(["line___{$e->getLine()}", $e->getMessage()]);
            return back();
        }
    }
}
