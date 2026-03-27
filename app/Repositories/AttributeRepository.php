<?php

namespace App\Repositories;

use App\Contracts\Repositories\AttributeRepositoryInterface;
use App\Models\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AttributeRepository implements AttributeRepositoryInterface
{
    public function __construct(protected Attribute $attribute)
    {
    }

    public function add(array $data): string|object
    {
        $attribute = $this->attribute->newInstance();
        foreach ($data as $key => $column) {
            $attribute[$key] = $column;
        }
        $attribute->save();
        return $attribute;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->attribute->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->attribute->get();
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);

        return $this->attribute->orderBy('name')
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
        $attribute = $this->attribute->find($id);
        foreach ($data as $key => $column) {
            $attribute[$key] = $column;
        }
        $attribute->save();
        return $attribute;
    }

    public function delete(string $id): bool
    {
        $attribute = $this->attribute->find($id);
        $attribute->translations()->delete();
        $attribute->delete();

        return true;
    }

    public function getExportList(Request $request): Collection
    {
        $key = explode(' ', $request['search']);
        return $this->attribute->orderBy('name')
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
        return $this->attribute->withoutGlobalScope('translate')->where($params)->first();
    }
}
