<?php
namespace Modules\AI\app\Core\Constants;

use Illuminate\Support\Facades\DB;

class AIEngineNames
{
    public const OPENAI = 'openai';
    public const CLAUDE = 'claude';

    public static function getDefault(): string
    {
        $default = null;

        return $default ?: self::OPENAI;
    }
}
