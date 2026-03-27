<?php

namespace App\Repositories;

use App\Contracts\Repositories\ZoneRepositoryInterface;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ZoneRepository implements ZoneRepositoryInterface
{
    public function __construct(protected Zone $zone)
    {
    }

    public function add(array $data): string|object
    {
        $zone = $this->zone->newInstance();
        foreach ($data as $key => $column) {
            $zone[$key] = $column;
        }
        $zone->save();
        return $zone;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->zone->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->zone->with($relations)->get();
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);

        return $this->zone->withCount(['stores','deliverymen'])

            ->when(isset($key) , function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->with($relations)
            ->latest()->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $zone = $this->zone->find($id);
        foreach ($data as $key => $column) {
            $zone[$key] = $column;
        }
        $zone->save();
        return $zone;
    }

    public function delete(string $id): bool
    {
        $zone = $this->zone->find($id);
        $zone->translations()->delete();
        $zone->delete();

        return true;
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->zone->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getAll(): Collection
    {
        return $this->zone->all();
    }

    public function getWithCoordinateWhere(array $params): ?Model
    {
        return $this->zone->withoutGlobalScopes()->selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->where($params)->first();
    }

    public function getExportList(Request $request): Collection
    {
        $key = explode(' ', $request['search']);
        return $this->zone->withCount(['stores','deliverymen'])
            ->when(isset($key) , function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->get();
    }

    public function getLatest(array $relations = []): ?Model
    {
        return $this->zone->with($relations)->latest()->first();
    }

    public function zoneModuleSetupUpdate(string $id, array $data, array $moduleData): bool|string|object
    {
        $zone = $this->zone->find($id);
        foreach ($data as $key => $column) {
            $zone[$key] = $column;
        }
        $zone->modules()->sync($moduleData);
        $zone->save();
        return $zone;
    }

    public function getWithCountLatest(array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->zone->withCount($relations)->latest()->paginate($dataLimit);
    }

    public function getActiveListExcept(array $params): Collection
    {
        return $this->zone->whereNot($params)->active()->get();
    }
}
