<?php

namespace App\Repositories;

use App\Contracts\Repositories\MessageRepositoryInterface;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageRepository implements MessageRepositoryInterface
{
    public function __construct(protected Message $message)
    {
    }

    public function add(array $data): string|object
    {
        $message = $this->message->newInstance();
        foreach ($data as $key => $column) {
            $message[$key] = $column;
        }
        $message->save();
        return $message;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->message->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->message->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $data = $this->message->where($filters);
        if($dataLimit == 'all'){
            return $data->get();
        }
        return $data->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $message = $this->message->find($id);
        foreach ($data as $key => $column) {
            $message[$key] = $column;
        }
        $message->save();
        return $message;
    }

    public function delete(string $id): bool
    {
        $message = $this->message->find($id);
        $message->translations()->delete();
        $message->delete();

        return true;
    }
}
