<?php

namespace App\Http\Controllers\Admin\Zone;

use App\CentralLogics\Helpers;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\View\View;
use App\Exports\ZoneExport;
use Illuminate\Http\Request;
use App\Services\ZoneService;
use Illuminate\Http\JsonResponse;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\BaseController;
use App\Enums\ExportFileNames\Admin\Zone;
use App\Http\Requests\Admin\ZoneAddRequest;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\Admin\ZoneUpdateRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Enums\ViewPaths\Admin\Zone as ZoneViewPath;
use App\Http\Requests\Admin\ZoneModuleUpdateRequest;
use App\Contracts\Repositories\ZoneRepositoryInterface;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Models\Module;
use App\Models\Zone as ZoneModel;

class ZoneController extends BaseController
{
    public function __construct(
        protected ZoneRepositoryInterface $zoneRepo,
        protected ZoneService $zoneService,
        protected TranslationRepositoryInterface $translationRepo
    ) {
    }

    public function index(?Request $request): View|Collection|LengthAwarePaginator|null
    {
        return $this->getAddView($request);
    }

    private function getAddView(Request $request): View
    {
        $zones = $this->zoneRepo->getListWhere(
            searchValue: $request['search'],
            dataLimit: config('default_pagination'),
            relations: ['stores.vendor', 'modules', 'deliverymen'],
        );
        $language = getWebConfig('language');

        $config = Helpers::get_business_settings('cash_on_delivery');
        $digital_payment = Helpers::get_business_settings('digital_payment');
        $offline_payment = Helpers::get_business_settings('offline_payment_status');

        return view(ZoneViewPath::INDEX[VIEW], compact('zones', 'language', 'config', 'digital_payment', 'offline_payment'));
    }

    public function add(ZoneAddRequest $request): JsonResponse
    {
        $zoneId = $this->zoneRepo->getLatest()?->id + 1;
        $zone = $this->zoneRepo->add(data: $this->zoneService->getAddData(request: $request, zoneId: $zoneId));

        $this->translationRepo->addByModel(request: $request, model: $zone, modelPath: 'App\Models\Zone', attribute: 'name');
        $this->translationRepo->addByModel(request: $request, model: $zone, modelPath: 'App\Models\Zone', attribute: 'display_name');

        $zones = $this->zoneRepo->getListWhere(
            relations: ['stores.vendor', 'deliverymen'],
            dataLimit: config('default_pagination'),
        );

        $config = Helpers::get_business_settings('cash_on_delivery');
        $digital_payment = Helpers::get_business_settings('digital_payment');
        $offline_payment = Helpers::get_business_settings('offline_payment_status');
        return response()->json([
            'view' => view('admin-views.zone.partials._table', compact('zones', 'config', 'digital_payment', 'offline_payment'))->render(),
            'id' => $zone->id,
            'total' => $zones->count()
        ]);
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        if (env('APP_MODE') == 'demo' && $id == 1) {
            Toastr::warning(translate('messages.you_can_not_edit_this_zone_please_add_a_new_zone_to_edit'));
            return back();
        }

        $zone = $this->zoneRepo->getWithCoordinateWhere(
            params: ['id' => $id]
        );

        $area = json_decode($zone['coordinates'][0]->toJson(), true);
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());

        return view(ZoneViewPath::UPDATE[VIEW], compact(['zone', 'area', 'language', 'defaultLang']));
    }

    public function update(ZoneUpdateRequest $request, $id): RedirectResponse
    {
        $zone = $this->zoneRepo->update(id: $id, data: $this->zoneService->getUpdateData(request: $request, zoneId: $id));

        $this->translationRepo->updateByModel(request: $request, model: $zone, modelPath: 'App\Models\Zone', attribute: 'name');
        $this->translationRepo->updateByModel(request: $request, model: $zone, modelPath: 'App\Models\Zone', attribute: 'display_name');

        Toastr::success(translate('messages.zone_updated_successfully'));
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        if (env('APP_MODE') == 'demo' && $request['id'] == 1) {
            Toastr::warning(translate('messages.you_can_not_delete_this_zone_please_add_a_new_zone_to_delete'));
            return back();
        }
        $zone = $this->zoneRepo->getFirstWhere(params: ['id' => $request['id']]);
        if($zone->is_default){
            Toastr::warning('Sorry! This zone is set as default.You can not delete this zone!');
            return back();
        }
        if (Order::where('zone_id', $request['id'])->whereIn('order_status', ['pending', 'accepted', 'confirmed', 'processing', 'handover', 'picked_up'])->exists()) {
            Toastr::warning(translate('messages.you_can_not_delete_this_zone_Please_complete_the_ongoing_orders_of_this_zone'));
            return back();
        }
        $this->zoneRepo->delete(id: $request['id']);
        Toastr::success(translate('messages.zone_deleted_successfully'));
        return back();
    }

    public function exportList(Request $request, string $type): BinaryFileResponse
    {
        $collection = $this->zoneRepo->getExportList($request);
        $data = [
            'data' => $collection,
            'search' => $request['search'] ?? null,
        ];
        if ($type == 'csv') {
            return Excel::download(new ZoneExport($data), Zone::EXPORT_CSV);
        }
        return Excel::download(new ZoneExport($data), Zone::EXPORT_XLSX);
    }

    public function zoneFilter($id): RedirectResponse
    {
        if ($id == 'all') {
            if (session()->has('zone_id')) {
                session()->forget('zone_id');
            }
        } else {
            session()->put('zone_id', $id);
        }

        return back();
    }

    public function getModuleSetupView($id): View
    {
        $zone = $this->zoneRepo->getFirstWhere(
            params: ['id' => $id],
            relations: ['modules']
        );
        $cash_on_delivery = Helpers::get_business_settings('cash_on_delivery');
        $digital_payment = Helpers::get_business_settings('digital_payment');
        $offline_payment = Helpers::get_business_settings('offline_payment_status');

        return view(ZoneViewPath::MODULE_SETUP[VIEW], compact('zone', 'cash_on_delivery', 'digital_payment', 'offline_payment'));
    }

    public function getLatestModuleSetupView(): View
    {
        $zone = $this->zoneRepo->getLatest(
            relations: ['modules']
        );
        return view(ZoneViewPath::MODULE_SETUP[VIEW], compact('zone'));
    }

    public function updateModuleSetup(ZoneModuleUpdateRequest $request, $id): RedirectResponse
    {

        if (!$request->cash_on_delivery && !$request->digital_payment && !$request->offline_payment) {
            Toastr::error(translate('Please select at least one payment method'));
            return back()->withInput();
        }
        $cash_on_delivery = Helpers::get_business_settings('cash_on_delivery');
        $digital_payment = Helpers::get_business_settings('digital_payment');
        $offline_payment = Helpers::get_business_settings('offline_payment_status');

        $paymentData = [
            'cash_on_delivery' => $request->cash_on_delivery && data_get($cash_on_delivery, 'status') == 1 ? 1 : 0,
            'digital_payment' => $request->digital_payment && data_get($digital_payment, 'status') == 1 ?: 0,
            'offline_payment' => $request->offline_payment && $offline_payment == 1 ? 1 : 0
        ];
        $data = $this->zoneService->checkModuleDeliveryCharge(moduleData: $request->module_data, selectedModules: $request->module_id);

        if (!empty($data) && array_key_exists('flag', $data)) {
            $module = Module::find($data['module_id']);
            $moduleName = $module?->module_name ?? 'Unknown Module';

            if (!in_array($module?->module_type, ['parcel', 'rental'])) {
                switch ($data['flag']) {
                    case 'fixed_required':
                        Toastr::error(translate("Fixed delivery charge is required for module:") . '' . $moduleName);
                        break;

                    case 'distance_required':
                        Toastr::error(translate("Per km and minimum delivery charge are required for module:") . '' . $moduleName);
                        break;

                    case 'max_lt_min':
                        Toastr::error(translate("Maximum delivery charge must be greater than or equal to minimum for module:") . '' . $moduleName);
                        break;

                    case 'unknown_type':
                        Toastr::error(translate("Unknown delivery charge type selected for module:") . '' . $moduleName);
                        break;

                    default:
                        Toastr::error(translate("Invalid delivery charge configuration for module:") . '' . $moduleName);
                        break;
                }

                return back()->withInput();
            }
        }
        $filteredModuleData = collect($request->module_data)
            ->only($request->module_id)
            ->toArray();
        $this->zoneRepo->zoneModuleSetupUpdate(id: $id, data: $paymentData, moduleData: $filteredModuleData);

        Toastr::success(translate('messages.zone_module_updated_successfully'));
        return redirect()->route('admin.business-settings.zone.home');
    }

    public function getInstruction(): View
    {
        session()->put('zone-instruction', 1);
        $zones = $this->zoneRepo->getWithCountLatest(
            relations: ['stores', 'deliverymen'],
            dataLimit: config('default_pagination')
        );
        $language = getWebConfig('language');
        $config = Helpers::get_business_settings('cash_on_delivery');
        $digital_payment = Helpers::get_business_settings('digital_payment');
        $offline_payment = Helpers::get_business_settings('offline_payment_status');
        return view(ZoneViewPath::INDEX[VIEW], compact('zones', 'language', 'config', 'digital_payment', 'offline_payment'));
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        if (env('APP_MODE') == 'demo' && $request['id'] == 1) {
            Toastr::warning('Sorry!You can not inactive this zone!');
            return back();
        }

         $zone = $this->zoneRepo->getFirstWhere(
            params: ['id' => $request->id],
        );


        if($zone->is_default && $request->status == 0){
            Toastr::warning('Sorry! This zone is set as default.You can not inactive this zone!');
            return back();
        }

        $this->zoneRepo->update(id: $request['id'], data: ['status' => $request['status']]);
        if ($request['status'] == 0) {
            $this->clearCartByZone($request['id']);
        }
        Toastr::success(translate('messages.zone_status_updated'));
        return back();
    }

    public function updateDigitalPayment(Request $request): RedirectResponse
    {

        $zone = $this->zoneRepo->getFirstWhere(
            params: ['id' => $request['id']],
            relations: ['modules']
        );

        if (count($zone->modules) == 0) {
            Toastr::error(translate('You must connect at least one module to enable digital payment'));
            return back();
        }

        if (!$request['digital_payment'] && $zone['offline_payment'] != 1 && $zone['cash_on_delivery'] != 1) {
            Toastr::error(translate('You must enable at least one payment method'));
            return back();

        }
        $this->zoneRepo->update(id: $request['id'], data: ['digital_payment' => $request['digital_payment']]);
        Toastr::success(translate('messages.zone_digital_payment_status_updated'));
        return back();
    }

    public function updateCashOnDelivery(Request $request): RedirectResponse
    {
        $zone = $this->zoneRepo->getFirstWhere(
            params: ['id' => $request['id']],
            relations: ['modules']
        );

        if (count($zone->modules) == 0) {
            Toastr::error(translate('You must connect at least one module to enable cash on delivery'));
            return back();
        }

        if (!$request['cash_on_delivery'] && $zone['offline_payment'] != 1 && $zone['digital_payment'] != 1) {
            Toastr::error(translate('You must enable at least one payment method'));
            return back();

        }
        $this->zoneRepo->update(id: $request['id'], data: ['cash_on_delivery' => $request['cash_on_delivery']]);
        Toastr::success(translate('messages.zone_cash_on_delivery_status_updated'));
        return back();
    }

    public function updateOfflinePayment(Request $request): RedirectResponse
    {
        $zone = $this->zoneRepo->getFirstWhere(
            params: ['id' => $request['id']],
            relations: ['modules']
        );

        if (count($zone->modules) == 0) {
            Toastr::error(translate('You must connect at least one module to enable offline payment'));
            return back();
        }

        if (!$request['offline_payment'] && $zone['cash_on_delivery'] != 1 && $zone['digital_payment'] != 1) {
            Toastr::error(translate('You must enable at least one payment method'));
            return back();

        }
        $this->zoneRepo->update(id: $request['id'], data: ['offline_payment' => $request['offline_payment']]);
        Toastr::success(translate('messages.zone_offline_payment_status_updated'));
        return back();
    }

    public function getCoordinates($id): JsonResponse
    {
        $zone = $this->zoneRepo->getWithCoordinateWhere(
            params: ['id' => $id]
        );
        $area = json_decode($zone['coordinates'][0]->toJson(), true);
        $data = $this->zoneService->formatCoordinates(coordinates: $area['coordinates']);
        $center = (object) ['lat' => (float) trim(explode(' ', $zone['center'])[1], 'POINT()'), 'lng' => (float) trim(explode(' ', $zone['center'])[0], 'POINT()')];
        return response()->json(['coordinates' => $data, 'center' => $center]);
    }

    public function getAllZoneCoordinates($id = 0): JsonResponse
    {
        $zones = $this->zoneRepo->getActiveListExcept(
            params: ['id' => $id]
        );

        $data = $this->zoneService->formatZoneCoordinates(zones: $zones);

        return response()->json($data);
    }

    public function get_zone(Request $request)
    {
        $zone = Helpers::getCoordinatesZone($request->lat, $request->lng);

        return response()->json($zone);
    }

    private function clearCartByZone($zone_id)
    {
        $cart_ids = Cart::whereHas('item.store', function ($query) use ($zone_id) {
            $query->where('zone_id', $zone_id);
        })->pluck('id')->toArray();

        if (count($cart_ids) > 0) {
            Cart::destroy($cart_ids);
        }
    }

    public function checkLocation(Request $request): JsonResponse
    {
        if (! $request->filled(['latitude', 'longitude', 'zone_id'])) {
            return response()->json([
                'errors' => [
                    ['code' => 'validation', 'message' => translate('messages.Please_select_a_location_within_the_selected_zone.')]
                ]
            ], 200);
        }

        $zone = ZoneModel::query()
            ->where('id', $request->zone_id)
            ->whereContains(
                'coordinates',
                new Point($request->latitude, $request->longitude, POINT_SRID)
            )
            ->first();

        if (! $zone) {
            return response()->json([
                'errors' => [
                    [
                        'code' => 'coordinates',
                        'message' => translate('messages.Please_select_a_location_within_the_selected_zone.')
                    ]
                ]
            ], 200);
        }

        return response()->json(['status' => 'ok'], 200);
    }
    public function defaultStatus($id){

        $zone = $this->zoneRepo->getFirstWhere(
            params: ['id' => $id]
        );
        $zone->is_default = 1;
        $zone->status = 1;
        $zone->save();

        ZoneModel::where('id', '!=', $id)->update(['is_default' => 0]);
        Toastr::success(translate('messages.zone_default_status_updated'));
        return back();
    }
}

