<?php

namespace Modules\AI\app\Http\Controllers\Admin\Web\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Brian2694\Toastr\Facades\Toastr;
use Modules\AI\app\Models\AISettings;

class AISettingsController extends Controller
{
    public function index(): View
    {
       dd("SS");
    }
}
