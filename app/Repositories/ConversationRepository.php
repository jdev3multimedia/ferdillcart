<?php

namespace App\Repositories;

use App\Contracts\Repositories\ConversationRepositoryInterface;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ConversationRepository implements ConversationRepositoryInterface
{
    public function __construct(protected Conversation $conversation)
    {
    }

    public function add(array $data): string|object
    {
        $conversation = $this->conversation->newInstance();
        foreach ($data as $key => $column) {
            $conversation[$key] = $column;
        }
        $conversation->save();
        return $conversation;
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->conversation->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->conversation->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        return $this->conversation->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit($dataLimit)->get();
    }

    public function update(string $id, array $data): bool|string|object
    {
        $conversation = $this->conversation->find($id);
        foreach ($data as $key => $column) {
            $conversation[$key] = $column;
        }
        $conversation->save();
        return $conversation;
    }

    public function delete(string $id): bool
    {
        $conversation = $this->conversation->find($id);
        $conversation->translations()->delete();
        $conversation->delete();

        return true;
    }

    public function getFirstWhereWithScope(array $params, array $relations = [], array $scopes=[]): ?Model
    {
        return $this->conversation
            ->where(function ($q) use ($scopes) {
                foreach ($scopes as $key => $value) {
                    $q->$key(implode(', ',$value));
                }
            })
            ->where($params)->first();
    }

    public function getListWithScope(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, string $conversation_with = 'customer', int $offset = null, array $scopes=[]): Collection|LengthAwarePaginator
    {
        $data = $this->conversation->with($relations)
            ->where(function ($q) use ($scopes) {
                foreach ($scopes as $key => $value) {
                    $q->$key(implode(', ',$value));
                }
            })
            ->when($conversation_with == 'store' , function($query){
                $query->WhereUserType('vendor');
            })
            ->when($conversation_with != 'store' , function($query){
                $query->WhereUserType('customer');
            });
        if($dataLimit == 'all'){
            return $data->get();
        }
        return $data->paginate($dataLimit);
    }

    public function getListWhereWithScope(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null, array $scopes=[]): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        $data = $this->conversation
            ->where(function ($q) use ($scopes) {
                foreach ($scopes as $key => $value) {
                    $q->$key(implode(', ',$value));
                }
            })
            ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        });
        if($dataLimit == 'all'){
            return $data->get();
        }
        return $data->paginate($dataLimit);
    }
    public function getDmConversationList(Request $request, int|string $dataLimit = DEFAULT_DATA_LIMIT, int $user ,int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $request->get('key'));
        $data =$this->conversation->with(['sender', 'receiver', 'last_message'])->WhereUser($user)
        ->when($request->conversation_with == 'store' , function($query){
            $query->WhereUserType('vendor');
        })
        ->when($request?->conversation_with != 'store' , function($query){
            $query->WhereUserType('customer');
        })
        ->when($request->get('key') , function ($query) use ($key){
            $query->where(function($qu)use($key){
                $qu->where('sender_type','!=', 'delivery_man')->whereHas('sender',function($query)use($key){
                        foreach ($key as $value) {
                            $query->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%")->orWhere('phone', 'like', "%{$value}%");
                        }
                    })->orWhere(function($q)use($key){
                        $q->where('receiver_type','!=', 'delivery_man')->whereHas('receiver',function($query)use($key){
                            foreach ($key as $value) {
                                $query->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%")->orWhere('phone', 'like', "%{$value}%");
                            }
                        });
                    });
                });
            });
            if($dataLimit == 'all'){
                return $data->get();
            }
            return $data->paginate($dataLimit);
    }
}
