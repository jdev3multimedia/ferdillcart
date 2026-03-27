<?php

namespace App\Repositories;

use App\Contracts\Repositories\WalletBonusRepositoryInterface;
use App\Models\WalletBonus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class WalletBonusRepository implements WalletBonusRepositoryInterface
{
    public function __construct(protected WalletBonus $bonus)
    {
    }

    public function add(array $data): string|object
    {
        $bonus = $this->bonus->newInstance();
        foreach ($data as $key => $column) {
            $bonus[$key] = $column;
        }
        $bonus->save();
        return $bonus;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->bonus->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->bonus->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->bonus->with($relations)->where($filters)->latest('end_date')->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $bonus = $this->bonus->find($id);
        foreach ($data as $key => $column) {
            $bonus[$key] = $column;
        }
        $bonus->save();
        return $bonus;
    }

    public function delete(string $id): bool
    {
        $bonus = $this->bonus->find($id);
        $bonus->translations()->delete();
        $bonus->delete();

        return true;
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->bonus->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getSearchedList(string $searchValue = null, int|string $dataLimit = DEFAULT_DATA_LIMIT): Collection
    {
        $key = explode(' ', $searchValue);
        return $this->bonus->where(function ($query) use ($key) {
            foreach ($key as $value) {
                $query->orWhere('title', 'like', "%{$value}%");
            }
        })->limit($dataLimit)->get();
    }
}
