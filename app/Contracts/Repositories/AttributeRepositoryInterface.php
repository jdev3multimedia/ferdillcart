<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface AttributeRepositoryInterface extends RepositoryInterface
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
}
