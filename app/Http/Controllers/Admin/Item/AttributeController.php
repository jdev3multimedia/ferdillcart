<?php

namespace App\Http\Controllers\Admin\Item;

use App\Contracts\Repositories\AttributeRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Attribute as AttributeViewPath;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AttributeAddRequest;
use App\Services\AttributeService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Exports\AttributesExport;
use App\Http\Requests\Admin\AttributeUpdateRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttributeController extends BaseController
{
    public function __construct(
        protected AttributeRepositoryInterface $attributeRepo,
        protected AttributeService $attributeService,
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
        $attributes = $this->attributeRepo->getListWhere(
            searchValue: $request['search'],
            dataLimit: config('default_pagination')
        );
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(AttributeViewPath::INDEX[VIEW], compact('attributes','language','defaultLang'));
    }

    public function add(AttributeAddRequest $request): RedirectResponse
    {
        $attribute = $this->attributeRepo->add(data: $this->attributeService->getAddData(request: $request));
        $this->translationRepo->addByModel(request: $request, model: $attribute, modelPath: 'App\Models\Attribute', attribute: 'name');
        Toastr::success(translate('messages.attribute_added_successfully'));
        return back();
    }

    public function getUpdateView(string|int $id): View
    {
        $attribute = $this->attributeRepo->getFirstWithoutGlobalScopeWhere(params: ['id' => $id]);
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view(AttributeViewPath::UPDATE[VIEW], compact('attribute','language','defaultLang'));
    }

    public function update(AttributeUpdateRequest $request, $id): RedirectResponse
    {
        $attribute = $this->attributeRepo->update(id: $id ,data: $this->attributeService->getAddData(request: $request));
        $this->translationRepo->updateByModel(request: $request, model: $attribute, modelPath: 'App\Models\Attribute', attribute: 'name');
        Toastr::success(translate('messages.attribute_updated_successfully'));
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->attributeRepo->delete(id: $request['id']);
        Toastr::success(translate('messages.attribute_deleted_successfully'));
        return back();
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $attributes = $this->attributeRepo->getExportList(request: $request);
        $data=[
            'data' =>$attributes,
            'search' =>$request['search'] ?? null,
        ];

        if($request['type'] == 'csv'){
            return Excel::download(new AttributesExport($data), 'Attributes.csv');
        }
        return Excel::download(new AttributesExport($data), 'Attributes.xlsx');
    }
}
