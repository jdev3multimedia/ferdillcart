<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ConversationRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $params
     * @param array $relations
     * @param array $scopes
     * @return Model|null
     */
    public function getFirstWhereWithScope(array $params, array $relations = [], array $scopes=[]): ?Model;

    /**
     * @param array $orderBy
     * @param array $relations
     * @param int|string $dataLimit
     * @param int|null $offset
     * @param array $scopes
     * @return Collection|LengthAwarePaginator
     */
    public function getListWithScope(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, string $conversation_with = 'customer', int $offset = null, array $scopes=[]): Collection|LengthAwarePaginator;

    /**
     * @param string|null $searchValue
     * @param array $filters
     * @param array $relations
     * @param int|string $dataLimit
     * @param int|null $offset
     * @param array $scopes
     * @return Collection|LengthAwarePaginator
     */
    public function getListWhereWithScope(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null, array $scopes=[]): Collection|LengthAwarePaginator;

    /**
     * @param Request $request
     * @param int|string $dataLimit
     * @param int|null $offset
     * @return Collection|LengthAwarePaginator
     */
    public function getDmConversationList(Request $request, int|string $dataLimit = DEFAULT_DATA_LIMIT, int $user,  int $offset = null ): Collection|LengthAwarePaginator;
}
