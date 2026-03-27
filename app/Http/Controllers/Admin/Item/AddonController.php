<?php

namespace App\Http\Controllers\Admin\Item;

use App\CentralLogics\Helpers;
use App\Contracts\Repositories\AddonRepositoryInterface;
use App\Contracts\Repositories\StoreRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ExportFileNames\Admin\Addon;
use App\Enums\ViewPaths\Admin\Addon as AddonViewPath;
use App\Exports\AddonExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AddonAddRequest;
use App\Http\Requests\Admin\AddonBulkExportRequest;
use App\Http\Requests\Admin\AddonBulkImportRequest;
use App\Http\Requests\Admin\AddonUpdateRequest;
use App\Models\AddOn as ModelsAddOn;
use App\Models\AddonCategory;
use App\Services\AddonService;
use App\Traits\ImportExportTrait;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\JsonResponse;

class AddonController extends BaseController
{
    use ImportExportTrait;

    public function __construct(
        protected AddonRepositoryInterface $addonRepo,
        protected AddonService $addonService,
        protected TranslationRepositoryInterface $translationRepo,
        protected StoreRepositoryInterface $storeRepo
    ) {}

    public function index(?Request $request): View|Collection|LengthAwarePaginator|null
    {
        return $this->getListView($request);
    }

    public function getListView(Request $request): View|Collection|LengthAwarePaginator|null
    {
        $storeId = $request->query('store_id', 'all');

        $addons = $this->addonRepo->getStoreWiseList(
            moduleId: Config::get('module.current_module_id'),
            searchValue: $request['search'],
            storeId: $storeId,
            dataLimit: config('default_pagination')
        );
        $store = $storeId != 'all' ? $this->storeRepo->getFirstWhere(params: ['id' => $storeId]) : null;
        $language = getWebConfig('language');

        $addonCategories = AddonCategory::where(function ($query) {
            $query->where('module_id', Config::get('module.current_module_id'))->orWhereNull('module_id');
        })->where('status', 1)->select('id', 'name')->get();

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];

        return view(AddonViewPath::INDEX[VIEW], compact('addons', 'store', 'language', 'addonCategories', 'productWiseTax', 'taxVats'));
    }

    public function add(AddonAddRequest $request): RedirectResponse
    {
        $addon = $this->addonRepo->add(data: $this->addonService->getAddData(request: $request));

        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'product_wise') {
                foreach ($request['tax_ids'] ?? [] as $tax_id) {
                    \Modules\TaxModule\Entities\Taxable::create(
                        [
                            'taxable_type' => ModelsAddOn::class,
                            'taxable_id' => $addon->id,
                            'system_tax_setup_id' => $SystemTaxVat->id,
                            'tax_id' => $tax_id
                        ],
                    );
                }
            }
        }

        $this->translationRepo->addByModel(request: $request, model: $addon, modelPath: 'App\Models\AddOn', attribute: 'name');
        Toastr::success(translate('messages.addon_added_successfully'));
        return back();
    }

    public function getUpdateView(string|int $id): JsonResponse
    {
        $addon = $this->addonRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $language = getWebConfig('language');

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];
        $taxVatIds =  $productWiseTax ? $addon->taxVats()->pluck('tax_id')->toArray(): [];


        $addonCategories = AddonCategory::where(function ($query) {
            $query->where('module_id', Config::get('module.current_module_id'))->orWhereNull('module_id');
        })->where('status', 1)->select('id', 'name')->get();
        return response()->json([
            'view' => view(AddonViewPath::UPDATE[VIEW], compact('addon','addonCategories', 'taxVats', 'productWiseTax', 'language', 'taxVatIds'))->render(),
        ]);
    }

    public function update(AddonUpdateRequest $request, $id): RedirectResponse
    {
        $addon = $this->addonRepo->update(id: $id, data: $this->addonService->getAddData(request: $request));


            if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'product_wise') {
                $addon->taxVats()->delete();
                foreach ($request['tax_ids'] ?? [] as $tax_id) {
                    \Modules\TaxModule\Entities\Taxable::create(
                        [
                            'taxable_type' => ModelsAddOn::class,
                            'taxable_id' => $addon->id,
                            'system_tax_setup_id' => $SystemTaxVat->id,
                            'tax_id' => $tax_id
                        ],
                    );
                }
            }
        }


        $this->translationRepo->updateByModel(request: $request, model: $addon, modelPath: 'App\Models\AddOn', attribute: 'name');
        Toastr::success(translate('messages.addon_updated_successfully'));
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->addonRepo->delete(id: $request['id']);
        Toastr::success(translate('messages.addon_deleted_successfully'));
        return back();
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $this->addonRepo->update(id: $request['id'], data: ['status' => $request['status']]);
        Toastr::success(translate('messages.addon_status_updated'));
        return back();
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $storeId = $request->query('store_id', 'all');
        $addons = $this->addonRepo->getExportList(
            moduleId: Config::get('module.current_module_id'),
            searchValue: $request['search'],
            storeId: $storeId
        );
        $store = $storeId != 'all' ? $this->storeRepo->getFirstWhere(params: ['id' => $storeId]) : null;

        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];

        $data = [
            'data' => $addons,
            'search' => $request['search'] ?? null,
            'store' => $store,
            'productWiseTax' => $productWiseTax
        ];

        if ($request['type'] == 'csv') {
            return Excel::download(new AddonExport($data), Addon::EXPORT_CSV);
        }
        return Excel::download(new AddonExport($data), Addon::EXPORT_XLSX);
    }

    public function getBulkImportView(): View
    {
        return view(AddonViewPath::BULK_IMPORT['view']);
    }

    public function importBulkData(AddonBulkImportRequest $request): RedirectResponse
    {
        $data = $this->addonService->getImportData(request: $request);

        if (array_key_exists('flag', $data) && $data['flag'] == 'wrong_format') {
            Toastr::error(translate('messages.you_have_uploaded_a_wrong_format_file'));
            return back();
        }

        if (array_key_exists('flag', $data) && $data['flag'] == 'required_fields') {
            Toastr::error(translate('messages.please_fill_all_required_fields'));
            return back();
        }

        if (array_key_exists('flag', $data) && $data['flag'] == 'price_range') {
            Toastr::error(translate('messages.Price_must_be_greater_then_0'));
            return back();
        }

        try {
            DB::beginTransaction();
            $this->addonRepo->addByChunk(data: $data);
            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            Toastr::error(translate('messages.failed_to_import_data'));
            return back();
        }

        Toastr::success(translate('messages.category_imported_successfully', ['count' => count($data)]));
        return back();
    }
    public function updateBulkData(AddonBulkImportRequest $request): RedirectResponse
    {
        $data = $this->addonService->getImportData(request: $request, toAdd: false);

        if (array_key_exists('flag', $data) && 'wrong_format' == $data['flag']) {
            Toastr::error(translate('messages.you_have_uploaded_a_wrong_format_file'));
            return back();
        }

        if (array_key_exists('flag', $data) && $data['flag'] == 'required_fields') {
            Toastr::error(translate('messages.please_fill_all_required_fields'));
            return back();
        }

        if (array_key_exists('flag', $data) && $data['flag'] == 'price_range') {
            Toastr::error(translate('messages.Price_must_be_greater_then_0'));
            return back();
        }

        try {
            DB::beginTransaction();
            $this->addonRepo->updateByChunk(data: $data);
            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            Toastr::error(translate('messages.failed_to_import_data'));
            return back();
        }

        Toastr::success(translate('messages.category_imported_successfully', ['count' => count($data)]));
        return back();
    }
    public function getBulkExportView(): View
    {
        return view(AddonViewPath::BULK_EXPORT['view']);
    }

    /**
     * @throws WriterNotOpenedException
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws InvalidArgumentException
     */
    public function exportBulkData(AddonBulkExportRequest $request): StreamedResponse|string
    {
        $categories = $this->addonRepo->getBulkExportList(request: $request);
        return (new FastExcel($this->addonService->getBulkExportData(collection: $this->exportGenerator(data: $categories))))->download(Addon::EXPORT_XLSX);
    }
}
