<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserNotificationRepositoryInterface;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class UserNotificationRepository implements UserNotificationRepositoryInterface
{
    public function __construct(protected UserNotification $notification)
    {
    }

    public function add(array $data): string|object
    {
        $notification = $this->notification->newInstance();
        foreach ($data as $key => $column) {
            $notification[$key] = $column;
        }
        $notification->save();
        return $notification;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->notification->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->notification->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->notification->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit($dataLimit)->get();
    }

    public function update(string $id, array $data): bool|string|object
    {
        $notification = $this->notification->find($id);
        foreach ($data as $key => $column) {
            $notification[$key] = $column;
        }
        $notification->save();
        return $notification;
    }

    public function delete(string $id): bool
    {
        $notification = $this->notification->find($id);
        $notification->translations()->delete();
        $notification->delete();

        return true;
    }
}
