<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface OrderTransactionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Request $request
     * @return Collection
     */
    public function getDmEarningList(Request $request): Collection;
}
