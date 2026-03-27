<?php

namespace App\Http\Controllers\Admin\Item;

use App\Models\Item;
use App\Models\Brand;
use Illuminate\View\View;
use App\Models\TempProduct;
use Illuminate\Http\Request;
use App\Services\BrandService;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Models\EcommerceItemDetails;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\BrandAddRequest;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\Admin\BrandUpdateRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Enums\ViewPaths\Admin\Brand as BrandViewPath;
use App\Contracts\Repositories\BrandRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\CentralLogics\Helpers;
class BrandController extends BaseController
{
    public function __construct(
        protected BrandRepositoryInterface $brandRepo,
        protected BrandService $brandService,
        protected TranslationRepositoryInterface $translationRepo
    )
    {
    }

    public function index(?Request $request): View|Collection|LengthAwarePaginator|null
    {
        return $this->getListView($request);
    }

    private function getListView(Request $request): View
    {
        $brands = $this->brandRepo->getListWhere(
            searchValue: $request['search'],
            dataLimit: config('default_pagination')
        );
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(BrandViewPath::INDEX[VIEW], compact('brands','language','defaultLang'));
    }

    public function add(BrandAddRequest $request): RedirectResponse
    {
        $brand = $this->brandRepo->add(data: $this->brandService->getAddData(request: $request));
        $this->translationRepo->addByModel(request: $request, model: $brand, modelPath: 'App\Models\Brand', attribute: 'name');
        Toastr::success(translate('messages.brand_added_successfully'));
        return back();
    }



    public function update(BrandUpdateRequest $request, $id): RedirectResponse
    {
        $brand = $this->brandRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $brand = $this->brandRepo->update(id: $id ,data: $this->brandService->getUpdateData(request: $request,brand: $brand));
        $this->translationRepo->updateByModel(request: $request, model: $brand, modelPath: 'App\Models\Brand', attribute: 'name');
        Toastr::success(translate('messages.brand_updated_successfully'));
        return back();
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $this->brandRepo->update(id: $request['id'] ,data: ['status'=>$request['status']]);
        Toastr::success(translate('messages.brand_status_updated'));
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->brandRepo->delete(id: $request['id']);
        Toastr::success(translate('messages.brand_deleted_successfully'));
        return back();
    }

    public function getDropdownList(Request $request): JsonResponse
    {
        $data = $this->brandRepo->getDropdownList(request: $request, dataLimit: 8);
        $data = $this->brandService->getDropdownData(data: $data, request: $request);

        return response()->json($data);
    }


    public function moduleUpadte(Request $request)
    {

        $brandId = $request->brand_id;
        if($request->type == 'copy_this_brand'){
            $oldBrand =$this->brandRepo->getFirstWhere(['id'=> $request->brand_id]);
                    $mergedItems = $this->getMergedItems($brandId);
                    $newBrand= null;
                    foreach($mergedItems as $item){
                        if($item['module_id'] !== ($newBrand?->module_id ?? null)){
                            $newBrand= $this->createNewBrand($oldBrand ,$item['module_id']);
                        }

                        if($newBrand){
                            EcommerceItemDetails::where(function($query) use($item){
                                $query->where('item_id',$item['id'])->orWhere('temp_product_id',$item['id']);
                            })->update(['brand_id'=> $newBrand->id]);
                        }
                    }

                    $this->brandRepo->update(id: $request['brand_id'] ,data: ['module_id'=>Config::get('module.current_module_id')]);

                    Toastr::success(translate('messages.New_brand_created_successfully'));
                    return back();
        } elseif($request->type == 'only_this_module'){
            $items = $this->getItemIds($brandId);

            EcommerceItemDetails::where(function($query) use($items){
                $query->whereIn('item_id', $items)->orWhereIn('temp_product_id', $items);
            })->delete();

            $this->brandRepo->update(id: $brandId ,data: ['module_id'=>Config::get('module.current_module_id')]);
            Toastr::success(translate('messages.brand_updated_successfully'));
            return back();
        }
        return back();
    }


    private function createNewBrand($oldBrand ,$module_id){

        $BrandCheck =$this->brandRepo->getFirstWhere(['module_id'=> $module_id,'name'=>$oldBrand->name]);
        if($BrandCheck){
            return $BrandCheck;
        }

        $brand = new Brand ();
        $brand->name=$oldBrand->name;
        $brand->slug=$oldBrand->slug;
        $brand->module_id=$module_id;
        $brand->save();
            $oldDisk = 'public';
            if ($oldBrand->storage && count($oldBrand->storage) > 0) {
                foreach ($oldBrand->storage as $value) {
                    if ($value['key'] == 'image') {
                        $oldDisk = $value['value'];
                    }
                }
            }
            $oldPath = "brand/{$oldBrand->image}";
            $newFileNamethumb = Carbon::now()->toDateString() . "-" . uniqid() . ".png";
            $newPath = "brand/{$newFileNamethumb}";
            $dir = 'brand/';
            $newDisk = Helpers::getDisk();

            try{
                if (Storage::disk($oldDisk)->exists($oldPath)) {
                    if (!Storage::disk($newDisk)->exists($dir)) {
                        Storage::disk($newDisk)->makeDirectory($dir);
                    }
                    $fileContents = Storage::disk($oldDisk)->get($oldPath);
                    Storage::disk($newDisk)->put($newPath, $fileContents);
                }
            } catch (\Exception $e) {
            }
            $brand->image=$newFileNamethumb;
            $brand->slug=$oldBrand->slug.'-'.$brand->id;
            $brand->save();
            return $brand;
    }


    private function getMergedItems($brandId)
    {
        return Item::whereHas('ecommerce_item_details', fn($q) => $q->where('brand_id', $brandId))
            ->where('module_id', '!=', Config::get('module.current_module_id'))
            ->get(['id', 'module_id'])
            ->merge(
                TempProduct::whereHas('ecommerce_item_details', fn($q) => $q->where('brand_id', $brandId))
                    ->where('module_id', '!=', Config::get('module.current_module_id'))
                    ->get(['id', 'module_id'])
            )->toArray();
    }

    private function getItemIds($brandId)
    {
        return Item::whereHas('ecommerce_item_details', fn($q) => $q->where('brand_id', $brandId))
            ->where('module_id', '!=', Config::get('module.current_module_id'))
            ->pluck('id')
            ->merge(
                TempProduct::whereHas('ecommerce_item_details', fn($q) => $q->where('brand_id', $brandId))
                    ->where('module_id', '!=', Config::get('module.current_module_id'))
                    ->pluck('id')
            );
    }

    public function getBrandData(Request $request): JsonResponse
    {
        $brand = $this->brandRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $request->id]);
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return response()->json([
            'view' => view('admin-views.brand.partials.edit_partial', compact('brand','language'))->render(),
        ]);
    }
}
