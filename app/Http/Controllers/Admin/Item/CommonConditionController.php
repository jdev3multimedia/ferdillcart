<?php

namespace App\Http\Controllers\Admin\Item;

use App\Contracts\Repositories\CommonConditionRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\CommonCondition as CommonConditionViewPath;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\CommonConditionAddRequest;
use App\Services\CommonConditionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class CommonConditionController extends BaseController
{
    public function __construct(
        protected CommonConditionRepositoryInterface $conditionRepo,
        protected CommonConditionService $conditionService,
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
        $conditions = $this->conditionRepo->getListWhere(
            searchValue: $request['search'],
            dataLimit: config('default_pagination')
        );
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(CommonConditionViewPath::INDEX[VIEW], compact('conditions','language','defaultLang'));
    }

    public function add(CommonConditionAddRequest $request): RedirectResponse
    {
        $condition = $this->conditionRepo->add(data: $this->conditionService->getAddData(request: $request));
        $this->translationRepo->addByModel(request: $request, model: $condition, modelPath: 'App\Models\CommonCondition', attribute: 'name');
        Toastr::success(translate('messages.condition_added_successfully'));
        return back();
    }

    public function getUpdateView(string|int $id): View
    {
        $condition = $this->conditionRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(CommonConditionViewPath::UPDATE[VIEW], compact('condition','language','defaultLang'));
    }

    public function update(CommonConditionAddRequest $request, $id): RedirectResponse
    {
        $condition = $this->conditionRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $condition = $this->conditionRepo->update(id: $id ,data: $this->conditionService->getUpdateData(request: $request,condition: $condition));
        $this->translationRepo->updateByModel(request: $request, model: $condition, modelPath: 'App\Models\CommonCondition', attribute: 'name');
        Toastr::success(translate('messages.condition_updated_successfully'));
        return back();
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $this->conditionRepo->update(id: $request['id'] ,data: ['status'=>$request['status']]);
        Toastr::success(translate('messages.condition_status_updated'));
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->conditionRepo->delete(id: $request['id']);
        Toastr::success(translate('messages.condition_deleted_successfully'));
        return back();
    }

    public function getDropdownList(Request $request): JsonResponse
    {
        $data = $this->conditionRepo->getDropdownList(request: $request, dataLimit: 8);
        $data = $this->conditionService->getDropdownData(data: $data, request: $request);

        return response()->json($data);
    }
}
