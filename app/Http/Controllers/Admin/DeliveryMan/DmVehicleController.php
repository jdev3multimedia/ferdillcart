<?php

namespace App\Http\Controllers\Admin\DeliveryMan;

use App\Contracts\Repositories\DeliveryManRepositoryInterface;
use App\Contracts\Repositories\DmVehicleRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\DmVehicle as DmVehicleViewPath;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\DmVehicleAddRequest;
use App\Http\Requests\Admin\DmVehicleUpdateRequest;
use App\Services\DmVehicleService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class DmVehicleController extends BaseController
{
    public function __construct(
        protected DmVehicleRepositoryInterface $vehicleRepo,
        protected DmVehicleService $vehicleService,
        protected TranslationRepositoryInterface $translationRepo
    )
    {
    }

    public function index(?Request $request): View|Collection|LengthAwarePaginator|null
    {
        return $this->getListView($request);
    }

    public function getAddView(): View
    {
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(DmVehicleViewPath::ADD[VIEW], compact('language','defaultLang'));
    }

    private function getListView(Request $request): View
    {
        $vehicles = $this->vehicleRepo->getListWhereWithCount(
            withCountRelations: ['delivery_man'],
            searchValue: $request['search'],
            dataLimit: config('default_pagination')
        );
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(DmVehicleViewPath::INDEX[VIEW], compact('vehicles','language','defaultLang'));
    }

    public function add(DmVehicleAddRequest $request): JsonResponse
    {
        $temp = $this->vehicleRepo->getExistFirst(
            params: [
                'starting_coverage_area' => $request['starting_coverage_area'],
                'maximum_coverage_area' => $request['maximum_coverage_area']
            ]
        );
        if (isset($temp)) {
            return response()->json(['errors' => [
                ['code' => 'Vehicle_overlapped', 'message' => translate('messages.Coverage_area_overlapped')]
            ]]);
        }
        $vehicle = $this->vehicleRepo->add(data: $this->vehicleService->getAddData(request: $request));
        $this->translationRepo->addByModel(request: $request, model: $vehicle, modelPath: 'App\Models\DMVehicle', attribute: 'type');

        return response()->json(['success' => translate('messages.Vehicle_category_created')]);
    }

    public function getUpdateView(string|int $id): View
    {
        $vehicle = $this->vehicleRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(DmVehicleViewPath::UPDATE[VIEW], compact('vehicle','language','defaultLang'));
    }

    public function update(DmVehicleUpdateRequest $request, $id): JsonResponse
    {
        $temp = $this->vehicleRepo->getExistFirst(
            params: [
                'starting_coverage_area' => $request['starting_coverage_area'],
                'maximum_coverage_area' => $request['maximum_coverage_area']
            ],
            id: $id
        );
        if (isset($temp)) {
            return response()->json(['errors' => [
                ['code' => 'Vehicle_overlapped', 'message' => translate('messages.Coverage_area_overlapped')]
            ]]);
        }
        $vehicle = $this->vehicleRepo->update(id: $id ,data: $this->vehicleService->getUpdateData(request: $request));
        $this->translationRepo->updateByModel(request: $request, model: $vehicle, modelPath: 'App\Models\DMVehicle', attribute: 'type');

        return response()->json(['success' => translate('messages.Vehicle_category_updated')]);
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->vehicleRepo->delete(id: $request['id']);
        Toastr::success(translate('messages.vehicle_removed'));
        return back();
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $this->vehicleRepo->update(id: $request['id'] ,data: ['status'=>$request['status']]);
        Toastr::success(translate('messages.Vehicle_status_updated'));
        return back();
    }

    public function getDetailsView(string|int $id, Request $request, DeliveryManRepositoryInterface $deliveryManRepo): View
    {
        $vehicle = $this->vehicleRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $deliveryMen = $deliveryManRepo->getListWhere(
            searchValue: $request['search'],
            filters: ['vehicle_id'=>$vehicle['id']],
            relations: ['vehicle','rating','orders'],
            dataLimit: config('default_pagination')
        );
        return view(DmVehicleViewPath::VIEW[VIEW], compact('vehicle','deliveryMen') );
    }
}
