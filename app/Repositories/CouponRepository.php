<?php

namespace App\Repositories;

use App\Contracts\Repositories\CouponRepositoryInterface;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;

class CouponRepository implements CouponRepositoryInterface
{
    public function __construct(protected Coupon $coupon)
    {
    }

    public function add(array $data): string|object
    {
        $coupon = $this->coupon->newInstance();
        foreach ($data as $key => $column) {
            $coupon[$key] = $column;
        }
        $coupon->save();
        return $coupon;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->coupon->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->coupon->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->coupon->with($relations)->where($filters)
            ->when(isset($key), function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%")
                            ->orWhere('code', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()
            ->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $coupon = $this->coupon->find($id);
        foreach ($data as $key => $column) {
            $coupon[$key] = $column;
        }
        $coupon->save();
        return $coupon;
    }

    public function delete(string $id): bool
    {
        $coupon = $this->coupon->find($id);
        $coupon->translations()->delete();
        $coupon->delete();

        return true;
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->coupon->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getExportList(Request $request): Collection
    {
        $key = explode(' ', $request['search']);
        return $this->coupon->with('module')->where('created_by','admin')->where('module_id', Config::get('module.current_module_id'))
            ->when(isset($key), function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%")
                            ->orWhere('code', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->get();
    }
}
