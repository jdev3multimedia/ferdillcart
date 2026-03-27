<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Request $request
     * @return Collection
     */
    public function getSearchList(Request $request): Collection;

    /**
     * @param array $params
     * @param array $relations
     * @return Model|null
     */
    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model;

    /**
     * @param array $params
     * @param array $relations
     * @return Model|null
     */
    public function getFirstWhereExceptAdmin(array $params, array $relations = []): ?Model;

    /**
     * @param Request $request
     * @return Collection
     */
    public function getExportList(Request $request): Collection;

        /**
     * @param string $zoneId
     * @param string|null $searchValue
     * @param array $filters
     * @param array $relations
     * @param int|string $dataLimit
     * @param int|null $offset
     * @return Collection|LengthAwarePaginator
     */
    public function getZoneWiseListWhere(string $zoneId = 'all', string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator;

}
