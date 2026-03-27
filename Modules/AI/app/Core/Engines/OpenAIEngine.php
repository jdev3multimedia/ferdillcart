<?php

namespace Modules\AI\app\Core\Engines;

use Modules\AI\app\Core\Contracts\AIEngineInterface;
use OpenAI\Laravel\Facades\OpenAI;


class OpenAIEngine implements AIEngineInterface
{
    public function boot(): void
    {
        // TODO: Implement boot() method.
    }

    public function core($prompt, $imageUrl = null): string
    {
        $content = [['type' => 'text', 'text' => $prompt]];

        if (!empty($imageUrl)) {
            $content[] = [
                'type' => 'image_url',
                'image_url' => ['url' => $imageUrl],
            ];
        }

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
            'temperature' => 0.3,
        ]);

        return $response->choices[0]->message->content;
    }


}


