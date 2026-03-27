<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;


interface DmVehicleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $params
     * @param array $relations
     * @return Model|null
     */
    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model;

    /**
     * @param string|null $searchValue
     * @param int|string $dataLimit
     * @return Collection
     */
    public function getSearchedList(string $searchValue = null, int|string $dataLimit = DEFAULT_DATA_LIMIT): Collection;

    /**
     * @param array $params
     * @param string|null $id
     * @return Model|null
     */
    public function getExistFirst(array $params, string $id = null): ?Model;


    /**
     * @param string|null $searchValue
     * @param array $filters Filters value must be in key and value pair structure, support one level nested array, ex: Filters = ['category'=>[1,2,5,8], 'email'=>['x@x.com','test@test.com']]
     * @param array $relations
     * @param int|string $dataLimit If you need all data without pagination, you need to set dataLimit = 'all'
     * @param int|null $offset
     * @return Collection|LengthAwarePaginator
     */
    public function getListWhereWithCount(string $searchValue = null, array $filters = [], array $relations = [], array $withCountRelations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator;

}
