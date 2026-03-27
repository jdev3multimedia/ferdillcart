<?php

namespace App\Repositories;

use App\Contracts\Repositories\StoreRepositoryInterface;
use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class StoreRepository implements StoreRepositoryInterface
{
    public function __construct(protected Store $store)
    {
    }

    public function add(array $data): string|object
    {
        $store = $this->store->newInstance();
        foreach ($data as $key => $column) {
            $store[$key] = $column;
        }
        $store->save();
        return $store;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->store->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->store->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->store->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit($dataLimit)->get();
    }

    public function update(string $id, array $data): bool|string|object
    {
        $store = $this->store->find($id);
        foreach ($data as $key => $column) {
            $store[$key] = $column;
        }
        $store->save();
        return $store;
    }

    public function delete(string $id): bool
    {
        $store = $this->store->find($id);
        $store->translations()->delete();
        $store->delete();

        return true;
    }
}
