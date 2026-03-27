<?php

namespace App\Repositories;

use App\Contracts\Repositories\CustomRoleRepositoryInterface;
use App\Models\AdminRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomRoleRepository implements CustomRoleRepositoryInterface
{
    public function __construct(protected AdminRole $role)
    {
    }

    public function add(array $data): string|object
    {
        $role = $this->role->newInstance();
        foreach ($data as $key => $column) {
            $role[$key] = $column;
        }
        $role->save();
        return $role;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->role->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->role->whereNotIn('id', [1])->get();
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->role->whereNotIn('id',[1])
            ->when(isset($searchValue), function($query) use($key) {
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $role = $this->role->find($id);
        foreach ($data as $key => $column) {
            $role[$key] = $column;
        }
        $role->save();
        return $role;
    }

    public function delete(string $id): bool
    {
        $role = $this->role->find($id);
        $role->translations()->delete();
        $role->delete();

        return true;
    }

    public function getSearchList(Request $request): Collection
    {
        $key = explode(' ', $request['search']);
        return $this->role->where('id','!=','1')
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            })->latest()->limit(50)->get();
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->role->withoutGlobalScope('translate')->where($params)->first(['id','name','modules']);
    }
}
