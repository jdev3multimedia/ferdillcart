<?php

namespace App\Repositories;

use App\Contracts\Repositories\CommonConditionRepositoryInterface;
use App\Models\CommonCondition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CommonConditionRepository implements CommonConditionRepositoryInterface
{
    public function __construct(protected CommonCondition $condition)
    {
    }

    public function add(array $data): string|object
    {
        $condition = $this->condition->newInstance();
        foreach ($data as $key => $column) {
            $condition[$key] = $column;
        }
        $condition->save();
        return $condition;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->condition->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->condition->get();
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);

        return $this->condition->orderBy('name')
            ->when(isset($key) , function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $condition = $this->condition->find($id);
        foreach ($data as $key => $column) {
            $condition[$key] = $column;
        }
        $condition->save();
        return $condition;
    }

    public function delete(string $id): bool
    {
        $condition = $this->condition->find($id);
        $condition->translations()->delete();
        $condition->delete();

        return true;
    }

    public function getExportList(Request $request): Collection
    {
        $key = explode(' ', $request['search']);
        return $this->condition->orderBy('name')
            ->when(isset($key) , function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->get();
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->condition->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getDropdownList(Request $request, int|string $dataLimit = DEFAULT_DATA_LIMIT): Collection
    {
        return $this->condition->where('name', 'like', '%'.$request->q.'%')->limit($dataLimit)->get();
    }
}
