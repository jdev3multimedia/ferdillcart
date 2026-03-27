<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Contracts\Repositories\CustomRoleRepositoryInterface;
use App\Contracts\Repositories\EmployeeRepositoryInterface;
use App\Contracts\Repositories\ZoneRepositoryInterface;
use App\Enums\ExportFileNames\Admin\Employee;
use App\Enums\ViewPaths\Admin\Employee as EmployeeViewPath;
use App\Exports\EmployeeListExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\EmployeeAddRequest;
use App\Http\Requests\Admin\EmployeeUpdateRequest;
use App\Services\EmployeeService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeController extends BaseController
{
    public function __construct(
        protected EmployeeRepositoryInterface $employeeRepo,
        protected CustomRoleRepositoryInterface $roleRepo,
        protected EmployeeService $employeeService,
        protected ZoneRepositoryInterface $zoneRepo,
    )
    {
    }

    public function index(?Request $request): View|Collection|LengthAwarePaginator|null
    {
        return $this->getListView($request);
    }

    public function getAddView(): View
    {
        $roles = $this->roleRepo->getList();
        $zones = $this->zoneRepo->getList();
        return view(EmployeeViewPath::ADD[VIEW], compact('roles','zones'));
    }
    private function getListView(Request $request): View
    {
        $zoneId = $request->query('zone_id', 'all');
        $employees = $this->employeeRepo->getZoneWiseListWhere(searchValue: $request['search'],
        relations:['role'],
        zoneId: $zoneId,
        dataLimit: config('default_pagination'));
        return view(EmployeeViewPath::INDEX[VIEW], compact('employees'));
    }

    public function add(EmployeeAddRequest $request): RedirectResponse
    {
        $this->employeeRepo->add(data: $this->employeeService->getAddData(request: $request));

        Toastr::success(translate('messages.employee_added_successfully'));
        return redirect()->route('admin.users.employee.list');
    }

    public function getUpdateView(string|int $id): RedirectResponse|View
    {
        $employee = $this->employeeRepo->getFirstWhereExceptAdmin(params: ['id' => $id]);
        $roles = $this->roleRepo->getList();
        $data = $this->employeeService->adminCheck(employee: $employee);
        $zones = $this->zoneRepo->getList();

        if (array_key_exists('flag', $data) && $data['flag'] == 'unauthorized') {
            return view(EmployeeViewPath::UPDATE[VIEW], compact('roles', 'employee','zones'));
        }

        Toastr::warning(translate('messages.access_denied'));
        return back();
    }

    public function update(EmployeeUpdateRequest $request, $id): RedirectResponse|View
    {
        $employee = $this->employeeRepo->getFirstWhereExceptAdmin(params: ['id' => $id]);

        $this->employeeRepo->update(id: $id ,data: $this->employeeService->getUpdateData(request: $request,employee: $employee));

        Toastr::success(translate('messages.employee_updated_successfully'));
        return back();
    }

    public function delete($id): RedirectResponse|View
    {
        $this->employeeRepo->delete(id: $id);
        Toastr::success(translate('messages.employee_deleted_successfully'));
        return back();
    }

    public function search(Request $request): JsonResponse
    {
        $employees=$this->employeeRepo->getSearchList($request);
        return response()->json([
            'view'=>view(EmployeeViewPath::SEARCH[VIEW],compact('employees'))->render(),
            'count'=>$employees->count()
        ]);
    }

    public function getSearchList(Request $request): JsonResponse
    {

        $employees = $this->employeeRepo->getSearchList(request: $request);

        return response()->json([
            'view'=>view(EmployeeViewPath::SEARCH[VIEW],compact('employees'))->render(),
            'count'=>$employees->count()
        ]);
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $employees = $this->employeeRepo->getExportList(request: $request);

        $data=[
            'employees' =>$employees,
            'search' =>$request['search'] ?? null,
        ];

        if($request['type'] == 'csv'){
            return Excel::download(new EmployeeListExport($data), Employee::EXPORT_CSV);
        }
        return Excel::download(new EmployeeListExport($data), Employee::EXPORT_XLSX);
    }
}
