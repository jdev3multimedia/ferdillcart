<?php

namespace App\Repositories;

use App\Contracts\Repositories\DmReviewRepositoryInterface;
use App\Models\DMReview;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DmReviewRepository implements DmReviewRepositoryInterface
{
    public function __construct(protected DMReview $review)
    {
    }

    public function add(array $data): string|object
    {
        $review = $this->review->newInstance();
        foreach ($data as $key => $column) {
            $review[$key] = $column;
        }
        $review->save();
        return $review;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->review->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->review->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        $data = $this->review->with($relations)->where($filters)
            ->when($searchValue, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    $q->whereHas('delivery_man', function ($query) use ($key) {
                        foreach ($key as $value) {
                            $query->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%");
                        }
                    })->orWhereHas('customer', function ($query) use ($key) {
                        foreach ($key as $value) {
                            $query->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%");
                        }
                    });
                });
            })
            ->latest();
        if ($dataLimit == 'all') {
            return $data->get();
        }
        return $data->paginate($dataLimit);
    }

    public function getListWhereOrder(string $searchValue = null, array $filters = [], array $relations = [], array $orderBy = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);

        $data = $this->review->with($relations)->where($filters)
            ->when(isset($key), function ($query) use ($key) {
                $query->whereHas('delivery_man', function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%");
                    }
                });
            });

        if (count($orderBy) > 0) {
            $data->orderBy($orderBy['col'], $orderBy['type']);
        } else {
            $data->latest();
        }
        if ($dataLimit == 'all') {
            return $data->get();
        }
        return $data->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $review = $this->review->find($id);
        foreach ($data as $key => $column) {
            $review[$key] = $column;
        }
        $review->save();
        return $review;
    }

    public function delete(string $id): bool
    {
        $review = $this->review->find($id);
        $review->delete();

        return true;
    }
}
