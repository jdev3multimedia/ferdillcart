<?php

namespace Modules\AI\app\Core\Constants;

class AIEngine
{
    public const OPENAI = 'openai';
    public const DEEPSEEK = 'deepseek';
    public const CLAUDE = 'claude';

    public const ALL = [
        self::OPENAI,
        self::DEEPSEEK,
        self::CLAUDE,
    ];

    public const LABELS = [
        self::OPENAI   => 'OpenAI',
        self::DEEPSEEK => 'DeepSeek',
        self::CLAUDE   => 'Claude',
    ];

    public static function isSupported(string $engine): bool
    {
        return in_array($engine, self::ALL, true);
    }

    public static function getLabel(string $engine): string
    {
        return self::LABELS[$engine] ?? ucfirst($engine);
    }
}
