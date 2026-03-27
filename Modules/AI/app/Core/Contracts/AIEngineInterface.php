<?php
namespace Modules\AI\app\Core\Contracts;

interface AIEngineInterface
{
     public function boot(): void;
     public function core($prompt , $imageUrl=null): string;
}
