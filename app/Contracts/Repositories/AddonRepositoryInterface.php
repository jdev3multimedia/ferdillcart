<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface AddonRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $params
     * @param array $relations
     * @return Model|null
     */
    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model;

    /**
     * @param int|string $moduleId
     * @param string|null $searchValue
     * @param int|string $storeId
     * @param int|string $dataLimit
     * @return Collection|LengthAwarePaginator
     */
    public function getStoreWiseList(int|string $moduleId , string $searchValue = null, int|string $storeId = 'all', int|string $dataLimit = DEFAULT_DATA_LIMIT): Collection|LengthAwarePaginator;

    /**
     * @param int|string $moduleId
     * @param string|null $searchValue
     * @param int|string $storeId
     * @return Collection
     */
    public function getExportList(int|string $moduleId , string $searchValue = null, int|string $storeId = 'all'): Collection;

    /**
     * @param array $data
     * @return void
     */
    public function addByChunk(array $data): void;

    /**
     * @param array $data
     * @return void
     */
    public function updateByChunk(array $data): void;
}
