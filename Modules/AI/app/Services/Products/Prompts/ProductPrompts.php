<?php

namespace Modules\AI\app\Services\Products\Prompts;

use App\CentralLogics\Helpers;
use Modules\AI\app\Services\Products\Resource\ProductResource;


class ProductPrompts
{
    protected ProductResource $ProductResource;

    public function __construct()
    {
        $this->ProductResource = new ProductResource();
    }



    public function titleAutoFill(string $name, string $langCode = "en", $moduleType = null)
    {

        $langCode = strtoupper($langCode);

        $promptText = <<<PROMPT
        You are a creative and professional copywriter.
        Rewrite the product name "{$name}" into a clean, concise, and professional title for an online {$moduleType} store (food → emphasize taste/cuisine; grocery → highlight freshness/quantity; pharmacy → focus on clarity and dosage/form; shop → make brand-focused and appealing for e-commerce).

      CRITICAL INSTRUCTION:
      - The output must be 100% in {$langCode} — this is mandatory.
      - If the original name is not in {$langCode}, fully translate it into {$langCode} while keeping the meaning.
      - Do not mix languages; use only {$langCode} characters and words.
      - Keep it short (35–70 characters), plain, and ready for listings.
      - No extra words, slogans, or punctuation.
      - Return only the translated title as plain text in {$langCode}.

      PROMPT;

        return $promptText;
    }




    public function descriptionAutoFill(string $name, string $langCode = "en", $moduleType = null)
    {
        $langCode = strtoupper($langCode);

        $promptText = <<<PROMPT
        You are a creative and professional copywriter.
        Generate engaging, and persuasive product description for the product named "{$name}" into a clean, concise, and professional description for an online {$moduleType} store (food → emphasize taste/cuisine; grocery → highlight freshness/quantity; pharmacy → focus on clarity and dosage/form; shop → make brand-focused and appealing for e-commerce).

        CRITICAL LANGUAGE RULES:
        - The entire description must be written 100% in {$langCode} — this is mandatory.
        - If the product name is in another language, translate and localize it naturally into {$langCode}.
        - Do not mix languages; use only {$langCode} characters and words.
        - Adapt the tone, phrasing, and examples to be natural for {$langCode} readers.

        Content & Structure:
        - Include a section with key features as separate paragraphs with its ingredients.
        - Focus on benefits, unique selling points, and appeal to the target audience.
        - Use clear, compelling, and marketing-friendly language.
        - Ensure the description is engaging and interesting.
        - Avoid any non-product-specific information.
        - Must be in 300-600 characters.
        - Keep it short and to the point, plain, simple and ready for listings.

        Formatting:
        - Output valid Product descriptions.
        - Do NOT include any markdown syntax, code fences, or triple backticks.
        - Return only plain text in the paragraph (no HTML tags, no empty lines).


        PROMPT;

        return $promptText;
    }






    public function generalSetupAutoFill(string $name, string $description, $store_id = null, $moduleType = null)
    {
        $resource = $this->ProductResource->productGeneralSetupData($store_id,$moduleType);

        if( in_array($moduleType,['shop','ecommerce'])){
            $promptText = $this->generalPromptForShop($name,$description,$resource);
        } elseif(in_array($moduleType,['pharmacy'])){
             $promptText = $this->generalPromptForPharmacy($name,$description,$resource);
        } else
        {
             $promptText = $this->generalPromptForFoodAndGrocery($name,$description,$resource);
        }


        return $promptText;
    }


    private function generalPromptForFoodAndGrocery($name,$description,$resource)
    {
        $categories      = $resource['categories'];
        $subCategories   = $resource['sub_categories'];
        $units   = $resource['units'];
        $rawSubCategories   = $resource['rawSubCategories'];
        $rawSubCategories   = json_encode($rawSubCategories);
        $productTypes    = $resource['product_types'];
        $subCategories = implode("', '", array_keys($subCategories));

        $categories =  json_encode($categories);

        $productTypes = implode("', '", $productTypes);
        $nutrition   = $resource['nutrition'];
        $allergy   = $resource['allergy'];
        $addon   = $resource['addon']??[];



        $units = implode("', '", array_keys($units));
        $nutrition = implode("', '", array_keys($nutrition));
        $allergy = implode("', '", array_keys($allergy));
        $addon = implode("', '", array_keys($addon));
        $promptText = <<<PROMPT
                 Given:
                    - Name: "{$name}"
                    - Description: "{$description}"

                    Return ONLY valid JSON with these fields: {
                        "category_name": REQUIRED. Must be selected strictly from "{$categories}".
                            Use the KEYS of the list (category names) as the value.
                            Match only if relevant to the product name/description.

                        "sub_category_name": OPTIONAL.
                            If provided, it MUST be selected strictly from "{$rawSubCategories}".
                            The chosen sub_category_name MUST have parent_id equal to the ID of the selected category_name from "{$categories}".
                            If no valid sub_category_name exists that matches both the product name/description and the selected category, then leave "sub_category_name" empty.
                            Do not invent, guess, or suggest categories outside the provided lists.
                        "nutrition": ["..."], // prefer "{$nutrition}", else infer from description
                        "allergy": ["..."], // prefer "{$allergy}", else infer from description (optional)
                        "addon": ["..."], // must be from "{$addon}" only, optional
                        "product_type": from "{$productTypes}",
                        "search_tags": 3–5 keywords from name/description,
                        "is_halal": true|false,
                        "is_organic": true|false,
                        "units": from: must be from "{$units}" and string, optional
                        "available_time_starts":MUST BE morning time (AM, "HH:MM"),
                        "available_time_ends": MUST BE night time (PM, "HH:MM")
                    }

                    Rules for FOOD OR GROCERY:
                    - Emphasize cuisine, taste, meal type, emphasize freshness.
                    - Nutrition and addon are important.
                    - Allergy optional.
                    - Categories must be chosen only from provided lists and must be a string.
                    - Categories/sub-categories must be chosen only from provided lists.
                    - Do not invent new values.
                    - Output must be valid JSON (json_decode in PHP).

                    Options:
                    [CATEGORIES] {$categories}
                    [SUB CATEGORIES] {$subCategories}
                    [NUTRITION] {$nutrition}
                    [ALLERGY] {$allergy}
                    [ADDON] {$addon}
                    [UNITS] {$units}
                    [PRODUCT TYPES] {$productTypes}

                    === STRUCTURED RESPONSE FORMAT ===
                    - Output ONLY the JSON object.
                    - Do NOT include any explanations, comments, or markdown formatting.
                    - Do NOT wrap the JSON in ```json or any other code block.
                    - Ensure the JSON is syntactically valid for json_decode in PHP.


                 PROMPT;

        return $promptText;
    }
    private function generalPromptForPharmacy($name,$description,$resource)
    {
        $categories      = $resource['categories'];
        $subCategories   = $resource['sub_categories'];
        $units   = $resource['units'];
        $rawSubCategories   = $resource['rawSubCategories'];
        $rawSubCategories   = json_encode($rawSubCategories);
        $productTypes    = $resource['product_types'];
        $categories =  json_encode($categories);
        $units = implode("', '", array_keys($units));
        $subCategories = implode("', '", array_keys($subCategories));


        $productTypes = implode("', '", $productTypes);
        $generic_names   = $resource['generic_names'];
        $common_conditions   = $resource['common_conditions'];



        $generic_names = implode("', '", array_keys($generic_names));
        $common_conditions = implode("', '", array_keys($common_conditions));

        $promptText = <<<PROMPT
                      Given:
                        - Name: "{$name}"
                        - Description: "{$description}"

                        Return ONLY valid JSON with these fields: {
                          "category_name": REQUIRED. Must be selected strictly from "{$categories}".
                            Use the KEYS of the list (category names) as the value.
                            Match only if relevant to the product name/description.

                        "sub_category_name": OPTIONAL.
                            If provided, it MUST be selected strictly from "{$rawSubCategories}".
                            The chosen sub_category_name MUST have parent_id equal to the ID of the selected category_name from "{$categories}".
                            If no valid sub_category_name exists that matches both the product name/description and the selected category, then leave "sub_category_name" empty.
                            Do not invent, guess, or suggest categories outside the provided lists.
                        "generic_names": from "{$generic_names}" (preferred, else infer from description), //SUGGESTIONS ARE ALLOWED
                        "common_conditions": must be from "{$common_conditions}" (preferred from description,commonly used medicines),
                        "search_tags": 3–5 keywords extracted from name/description,
                        "is_basic_medicine": true|false,
                        "is_prescription_required": true|false,
                        "units": from: must be from "{$units}" and string, optional
                        }

                        Rules for PHARMACY:
                        - Emphasize clarity: include dosage, strength, and form (tablet, syrup, cream, injection, etc.).
                         - Categories must be chosen only from provided lists and must be a string.
                        - Categories/sub-categories must be chosen only from provided lists.
                        - Generic names: preferred → pick from list or infer if strongly indicated.
                        - Common conditions: preferred → must be from provided list if relevant.
                        - Do not invent new categories, sub-categories, generic names, or conditions.
                        - Output must be valid JSON (parsable with json_decode in PHP).
                        - No extra text outside JSON.

                        Options:
                        [CATEGORIES] {$categories}
                        [SUB CATEGORIES] {$subCategories}
                        [GENERIC NAMES] {$generic_names}
                        [COMMON CONDITIONS] {$common_conditions}
                        [UNITS] {$units}
                         === STRUCTURED RESPONSE FORMAT ===
                    - Output ONLY the JSON object.
                    - Do NOT include any explanations, comments, or markdown formatting.
                    - Do NOT wrap the JSON in ```json or any other code block.
                    - Ensure the JSON is syntactically valid for json_decode in PHP.

                 PROMPT;

        return $promptText;
    }
    private function generalPromptForShop($name,$description,$resource)
    {
        $categories      = $resource['categories'];
        $subCategories   = $resource['sub_categories'];
        $units   = $resource['units'];
        $rawSubCategories   = $resource['rawSubCategories'];
        $rawSubCategories   = json_encode($rawSubCategories);
        $categories =  json_encode($categories);
        $units = implode("', '", array_keys($units));
        $subCategories = implode("', '", array_keys($subCategories));

        $brands   = $resource['brands'];
        $brands = implode("', '", array_keys($brands));

        $promptText = <<<PROMPT
                        Given:
                        - Name: "{$name}"
                        - Description: "{$description}"

                        Return ONLY valid JSON with these fields: {
                         "category_name": REQUIRED. Must be selected strictly from "{$categories}".
                            Use the KEYS of the list (category names) as the value.
                            Match only if relevant to the product name/description.

                        "sub_category_name": OPTIONAL.
                            If provided, it MUST be selected strictly from "{$rawSubCategories}".
                            The chosen sub_category_name MUST have parent_id equal to the ID of the selected category_name from "{$categories}".
                            If no valid sub_category_name exists that matches both the product name/description and the selected category, then leave "sub_category_name" empty.
                            Do not invent, guess, or suggest categories outside the provided lists.
                        "brand": from "{$brands}" //optional,
                        "search_tags": 3–5 keywords from name/description,
                        "units": from: must be from "{$units}" and string, optional
                        }

                        Rules for SHOP OR ECOMMERCE:
                        - Emphasize brand, style, and product type.
                       - Categories must be chosen only from provided lists and must be a string.
                        - Categories/sub-categories must be chosen only from provided lists.
                        - Brand must be from provided list if relevant.
                        - Do not invent new values.
                        - Output must be valid JSON (json_decode in PHP).

                        Options:
                        [CATEGORIES] {$categories}
                        [SUB CATEGORIES] {$subCategories}
                        [BRAND] {$brands}
                        [UNITS] {$units}

                    === STRUCTURED RESPONSE FORMAT ===
                    - Output ONLY the JSON object.
                    - Do NOT include any explanations, comments, or markdown formatting.
                    - Do NOT wrap the JSON in ```json or any other code block.
                    - Ensure the JSON is syntactically valid for json_decode in PHP.
                 PROMPT;

        return $promptText;
    }

    public function PriceOthersAutoFill(string $name, $description = null)
    {
        $currency = Helpers::currency_symbol();

        $productInfo = $description
            ? "Product name: \"{$name}\". Description: \"" . addslashes($description) . "\"."
            : "Product name: \"{$name}\".";

        $promptText = <<<PROMPT
                  You are an expert pricing analyst.

                  Given the following product information:

                  {$productInfo}

                  Using the currency symbol "{$currency}", provide ONLY a JSON object with pricing details below.
                  Set realistic values based on the product info and currency.

                  The JSON must contain exactly these fields:

                  {
                    "unit_price": 100.00, // must be competitive or standard price based on {$productInfo} accordeing to currency and market demand
                    "minimum_order_quantity": 1-10, // any positive random integer
                    "discount_amount": 0.00, // this is the percentage of the unit_price, this must be less than unit_price and intiger and can be 0 or random and a required field

                  }

                  IMPORTANT: Return ONLY the pure JSON text with no markdown, no code fences, no extra text or explanation.
                  PROMPT;

        return $promptText;
    }
    public function seoSectionAutoFill(string $name, $description = null)
    {
        $productInfo = $description
            ? "Product name: \"{$name}\". Description: \"" . addslashes($description) . "\"."
            : "Product name: \"{$name}\".";

        $promptText = <<<PROMPT
                    You are an expert SEO content writer and technical SEO specialist.

                    Given the following product information:

                    {$productInfo}

                    Generate ONLY a JSON object with the following SEO meta fields:

                    {
                      "meta_title": "",                  // Concise SEO title (max 100 chars)
                      "meta_description": "",            // Compelling meta description (max 160 chars)

                      "meta_index": "index",             // Either "index" or "noindex"
                      "meta_no_follow": 0,               // 0 or 1 (boolean)
                      "meta_no_image_index": 0,          // 0 or 1
                      "meta_no_archive": 0,              // 0 or 1
                      "meta_no_snippet": 0,              // 0 or 1

                      "meta_max_snippet": 0,             // 0 or 1
                      "meta_max_snippet_value": -1,      // Number, -1 means no limit

                      "meta_max_video_preview": 0,       // 0 or 1
                      "meta_max_video_preview_value": -1,// Number, -1 means no limit

                      "meta_max_image_preview": 0,       // 0 or 1
                      "meta_max_image_preview_value": "large"  // One of "large", "medium", or "small"
                    }

                    Instructions:
                    - Use natural, clear language optimized for search engines.
                    - Choose values for index/noindex and booleans based on product info.
                    - Keep character limits for title and description.
                    - Return ONLY the pure JSON text without markdown, code fences, or explanations.
                    PROMPT;

        return $promptText;
    }


    public function variationSetupAutoFill(string $name, string $description)
    {
        $promptText = <<<PROMPT
                       You are an expert food product specialist with deep knowledge of food variations.

                        Given the following product:

                        Name: {$name}

                        Description: {$description}

                        Strict Rules:

                        Return ONLY a JSON array with the following structure for food variations (no explanations, no markdown, no text outside JSON).
                        "variation_name" must be a string // Must be based on '{$name}' and '{$description}' .
                        "required" is a boolean.
                        "selection_type" must be "multi" or "single".
                        "min" and "max" are integers and min must be grater then 0 and (min ≤ max) but selection_type: "single" both must be null .
                        Each variation must have at least 2 options.
                        "option_price" must be a positive number.
                        Generate at least one variation per food item, more if suggested by the description (e.g., rice, soup, sauces, toppings).
                        Options must be realistic and relevant to the food description.
                        JSON Schema Example (structure only, values are samples):
                        [
                        {
                        "variation_name": "Rice",
                        "required": true,
                        "selection_type": "multi",
                        "min": 1,
                        "max": 2,
                        "options": [
                        { "option_name": "Fried Rice", "option_price": 10 },
                        { "option_name": "White Rice", "option_price": 20 }
                        ]
                        },
                        {
                        "variation_name": "Soup",
                        "required": false,
                        "selection_type": "single",
                        "min": null,
                        "max": null,
                        "options": [
                        { "option_name": "Thai Soup", "option_price": 15 },
                        { "option_name": "Corn Soup", "option_price": 12 }
                        ]
                        }
                        ]

                        Output Format Rules:

                        Return ONLY the raw JSON array — no code blocks, no markdown, no explanation, no labels, no timestamps, no extra text.

                        The response must start with [ and end with ].

                        Ensure the JSON is syntactically valid for json_decode in PHP.
                PROMPT;

        return $promptText;
    }

    public function imageAnalysisAutoFill(string $langCode = "en")
    {
        $langCode = strtoupper($langCode);

        $promptText = <<<PROMPT
            You are an advanced food product analyst with strong skills in image recognition.

            Analyze the uploaded product image provided by the user.
            Your task is to generate a clean, concise, and professional product title for online stores.

            CRITICAL INSTRUCTION:
            - The output must be 100% in {$langCode} — this is mandatory.
            - Identify the main product in the image and name it clearly.
            - Do not add extra descriptions like "high quality" or "best".
            - Keep it short (35–70 characters), plain, and ready for listings.
            - Return only the translated product title as plain text in {$langCode}.

            PROMPT;

        return $promptText;
    }
    public function generateTitleSuggestions(array $keywords, string $langCode = "en")
    {
        $langCode = strtoupper($langCode);
        $keywordsText = implode(' ', $keywords);

        $promptText = <<<PROMPT
               You are an advanced e-commerce product analyst.

               Using the keywords "{$keywordsText}", generate 4 professional, clean, and concise product titles for online stores.

               CRITICAL INSTRUCTIONS:
               - The output must be 100% in {$langCode}.
               - Titles must use the keywords naturally.
               - Keep them short (35–70 characters), clear, and ready for listings.
               - Return exactly 4 titles in **plain JSON** format as shown below (do not include ```json``` or any extra markdown):

               {
                 "titles": [
                   "Title 1",
                   "Title 2",
                   "Title 3",
                   "Title 4"
                 ]
               }

               Do not include any extra explanation, only return the JSON.
               PROMPT;

        return $promptText;
    }
      public function otherVariationSetupAutoFill(string $name, string $description,$moduleType)
  {
    $resource = $this->ProductResource->getVariationData();
    $selectedValues = [];
    foreach ($resource['attributes'] as $attrName => $attrId) {
      $selectedValues[] = [
        'id' => (string)$attrId,
        'name' => $attrName,
        'variation' => ''
      ];
    }

    $attributesList = [];
    foreach ($selectedValues as $attr) {
      $attributesList[] = "{$attr['name']} (ID:{$attr['id']})";
    }
    $attributesString = implode(', ', $attributesList);


    $promptText = <<<PROMPT
                You are an expert product specialist with deep knowledge of product variations and attributes.

                Given the following product:
                - Name: {$name}
                - Description: {$description}

                Available configuration options from the system:
                - Attributes: {$attributesString}

                Generate ONLY a JSON object with the following structure for product variation setup based on the provided attributes and product name and description.:
                    Example:
                    {
                    "choice_attributes": [{id: x, name: "x", options: ["x1", "x2"]}]
                    }

                Rules:
                1. Use the provided attribute options when generating "choice_attributes".
                2. Must Select relevant options from the choice_attributes dynamically, based on product name and description.
                3. Do NOT invent options not present in the provided attributes.
                4. **Output Format Rule:** Return ONLY the raw JSON object — no code blocks, no markdown, no explanation, no labels, no timestamps, no extra text. The response must start with "{" and end with "}".

                PROMPT;

    return $promptText;
  }

}
