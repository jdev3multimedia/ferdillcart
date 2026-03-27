<?php

namespace App\Exceptions;


use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;

class ZoneModuleException extends Exception implements ShouldntReport
{
    protected $code = 422;

    public function render()
    {
         return response()->json([
                'errors' => [
                    ['code' => 'zone_module_not_found', 'message' => $this->getMessage()]
                ]
            ], 422);
    }

    public function report()
    {
        return false;
    }
}
