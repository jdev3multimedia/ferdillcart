<?php

namespace App\Repositories;

use App\CentralLogics\Helpers;
use App\Contracts\Repositories\ModuleRepositoryInterface;
use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ModuleRepository implements ModuleRepositoryInterface
{
    public function __construct(protected Module $module)
    {
    }

    public function add(array $data): string|object
    {
        $module = $this->module->newInstance();
        foreach ($data as $key => $column) {
            $module[$key] = $column;
        }
        $module->save();
        return $module;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->module->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->module->get();
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);

        return $this->module->withCount($relations)->where($filters)
            ->when(isset($key) , function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('module_name', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $module = $this->module->find($id);
        foreach ($data as $key => $column) {
            $module[$key] = $column;
        }
        $module->save();
        return $module;
    }

    public function delete(string $id): bool
    {
        $module = $this->module->find($id);
        if($module->thumbnail)
        {
       
            Helpers::check_and_delete('module/' , $module['thumbnail']);
            
        }
        $module->translations()->delete();
        $module->delete();

        return true;
    }

    public function getExportList(Request $request): Collection
    {
        $key = explode(' ', $request['search']);
        return $this->module->withCount('stores')->
        when(isset($key) , function($q) use($key){
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('module_name', 'like', "%{$value}%");
                }
            });
        })
        ->get();
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->module->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getSearchListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection
    {
        $key = explode(' ', $searchValue);

        return $this->module->when(isset($key) , function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('module_name', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->get($dataLimit);
    }
}
