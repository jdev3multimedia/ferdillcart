<?php

namespace App\Http\Controllers\Admin\Banner;

use App\Contracts\Repositories\BannerRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Contracts\Repositories\ZoneRepositoryInterface;
use App\Enums\ViewPaths\Admin\Banner as BannerViewPath;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\BannerAddRequest;
use App\Http\Requests\Admin\BannerUpdateRequest;
use App\Services\BannerService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class BannerController extends BaseController
{
    public function __construct(
        protected BannerRepositoryInterface $bannerRepo,
        protected BannerService $bannerService,
        protected TranslationRepositoryInterface $translationRepo,
        protected ZoneRepositoryInterface $zoneRepo
    )
    {
    }

    public function index(?Request $request): View|Collection|LengthAwarePaginator|null
    {
        return $this->getAddView();
    }

    private function getAddView(): View
    {
        $banners = $this->bannerRepo->getListWhere(
            filters: ['module_id'=>Config::get('module.current_module_id'), 'created_by'=>'admin'],
            relations: ['module'],
            searchValue: request()->search,
            dataLimit: config('default_pagination')
        );
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        $zones = $this->zoneRepo->getList();
        return view(BannerViewPath::INDEX[VIEW], compact('banners','language','defaultLang','zones'));
    }

    public function add(BannerAddRequest $request): JsonResponse
    {
        $banner = $this->bannerRepo->add(data: $this->bannerService->getAddData(request: $request));
        $this->translationRepo->addByModel(request: $request, model: $banner, modelPath: 'App\Models\Banner', attribute: 'title');

        return response()->json();
    }

    public function getUpdateView(string|int $id): View
    {
        $banner = $this->bannerRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        $zones = $this->zoneRepo->getList();
        return view(BannerViewPath::UPDATE[VIEW], compact('banner','language','defaultLang','zones'));
    }

    public function update(BannerUpdateRequest $request, $id): JsonResponse
    {
        $banner = $this->bannerRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $banner = $this->bannerRepo->update(id: $id ,data: $this->bannerService->getUpdateData(request: $request,banner: $banner));
        $this->translationRepo->updateByModel(request: $request, model: $banner, modelPath: 'App\Models\Banner', attribute: 'title');

        return response()->json();
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->bannerRepo->delete(id: $request['id']);
        Toastr::success(translate('messages.banner_deleted_successfully'));
        return back();
    }


    public function getSearchList(Request $request): JsonResponse
    {
        $banners = $this->bannerRepo->getSearchedList(
            searchValue: $request['search'],
            dataLimit: 50
        );

        return response()->json([
            'view'=>view(BannerViewPath::SEARCH[VIEW],compact('banners'))->render(),
            'count'=>$banners->count()
        ]);
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $this->bannerRepo->update(id: $request['id'] ,data: ['status'=>$request['status']]);
        Toastr::success(translate('messages.banner_status_updated'));
        return back();
    }

    public function updateFeatured(Request $request): RedirectResponse
    {
        $this->bannerRepo->update(id: $request['id'] ,data: ['featured'=>$request['status']]);
        Toastr::success(translate('messages.banner_featured_status_updated'));
        return back();
    }
}
