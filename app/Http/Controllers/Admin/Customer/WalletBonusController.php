<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Contracts\Repositories\WalletBonusRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\WalletBonus as WalletBonusViewPath;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\WalletBonusAddRequest;
use App\Http\Requests\Admin\WalletBonusUpdateRequest;
use App\Services\WalletBonusService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class WalletBonusController extends BaseController
{
    public function __construct(
        protected WalletBonusRepositoryInterface $bonusRepo,
        protected WalletBonusService $bonusService,
        protected TranslationRepositoryInterface $translationRepo
    )
    {
    }

    public function index(?Request $request): View|Collection|LengthAwarePaginator|null
    {
        return $this->getAddView();
    }

    private function getAddView(): View
    {
        $bonuses = $this->bonusRepo->getListWhere(
            dataLimit: config('default_pagination')
        );
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(WalletBonusViewPath::INDEX[VIEW], compact('bonuses','language','defaultLang'));
    }

    public function add(WalletBonusAddRequest $request): RedirectResponse
    {
        $bonus = $this->bonusRepo->add(data: $this->bonusService->getAddData(request: $request));
        $this->translationRepo->addByModel(request: $request, model: $bonus, modelPath: 'App\Models\WalletBonus', attribute: 'title');
        $this->translationRepo->addByModel(request: $request, model: $bonus, modelPath: 'App\Models\WalletBonus', attribute: 'description');

        Toastr::success(translate('messages.bonus_added_successfully'));
        return back();
    }

    public function getUpdateView(string|int $id): View
    {
        $bonus = $this->bonusRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(WalletBonusViewPath::UPDATE[VIEW], compact('bonus','language','defaultLang'));
    }

    public function update(WalletBonusUpdateRequest $request, $id): RedirectResponse
    {
        $bonus = $this->bonusRepo->update(id: $id ,data: $this->bonusService->getUpdateData(request: $request));
        $this->translationRepo->updateByModel(request: $request, model: $bonus, modelPath: 'App\Models\WalletBonus', attribute: 'title');
        $this->translationRepo->updateByModel(request: $request, model: $bonus, modelPath: 'App\Models\WalletBonus', attribute: 'description');

        Toastr::success(translate('messages.bonus_updated_successfully'));
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->bonusRepo->delete(id: $request['id']);
        Toastr::success(translate('messages.bonus_deleted_successfully'));
        return back();
    }


    public function getSearchList(Request $request): JsonResponse
    {
        $bonuses = $this->bonusRepo->getSearchedList(
            searchValue: $request['search'],
            dataLimit: 50
        );

        return response()->json([
            'view'=>view(WalletBonusViewPath::SEARCH[VIEW],compact('bonuses'))->render(),
            'count'=>$bonuses->count()
        ]);
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $this->bonusRepo->update(id: $request['id'] ,data: ['status'=>$request['status']]);
        Toastr::success(translate('messages.bonus_status_updated'));
        return back();
    }
}
