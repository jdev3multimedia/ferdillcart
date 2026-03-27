<?php

namespace Modules\AI\app\Services\Products\Resource;

use App\CentralLogics\Helpers;
use App\Models\Brand;
use App\Models\AddOn;
use App\Models\Allergy;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\CommonCondition;
use App\Models\GenericName;
use App\Models\Nutrition;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ProductResource
{

    private $productType = ["veg", "nonveg"];
    protected Category $category;
    protected Nutrition $nutrition;
    protected Allergy $allergy;
    protected AddOn $addon;
    protected Unit $unit;
    protected Attribute $attribute;
    protected GenericName $genericName;
    protected CommonCondition $commonCondition;
    protected Brand $brand;



    public function __construct()
    {
        $this->category = new Category();
        $this->nutrition = new Nutrition();
        $this->allergy = new Allergy();
        $this->addon = new AddOn();
        $this->unit = new Unit();
        $this->attribute = new Attribute();
        $this->genericName = new GenericName();
        $this->commonCondition = new CommonCondition();
        $this->brand = new Brand();

    }

    private function getCategoryEntitiyData($position = 0)
    {
        $moduleId = null;
        if(request()->has('module_id')){
            $moduleId = request()->get('module_id');
        } else{
            $moduleId = Auth::guard('admin')->check() ? Config::get('module.current_module_id') ?? null : Helpers::get_store_data()?->module_id ?? null;
        }
        return $this->category
            ->where(['position' => $position, 'status' => 1])
            ->when($moduleId, function ($query) use ($moduleId) {
                return $query->where('module_id', $moduleId);
            })
            ->get(['id', 'name'])
            ->mapWithKeys(fn($item) => [strtolower($item->name) => $item->id])
            ->toArray();
    }
    private function getSubCategoryEntitiyData()
    {
        $moduleId = null;
        if(request()->has('module_id')){
            $moduleId = request()->get('module_id');
        } else{
            $moduleId = Auth::guard('admin')->check() ? Config::get('module.current_module_id') ?? null : Helpers::get_store_data()->module_id ?? null;
       }
        return $this->category
            ->where(['position' => 1, 'status' => 1])
             ->when($moduleId, function ($query) use ($moduleId) {
                return $query->where('module_id', $moduleId);
            })
            ->select(['id', 'name', 'parent_id'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'parent_id' => $item->parent_id,
                ];
            })
            ->toArray();
    }
    private function getAddonEntitiyData($storeId)
    {
        return $storeId ? $this->addon
            ->where(['store_id' => $storeId, 'status' => 1])
            ->get(['id', 'name'])
            ->mapWithKeys(fn($item) => [strtolower($item->name) => $item->id])
            ->toArray() : [];
    }

    private function getNuttitionEntitiyData()
    {
        return $this->nutrition
            ->get(['id', 'nutrition'])
            ->mapWithKeys(fn($item) => [strtolower($item->nutrition) => $item->id])
            ->toArray();
    }

    private function getAllergyEntitiyData()
    {
        return $this->allergy
            ->get(['id', 'allergy'])
            ->mapWithKeys(fn($item) => [strtolower($item->allergy) => $item->id])
            ->toArray();
    }
    private function getGenericName()
    {
        return $this->genericName
            ->get(['id', 'generic_name'])
            ->mapWithKeys(fn($item) => [strtolower($item->generic_name) => $item->id])
            ->toArray();
    }

    private function getBrandData()
    {
        return $this->brand->where('status', 1)
            ->get(['id', 'name'])
            ->mapWithKeys(fn($item) => [strtolower($item->name) => $item->id])
            ->toArray();
    }
    private function getCommonConditionData()
    {
        return $this->commonCondition->where('status', 1)
            ->get(['id', 'name'])
            ->mapWithKeys(fn($item) => [strtolower($item->name) => $item->id])
            ->toArray();
    }
    private function getUnitData()
    {
        return $this->unit
            ->get(['id', 'unit'])
            ->mapWithKeys(fn($item) => [strtolower($item->unit) => $item->id])
            ->toArray();
    }


    public function productGeneralSetupData($storeId, $moduleType = null): array
    {
        $data = [
            'categories'      => $this->getCategoryEntitiyData(0),
            'sub_categories'  => $this->getCategoryEntitiyData(1),
            'rawSubCategories' => $this->getSubCategoryEntitiyData(),
            'product_types'   => $this->productType,
            'units'           => $this->getUnitData(),
        ];

        if (in_array($moduleType, ['food', 'grocery'])) {
            $data = array_merge($data, [
                'addon'     => $this->getAddonEntitiyData($storeId),
                'nutrition' => $this->getNuttitionEntitiyData(),
                'allergy'   => $this->getAllergyEntitiyData(),
            ]);
        } elseif ($moduleType === 'pharmacy') {
            $data = array_merge($data, [
                'generic_names'     => $this->getGenericName(),
                'common_conditions' => $this->getCommonConditionData(),
            ]);
        } elseif ($moduleType === 'shop' || $moduleType === 'ecommerce') {
            $data = array_merge($data, [
                'brands' => $this->getBrandData(),
            ]);
        }
        return $data;
    }


    public function getVariationData(): array
    {
        $data = [
            'attributes' => $this->attribute
                ->get(['id', 'name'])
                ->mapWithKeys(fn($item) => [strtolower($item->name) => $item->id])
                ->toArray()
        ];
        return $data;
    }
}
