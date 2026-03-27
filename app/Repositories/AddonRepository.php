<?php

namespace App\Repositories;

use App\Contracts\Repositories\AddonRepositoryInterface;
use App\Http\Requests\Admin\AddonBulkExportRequest;
use App\Models\AddOn;
use App\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AddonRepository implements AddonRepositoryInterface
{
    public function __construct(protected AddOn $addon)
    {
    }

    public function add(array $data): string|object
    {
        $addon = $this->addon->newInstance();
        foreach ($data as $key => $column) {
            $addon[$key] = $column;
        }
        $addon->save();
        return $addon;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->addon->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->addon->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->addon->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('addon', 'like', "%{$value}%");
            }
        })->limit($dataLimit)->get();
    }

    public function update(string $id, array $data): bool|string|object
    {
        $addon = $this->addon->find($id);
        foreach ($data as $key => $column) {
            $addon[$key] = $column;
        }
        $addon->save();
        return $addon;
    }

    public function delete(string $id): bool
    {
        $addon = $this->addon->withoutGlobalScope(StoreScope::class)->find($id);
        $addon->translations()->delete();
        $addon?->taxVats()->delete();
        $addon->delete();

        return true;
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->addon->withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getStoreWiseList(int|string $moduleId ,string $searchValue = null, int|string $storeId = 'all', int|string $dataLimit = DEFAULT_DATA_LIMIT): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->addon->withoutGlobalScope(StoreScope::class)
            ->when(is_numeric($storeId), function($query)use($storeId){
                return $query->where('store_id', $storeId);
            })->whereHas('store', function ($q) use ($moduleId) {
                return $q->where('module_id', $moduleId);
            })
            ->when(isset($key), function ($q1) use($key){
                $q1->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->orderBy('name')->paginate($dataLimit);
    }
    public function getExportList(int|string $moduleId ,string $searchValue = null, int|string $storeId = 'all'): Collection
    {
        $key = explode(' ', $searchValue);
        return $this->addon->withoutGlobalScope(StoreScope::class)
            ->when(is_numeric($storeId), function($query)use($storeId){
                return $query->where('store_id', $storeId);
            })->whereHas('store', function ($q) use ($moduleId) {
                return $q->where('module_id', $moduleId);
            })
            ->when(isset($key), function ($q1) use($key){
                $q1->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->orderBy('name')->get();
    }

    public function addByChunk(array $data): void
    {
        $chunkSize = 100;
        $chunkAddons= array_chunk($data,$chunkSize);

        foreach($chunkAddons as $key=> $chunkAddon){
            DB::table('add_ons')->insert($chunkAddon);
        }
    }
    public function updateByChunk(array $data): void
    {
        $chunkSize = 100;
        $chunkAddons= array_chunk($data,$chunkSize);

        foreach($chunkAddons as $key=> $chunkAddon){
            DB::table('add_ons')->upsert($chunkAddon,['id'],['name','price','store_id','status']);
        }
    }

    public function getBulkExportList(AddonBulkExportRequest $request): Collection
    {
        return $this->addon->when($request['type']=='date_wise', function($query)use($request){
            $query->whereBetween('created_at', [$request['from_date'].' 00:00:00', $request['to_date'].' 23:59:59']);
        })
            ->when($request['type']=='id_wise', function($query)use($request){
                $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
            })->whereHas('store', function ($q) use ($request) {
                return $q->where('module_id', Config::get('module.current_module_id'));
            })
            ->withoutGlobalScope(StoreScope::class)->get();
    }

}
