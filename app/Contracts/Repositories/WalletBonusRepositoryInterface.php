<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface WalletBonusRepositoryInterface extends RepositoryInterface
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
}
