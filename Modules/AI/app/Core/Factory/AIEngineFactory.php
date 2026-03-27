<?php

namespace Modules\AI\app\Core\Factory;

use InvalidArgumentException;
use Modules\AI\app\Core\Contracts\AIEngineInterface;
use Modules\AI\app\Core\Engines\OpenAIEngine;
use Modules\AI\app\Core\Constants\AIEngine;

class AIEngineFactory
{
    public static function create(string $engine): AIEngineInterface
    {
        return match (strtolower($engine)) {
            AIEngine::OPENAI   => new OpenAIEngine(),
            // AIEngine::DEEPSEEK => new DeepSeekEngine(),
            // AIEngine::CLAUDE   => new ClaudeEngine(),

            default => throw new InvalidArgumentException("Unsupported AI engine: {$engine}"),
        };
    }
}
