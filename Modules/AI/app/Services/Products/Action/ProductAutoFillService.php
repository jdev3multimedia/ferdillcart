<?php

namespace Modules\AI\app\Services\Products\Action;

use Modules\AI\app\Core\Constants\AIEngineNames;
use Modules\AI\app\Core\Contracts\AIEngineInterface;
use Modules\AI\app\Core\Factory\AIEngineFactory;
use Modules\AI\app\Services\Products\Prompts\ProductPrompts;
use Modules\AI\app\Traits\ConversationTrait;

class ProductAutoFillService
{
   use ConversationTrait;
   protected AIEngineInterface $engine;
   protected ProductPrompts $productPrompts;

   public function __construct()
   {
      $this->engine = AIEngineFactory::create(AIEngineNames::getDefault());
      $this->productPrompts = new ProductPrompts();
   }


   public function titleAutoFill(string $name,  $langCode, $moduleType=null): string
   {

      $prompt = $this->productPrompts->titleAutoFill($name, $langCode, $moduleType);

      return  $this->engine->core($prompt);
   }
   public function descriptionAutoFill(string $name,  $langCode, $moduleType): string
   {

      $prompt = $this->productPrompts->descriptionAutoFill($name, $langCode, $moduleType);

      return $this->cleanAIHtmlOutput($this->engine->core($prompt));
   }
   public function generalSetupAutoFill(string $name,  string $description,$store_id, $moduleType): string
   {

      $prompt = $this->productPrompts->generalSetupAutoFill($name, $description,$store_id,$moduleType);

      return  $this->engine->core($prompt);
   }
   public function PriceOthersAutoFill(string $name, $description = null): string
   {

      $prompt = $this->productPrompts->PriceOthersAutoFill($name, $description);

      return  $this->engine->core($prompt);
   }
   public function seoSectionAutoFill(string $name, $description = null): string
   {

      $prompt = $this->productPrompts->seoSectionAutoFill($name, $description);

      return  $this->engine->core($prompt);
   }
   public function variationSetupAutoFill(string $name, string $description): string
   {

      $prompt = $this->productPrompts->variationSetupAutoFill($name, $description);

      return  $this->engine->core($prompt);
   }
   public function otherVariationSetupAutoFill(string $name, string $description ,$moduleType): string
   {

      $prompt = $this->productPrompts->otherVariationSetupAutoFill($name, $description,$moduleType);

      return  $this->engine->core($prompt);
   }
   public function imageAnalysisAutoFill(string $imageUrl): string
   {

      $prompt = $this->productPrompts->imageAnalysisAutoFill();

      return  $this->engine->core($prompt, $imageUrl);
   }
   public function generateTitleSuggestions(array $keywords): string
   {
      $prompt = $this->productPrompts->generateTitleSuggestions($keywords);
      return  $this->engine->core($prompt);
   }
}
