<?php

namespace App\Exceptions;

use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\Request;

class InvalidUploadException extends Exception implements ShouldntReport
{
    protected $code = 422;

    public function render(Request $request)
    {
        if ($request->is('api/v1/*') || $request->expectsJson()) {
            return response()->json([
                'errors' => [
                    ['code' => 'invalid_upload', 'message' => $this->getMessage()]
                ]
            ], $request->ajax() ? 200 : 422);
        }
        Toastr::error($this->getMessage());
        return redirect()->back();
    }

    public function report()
    {
        return false;
    }
}
