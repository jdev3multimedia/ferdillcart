<?php

namespace App\Repositories;

use App\Contracts\Repositories\DmVehicleRepositoryInterface;
use App\Models\DMVehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DmVehicleRepository implements DmVehicleRepositoryInterface
{
    public function __construct(protected DMVehicle $vehicle)
    {
    }

    public function add(array $data): string|object
    {
        $vehicle = $this->vehicle->newInstance();
        foreach ($data as $key => $column) {
            $vehicle[$key] = $column;
        }
        $vehicle->save();
        return $vehicle;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->vehicle->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->vehicle->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->vehicle->with($relations)->where($filters)
            ->when(isset($key), function($query) use($key){
                $query->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->where('type', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate($dataLimit);
    }
    public function getListWhereWithCount(string $searchValue = null, array $filters = [], array $relations = [], array $withCountRelations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->vehicle->with($relations)->withcount($withCountRelations)->where($filters)
            ->when(isset($key), function($query) use($key){
                $query->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->where('type', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $vehicle = $this->vehicle->find($id);
        foreach ($data as $key => $column) {
            $vehicle[$key] = $column;
        }
        $vehicle->save();
        return $vehicle;
    }

    public function delete(string $id): bool
    {
        $vehicle = $this->vehicle->find($id);
        $vehicle->translations()->delete();
        $vehicle->delete();

        return true;
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->vehicle->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getSearchedList(string $searchValue = null, int|string $dataLimit = DEFAULT_DATA_LIMIT): Collection
    {
        $key = explode(' ', $searchValue);
        return $this->vehicle->where(function ($query) use ($key) {
            foreach ($key as $value) {
                $query->orWhere('title', 'like', "%{$value}%");
            }
        })->limit($dataLimit)->get();
    }

    public function getExistFirst(array $params, string $id = null): ?Model
    {
        $startingCoverageArea = $params['starting_coverage_area'];
        $maximumCoverageArea = $params['maximum_coverage_area'];

        return $this->vehicle->where('id' ,'!=', $id)
            ->when(isset($id), function($query) use($id){
                $query->where('id' ,'!=', $id);
            })
            ->where(function ($query) use ($startingCoverageArea,$maximumCoverageArea ){
                $query->where(function ($query) use ($startingCoverageArea) {
                    $query->where('starting_coverage_area', '<=', $startingCoverageArea)->where('maximum_coverage_area', '>=', $startingCoverageArea);
                })->orWhere(function ($query) use ($maximumCoverageArea) {
                    $query->where('starting_coverage_area', '<=', $maximumCoverageArea)->where('maximum_coverage_area', '>=', $maximumCoverageArea);
                })->orWhere(function ($query) use ($startingCoverageArea, $maximumCoverageArea) {
                    $query->where('starting_coverage_area', '>=', $startingCoverageArea)->where('maximum_coverage_area', '<=', $maximumCoverageArea);
                });
            })
            ->first();
    }
}
