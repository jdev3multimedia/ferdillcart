<?php

namespace App\Http\Controllers\Admin\Module;

use App\Contracts\Repositories\ModuleRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ExportFileNames\Admin\Module;
use App\Enums\ViewPaths\Admin\Module as ModuleViewPath;
use App\Exports\ModuleExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ModuleAddRequest;
use App\Http\Requests\Admin\ModuleUpdateRequest;
use App\Services\ModuleService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ModuleController extends BaseController
{
    public function __construct(
        protected ModuleRepositoryInterface $moduleRepo,
        protected ModuleService $moduleService,
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
        $modules = $this->moduleRepo->getListWhere(
            searchValue: $request['search'],
            filters: ($request['module_type'] && $request['module_type'] != 'all')?['module_type' => $request['module_type']]:[],
            relations: ['stores'],
            dataLimit: config('default_pagination')
        );
        return view(ModuleViewPath::INDEX[VIEW], compact('modules'));
    }

    public function add(ModuleAddRequest $request): RedirectResponse
    {
        $module = $this->moduleRepo->add(data: $this->moduleService->getAddData(request: $request));
        $this->translationRepo->addByModel(request: $request, model: $module, modelPath: 'App\Models\Module', attribute: 'module_name');
        $this->translationRepo->addByModel(request: $request, model: $module, modelPath: 'App\Models\Module', attribute: 'description');

        Toastr::success(translate('messages.module_created_successfully'));
        return back();
    }

    public function getAddView(): View
    {
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(ModuleViewPath::ADD[VIEW], compact('language','defaultLang'));
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        if(env('APP_MODE')=='demo' && in_array($id, [1,2,3,4,5]))
        {
            Toastr::warning(translate('messages.you_can_not_edit_this_module_please_add_a_new_module_to_edit'));
            return back();
        }

        $module = $this->moduleRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(ModuleViewPath::UPDATE[VIEW], compact('module','language','defaultLang'));
    }

    public function update(ModuleUpdateRequest $request, $id): RedirectResponse
    {
        if(env('APP_MODE')=='demo' && in_array($id, [1,2,3,4,5]))
        {
            Toastr::warning(translate('messages.you_can_not_edit_this_module_please_add_a_new_module_to_edit'));
            return back();
        }
        $module = $this->moduleRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $module = $this->moduleRepo->update(id: $id ,data: $this->moduleService->getUpdateData(request: $request,module: $module));
        $this->translationRepo->updateByModel(request: $request, model: $module, modelPath: 'App\Models\Module', attribute: 'module_name');
        $this->translationRepo->updateByModel(request: $request, model: $module, modelPath: 'App\Models\Module', attribute: 'description');

        Toastr::success(translate('messages.module_updated_successfully'));
        return back();
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $this->moduleRepo->update(id: $request['id'] ,data: ['status'=>$request['status']]);
        Toastr::success(translate('messages.module_status_updated'));
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->moduleRepo->delete(id: $request['id']);
        Toastr::success(translate('messages.module_deleted_successfully'));
        return back();
    }

    public function show($id): JsonResponse
    {
        $module = $this->moduleRepo->getFirstWhere(params: ['id' => $id]);
        return response()->json(['data'=>config('module.'.$module['module_type']),'type'=>$module['module_type']]);
    }

    public function getType(Request $request): JsonResponse
    {
        return response()->json(['data'=>config('module.'.$request['module_type'])]);
    }

    public function search(Request $request): JsonResponse
    {
        $modules = $this->moduleRepo->getSearchListWhere(
            searchValue: $request['search'],
            relations: ['stores'],
            dataLimit: 50
        );
        return response()->json([
            'view'=>view(ModuleViewPath::SEARCH[VIEW],compact('modules'))->render(),
            'count'=>$modules->count()
        ]);
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $collection = $this->moduleRepo->getExportList($request);

        $data=[
            'data' =>$collection,
            'search' =>$request['search'] ?? null,
        ];
        if($request['type'] == 'csv'){
            return Excel::download(new ModuleExport($data), Module::EXPORT_CSV);
        }
        return Excel::download(new ModuleExport($data), Module::EXPORT_XLSX);
    }

}
