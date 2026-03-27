<?php

namespace App\Http\Controllers\Admin\Coupon;

use App\Contracts\Repositories\CouponRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Contracts\Repositories\ZoneRepositoryInterface;
use App\Enums\ExportFileNames\Admin\Coupon;
use App\Enums\ViewPaths\Admin\Coupon as CouponViewPath;
use App\Exports\CouponExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\CouponAddRequest;
use App\Http\Requests\Admin\CouponUpdateRequest;
use App\Models\Coupon as ModelsCoupon;
use App\Models\User;
use App\Models\Zone;
use App\Services\CouponService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CouponController extends BaseController
{
    public function __construct(
        protected CouponRepositoryInterface $couponRepo,
        protected CouponService $couponService,
        protected TranslationRepositoryInterface $translationRepo,
        protected ZoneRepositoryInterface $zoneRepo
    )
    {
    }

    public function index(?Request $request): View|Collection|LengthAwarePaginator|null
    {
        return $this->getAddView($request);
    }

    private function getAddView($request): View
    {
        $coupons = $this->couponRepo->getListWhere(
            searchValue: $request['search'],
            filters: ['created_by'=>'admin','module_id'=>Config::get('module.current_module_id')],
            relations: ['module'],
            dataLimit: config('default_pagination'),
        );
        $customer = $request['customer'];
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        $zones = $this->zoneRepo->getList();
        return view(CouponViewPath::INDEX[VIEW], compact('coupons','language','defaultLang','zones','customer'));
    }

    public function add(CouponAddRequest $request): RedirectResponse
    {
        $coupon = $this->couponRepo->add(data: $this->couponService->getAddData(request: $request,moduleId: Config::get('module.current_module_id')));
        $this->translationRepo->addByModel(request: $request, model: $coupon, modelPath: 'App\Models\Coupon', attribute: 'title');
        Toastr::success(translate('messages.coupon_added_successfully'));
        return back();
    }

    public function getUpdateView(string|int $id): View
    {
        $coupon = $this->couponRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        $zones = $this->zoneRepo->getList();
        return view(CouponViewPath::UPDATE[VIEW], compact('coupon','language','defaultLang','zones'));
    }

    public function update(CouponUpdateRequest $request, $id): RedirectResponse
    {
        $coupon = $this->couponRepo->update(id: $id ,data: $this->couponService->getAddData(request: $request, moduleId: Config::get('module.current_module_id')));
        $this->translationRepo->updateByModel(request: $request, model: $coupon, modelPath: 'App\Models\Coupon', attribute: 'title');
        Toastr::success(translate('messages.coupon_updated_successfully'));
        return back();
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $this->couponRepo->update(id: $request['id'] ,data: ['status'=>$request['status']]);
        Toastr::success(translate('messages.coupon_status_updated'));
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->couponRepo->delete(id: $request['id']);
        Toastr::success(translate('messages.coupon_deleted_successfully'));
        return back();
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $coupons = $this->couponRepo->getExportList($request);
        $data=[
            'data' =>$coupons,
            'search' =>$request['search'] ?? null
        ];
        if($request['type'] == 'csv'){
            return Excel::download(new CouponExport($data), Coupon::EXPORT_CSV);
        }
        return Excel::download(new CouponExport($data), Coupon::EXPORT_XLSX);
    }
    public function viewCoupon($id){
        $coupon = $this->couponRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id],relations:['store:id,name']);
        $selectedCustomers=json_decode($coupon->customer_id);
        if(in_array("all", $selectedCustomers)){
            $selectedCustomers='all';
        } else {
            $selectedCustomers=  User::withoutGlobalScopes()->whereIn('id', $selectedCustomers)->select('id','f_name','l_name')->get();
        }
        $zoneData = [];
        if($coupon->coupon_type=='zone_wise'){
           $zoneData = Zone::whereIn('id', json_decode($coupon->data))->select('id','name')->get();
        }
          return response()->json([
            'view' => view('admin-views.coupon._view', compact('coupon','selectedCustomers','zoneData'))->render(),
        ]);
    }
}
