<?php

namespace App\Repositories;

use App\CentralLogics\Helpers;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Http\Requests\Admin\CategoryBulkExportRequest;
use App\Models\Category;
use App\Traits\FileManagerTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    use FileManagerTrait;

    public function __construct(protected Category $category)
    {
    }

    public function add(array $data): string|object
    {
        $category = $this->category->newInstance();
        foreach ($data as $key => $column) {
            $category[$key] = $column;
        }
        $category->save();
        return $category;
    }

    public function addByChunk(array $data): void
    {
        $chunkSize = 100;
        $chunkCategories = array_chunk($data, $chunkSize);

        foreach ($chunkCategories as $key => $chunkCategory) {
//            DB::table('categories')->insert($chunkCategory);
            foreach ($chunkCategory as $category) {
                $insertedId = DB::table('categories')->insertGetId($category);
                Helpers::updateStorageTable(get_class(new Category), $insertedId, $category['image']);
            }
        }
    }

    public function updateByChunk(array $data): void
    {
        $chunkSize = 100;
        $chunkCategories = array_chunk($data, $chunkSize);

        foreach ($chunkCategories as $key => $chunkCategory) {
//            DB::table('categories')->upsert($chunkCategory, ['id', 'module_id'], ['name', 'image', 'parent_id', 'position', 'priority', 'status']);
            foreach ($chunkCategory as $category) {
                if (isset($category['id']) && DB::table('categories')->where('id', $category['id'])->exists()) {
                    DB::table('categories')->where('id', $category['id'])->update($category);
                    Helpers::updateStorageTable(get_class(new Category), $category['id'], $category['image']);
                } else {
                    $insertedId = DB::table('categories')->insertGetId($category);
                    Helpers::updateStorageTable(get_class(new Category), $insertedId, $category['image']);
                }
            }
        }
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->category->with($relations)->where($params)->first();
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->category->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->category->get();
    }

    public function getBulkExportList(CategoryBulkExportRequest $request): Collection
    {
        return $this->category->when($request['type'] == 'date_wise', function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        })->when($request->type == 'id_wise', function ($query) use ($request) {
            $query->whereBetween('id', [$request->start_id, $request->end_id]);
        })->module(Config::get('module.current_module_id'))->get();
    }

    public function getExportList(Request $request): Collection
    {
        $position=$request->position ?? 0;
        $key = explode(' ', $request['search']);
        return $this->category->with('module')->where(['position' => $position])->module(Config::get('module.current_module_id'))
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()
            ->get();
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->category->with($relations)->where($filters)->module(Config::get('module.current_module_id'))
            ->when(isset($key), function ($query) use ($key) {
                $query->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })->latest()->paginate($dataLimit);
    }

    public function getNameList(Request $request, int|string $dataLimit = DEFAULT_DATA_LIMIT): SupportCollection|LengthAwarePaginator
    {
        $search = $request->q ?? $request->searchValue;
        return $this->category->where('name', 'like', '%' . $search. '%')
            ->when($request->module_id, function ($query) use ($request) {
                $query->where('module_id', $request->module_id);
            })
            ->when($request->position == 1, function ($query) {
                $query->where('position', 1);
            })
            ->when($request->position != null && $request->position == 0, function ($query) {
                $query->where('position', 0);
            })
            ->limit($dataLimit)->get()
            ->map(function ($category) {
                $data = $category->position == 0 ? translate('messages.main') : translate('messages.sub');
                return [
                    'id' => $category->id,
                    'text' => $category->name . ' (' . $data . ')',
                ];
            });
    }

    public function getMainList(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->category->with($relations)->where($filters)->module(Config::get('module.current_module_id'))
            ->when(isset($key), function ($query) use ($key) {
                $query->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })->latest()->get();
    }


    public function update(string $id, array $data): string|object
    {
        $category = $this->category->find($id);
        foreach ($data as $key => $column) {
            $category[$key] = $column;
        }
        $category->save();
        return $category;
    }

    public function delete(string $id): bool
    {
        $category = $this->category->find($id);
        if ($category->childes->count() == 0) {
            $category?->taxVats()->delete();
            $category->translations()->delete();
            $category->delete();
        } else {
            return false;
        }
        return true;
    }
}
