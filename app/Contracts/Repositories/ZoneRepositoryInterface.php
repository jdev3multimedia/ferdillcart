<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ZoneRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $params
     * @param array $relations
     * @return Model|null
     */
    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model;

    /**
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * @param array $params
     * @return Model|null
     */
    public function getWithCoordinateWhere(array $params): ?Model;

    /**
     * @param Request $request
     * @return Collection
     */
    public function getExportList(Request $request): Collection;

    /**
     * @param array $relations
     * @return Model|null
     */
    public function getLatest(array $relations = []): ?Model;

    /**
     * @param string $id
     * @param array $data
     * @param array $moduleData
     * @return bool|string|object
     */
    public function zoneModuleSetupUpdate(string $id, array $data, array $moduleData): bool|string|object;

    /**
     * @param array $relations
     * @param int|string $dataLimit
     * @param int|null $offset
     * @return Collection|LengthAwarePaginator
     */
    public function getWithCountLatest(array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator;

    /**
     * @param array $params
     * @return Collection
     */
    public function getActiveListExcept(array $params): Collection;
}
