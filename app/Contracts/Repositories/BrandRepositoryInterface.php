<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface BrandRepositoryInterface extends RepositoryInterface
{
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
     * @return Collection
     */
    public function getDropdownList(Request $request, int|string $dataLimit = DEFAULT_DATA_LIMIT): Collection;
}
