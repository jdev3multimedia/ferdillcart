<?php

namespace App\Repositories;

use App\CentralLogics\Helpers;
use App\Contracts\Repositories\BannerRepositoryInterface;
use App\Models\Banner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class BannerRepository implements BannerRepositoryInterface
{
    public function __construct(protected Banner $banner)
    {
    }

    public function add(array $data): string|object
    {
        $banner = $this->banner->newInstance();
        foreach ($data as $key => $column) {
            $banner[$key] = $column;
        }
        $banner->save();
        return $banner;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->banner->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->banner->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->banner->with($relations)->where($filters)
        ->when(isset($key) , function($q) use($key){
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $banner = $this->banner->find($id);
        foreach ($data as $key => $column) {
            $banner[$key] = $column;
        }
        $banner->save();
        return $banner;
    }

    public function delete(string $id): bool
    {
        $banner = $this->banner->find($id);
        Helpers::check_and_delete('banner/' , $banner['image']);
        $banner->translations()->delete();
        $banner->delete();

        return true;
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->banner->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getSearchedList(string $searchValue = null, int|string $dataLimit = DEFAULT_DATA_LIMIT): Collection
    {
        $key = explode(' ', $searchValue);
        return $this->banner->where('module_id', Config::get('module.current_module_id'))->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('title', 'like', "%{$value}%");
            }
        })->limit($dataLimit)->get();
    }
}
