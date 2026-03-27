<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Contracts\Repositories\CustomRoleRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\CustomRole as CustomRoleViewPath;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\CustomRoleAddRequest;
use App\Http\Requests\Admin\CustomRoleUpdateRequest;
use App\Services\CustomRoleService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class CustomRoleController extends BaseController
{
    public function __construct(
        protected CustomRoleRepositoryInterface $roleRepo,
        protected CustomRoleService $roleService,
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
        $roles = $this->roleRepo->getListWhere(
            searchValue: request()?->search,
            dataLimit: config('default_pagination')
        );
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(CustomRoleViewPath::ADD[VIEW], compact('roles','language','defaultLang'));
    }

    public function add(CustomRoleAddRequest $request): RedirectResponse
    {
        $role = $this->roleRepo->add(data: $this->roleService->getAddData(request: $request));
        $this->translationRepo->addByModel(request: $request, model: $role, modelPath: 'App\Models\AdminRole', attribute: 'name');
        Toastr::success(translate('messages.role_added_successfully'));
        return back();
    }

    public function getUpdateView(string|int $id): View
    {
        $data = $this->roleService->roleCheck(role: $id);

        if (array_key_exists('flag', $data) && $data['flag'] == 'unauthorized') {
            return view('errors.404');
        }
        $role = $this->roleRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(CustomRoleViewPath::UPDATE[VIEW], compact('role','language','defaultLang'));
    }

    public function update(CustomRoleUpdateRequest $request, $id): RedirectResponse|View
    {
        $data = $this->roleService->roleCheck(role: $id);

        if (array_key_exists('flag', $data) && $data['flag'] == 'unauthorized') {
            return view('errors.404');
        }

        $role = $this->roleRepo->update(id: $id ,data: $this->roleService->getAddData(request: $request));
        $this->translationRepo->updateByModel(request: $request, model: $role, modelPath: 'App\Models\AdminRole', attribute: 'name');
        Toastr::success(translate('messages.role_updated_successfully'));
        return redirect()->route('admin.users.custom-role.create');
    }

    public function delete($id): RedirectResponse|View
    {
        $data = $this->roleService->roleCheck(role: $id);

        if (array_key_exists('flag', $data) && $data['flag'] == 'unauthorized') {
            return view('errors.404');
        }
        $this->roleRepo->delete(id: $id);
        Toastr::success(translate('messages.role_deleted_successfully'));
        return back();
    }

    public function search(Request $request): JsonResponse
    {
        $roles=$this->roleRepo->getSearchList($request);
        return response()->json([
            'view'=>view(CustomRoleViewPath::SEARCH[VIEW],compact('roles'))->render(),
            'count'=>$roles->count()
        ]);
    }
}
