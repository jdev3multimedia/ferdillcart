<?php

namespace App\Repositories;

use App\CentralLogics\Helpers;
use App\Contracts\Repositories\DeliveryManRepositoryInterface;
use App\Models\DeliveryMan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeliveryManRepository implements DeliveryManRepositoryInterface
{
    public function __construct(protected DeliveryMan $deliveryMan)
    {
    }

    public function add(array $data): string|object
    {
        $deliveryMan = $this->deliveryMan->newInstance();
        foreach ($data as $key => $column) {
            $deliveryMan[$key] = $column;
        }
        $deliveryMan->save();
        return $deliveryMan;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->deliveryMan->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->deliveryMan->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        $data = $this->deliveryMan->with($relations)->where($filters)
            ->when(isset($key), function($query) use($key){
                $query->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('identity_number', 'like', "%{$value}%");
                    }
                });
            })
            ->latest();

            if($dataLimit == 'all'){
                return $data->get();
            }
            return $data->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $deliveryMan = $this->deliveryMan->find($id);
        foreach ($data as $key => $column) {
            $deliveryMan[$key] = $column;
        }
        $deliveryMan->save();
        return $deliveryMan;
    }

    public function delete(string $id): bool
    {
        $deliveryMan = $this->deliveryMan->find($id);
        Helpers::check_and_delete('delivery-man/' , $deliveryMan['image']);


        foreach (json_decode($deliveryMan['identity_image'], true) as $img) {
            Helpers::check_and_delete('delivery-man/' , $img);

        }

        if($deliveryMan->userinfo){
            $deliveryMan->userinfo->delete();
        }
        $deliveryMan->delete();

        return true;
    }

    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model
    {
        return $this->deliveryMan->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getZoneWiseListWhere(string $zoneId = 'all',string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        $data = $this->deliveryMan->with($relations)->where($filters)
            ->when(is_numeric($zoneId), function($query) use($zoneId){
                return $query->where('zone_id', $zoneId);
            })
            ->when(isset($key), function($query) use($key){
                $query->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('identity_number', 'like', "%{$value}%");
                    }
                });
            })
            ->latest();
            if($dataLimit == 'all'){
                return $data->get();
            }
            return $data->paginate($dataLimit);

    }

    public function getDropdownList(Request $request): Collection
    {
        $key = explode(' ', $request->q);
        $zoneIds = isset($request->zone_ids)?(count($request->zone_ids)>0?$request->zone_ids:[]):0;
        return $this->deliveryMan->when($zoneIds, function($query) use($zoneIds){
            return $query->whereIn('zone_id', $zoneIds);
        })
            ->when($request->earning, function($query){
                return $query->earning();
            })
            ->where(function ($query) use ($key) {
                foreach ($key as $value) {
                    $query->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%")
                        ->orWhere('identity_number', 'like', "%{$value}%");
                }
            })->active()->limit(8)->get(['id',DB::raw('CONCAT(f_name, " ", l_name) as text')]);
    }

    public function getActiveFirstWhere(string $searchValue = null, array $filters = [], array $relations = []): ?Model
    {
        $key = explode(' ', $searchValue);
        return $this->deliveryMan->with($relations)->where($filters)
            ->when(isset($key), function($query) use($key){
                $query->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('identity_number', 'like', "%{$value}%");
                    }
                });
            })
            ->Active()
            ->first();
    }
    public function getFilterWiseListWhere(string $zoneId = 'all', string $searchValue = null, array $filters = [],  string $additionalFilter = null ,  string $jobType = null ,array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        $data = $this->deliveryMan->with($relations)
            ->where($filters)
            ->when(is_numeric($zoneId), function($query) use($zoneId){
                return $query->where('zone_id', $zoneId);
            })
            ->when(isset($additionalFilter) && $additionalFilter == 'active', function($query){
                return $query->Zonewise()->where('application_status','approved')->where('active',1);
            })
            ->when(isset($additionalFilter) && $additionalFilter == 'inactive', function($query){
                return $query->Zonewise()->where('application_status','approved')->where('active',0);
            })
            ->when(isset($additionalFilter) && $additionalFilter == 'new', function($query){
                return $query->Zonewise()->whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'));
            })
            ->when(isset($additionalFilter) && $additionalFilter == 'blocked', function($query){
                return $query->Zonewise()->where('status',0)->where('application_status','approved');
            })
            ->when(isset($jobType) && $jobType == 'freelancer', function($query){
                return $query->Zonewise()->where('earning',1)->where('application_status','approved');
            })
            ->when(isset($jobType) && $jobType == 'salary_base', function($query){
                return $query->Zonewise()->where('earning',0)->where('application_status','approved');
            })
            ->when(isset($key), function($query) use($key){
                $query->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('identity_number', 'like', "%{$value}%");
                    }
                });
            })
            ->latest();
            if($dataLimit == 'all'){
                return $data->get();
            }
            return $data->paginate($dataLimit);
    }


}
