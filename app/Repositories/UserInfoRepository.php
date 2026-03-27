<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserInfoRepositoryInterface;
use App\Models\UserInfo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class UserInfoRepository implements UserInfoRepositoryInterface
{
    public function __construct(protected UserInfo $userInfo)
    {
    }

    public function add(array $data): string|object
    {
        $userInfo = $this->userInfo->newInstance();
        foreach ($data as $key => $column) {
            $userInfo[$key] = $column;
        }
        $userInfo->save();
        return $userInfo;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->userInfo->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->userInfo->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->userInfo->where(function ($query) use ($key) {
            foreach ($key as $value) {
                $query->orWhere('name', 'like', "%{$value}%");
            }
        })->limit($dataLimit)->get();
    }

    public function update(string $id, array $data): bool|string|object
    {
        $userInfo = $this->userInfo->find($id);
        foreach ($data as $key => $column) {
            $userInfo[$key] = $column;
        }
        $userInfo->save();
        return $userInfo;
    }

    public function delete(string $id): bool
    {
        $userInfo = $this->userInfo->find($id);
        $userInfo->translations()->delete();
        $userInfo->delete();

        return true;
    }
}
