<?php

namespace App\Contracts\Repositories;

use Illuminate\Http\Request;

interface TranslationRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Request $request
     * @param object $model
     * @param string $modelPath
     * @return bool
     */
    public function addByModel(Request $request, object $model, string $modelPath, string $attribute): bool;

    /**
     * @param Request $request
     * @param object $model
     * @param string $modelPath
     * @return bool
     */
    public function updateByModel(Request $request, object $model, string $modelPath, string $attribute): bool;
}
