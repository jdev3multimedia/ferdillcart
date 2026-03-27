<?php

namespace App\Contracts\Repositories;

use App\Http\Requests\Admin\CategoryBulkExportRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface extends RepositoryInterface
{
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

    /**
     * @param CategoryBulkExportRequest $request
     * @return Collection
     */
    public function getBulkExportList(CategoryBulkExportRequest $request): Collection;

    /**
     * @param Request $request
     * @return Collection
     */
    public function getExportList(Request $request): Collection;

    /**
     * @param array $params
     * @param array $relations
     * @return Model|null
     */
    public function getFirstWithoutGlobalScopeWhere(array $params, array $relations = []): ?Model;

    /**
     * @param Request $request
     * @param int|string $dataLimit
     * @return \Illuminate\Support\Collection|LengthAwarePaginator
     */
    public function getNameList(Request $request, int|string $dataLimit = DEFAULT_DATA_LIMIT): \Illuminate\Support\Collection|LengthAwarePaginator;

    /**
     * @param string|null $searchValue
     * @param array $filters
     * @param array $relations
     * @param int|string $dataLimit
     * @param int|null $offset
     * @return Collection|LengthAwarePaginator
     */
    public function getMainList(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator;
}
