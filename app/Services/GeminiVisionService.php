<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use Illuminate\Support\Str;

class GeminiVisionService
{
    protected ?string $apiKey;
    protected ?string $apiUrl;

    /**
     * Constructor to prepare the API key and endpoint.
     */
    public function __construct()
    {
        $this->apiKey = Setting::where('key', 'GEMINI_API_KEY')->first()?->value;

                    if (empty($this->apiKey)) {
                        throw new \Exception('Gemini API Key not found in settings.');
                    }
        
                    $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";
    }

    public function testApiKey(): bool
    {
        try {
            $prompt = "Say 'hello'."; // Simple prompt to test connectivity
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ];

            $response = Http::withHeaders(['X-goog-api-key' => $this->apiKey])->post($this->apiUrl, $payload);

            if ($response->failed()) {
                Log::error('Gemini API key test failed.', ['status' => $response->status(), 'body' => $response->body()]);
                return false;
            }

            $responseData = $response->json();
            $jsonText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            return !empty($jsonText); // If we get any response, consider the key valid
        } catch (Exception $e) {
            Log::error('Gemini API key test exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generates SEO content for a given category name.
     *
     * @param string $categoryName The name of the category.
     * @return array SEO details.
     * @throws \Exception if the API call fails.
     */
    public function generateSeoForCategory(string $categoryName): array
    {
        $prompt = <<<PROMPT
        Actúa como un Copywriter y Especialista SEO Senior para "Maye Shop", una marca colombiana de alta costura que define la moda de lujo con diseños exclusivos, sexys y elegantes para una mujer poderosa y sofisticada. Tu tarea es generar el contenido meta para la página de la categoría: "{$categoryName}".

        Tu respuesta DEBE ser un único objeto JSON válido, sin markdown ni texto adicional. La estructura debe ser la siguiente:

        {
          "meta_title": "Genera un título SEO magnético y optimizado (máximo 60 caracteres, *sin excederse bajo ninguna circunstancia*). Debe ser evocador y reflejar exclusividad. Fórmula sugerida: '[Nombre de la Categoría] de Lujo | Colección Exclusiva | Maye Shop'. Ejemplo para 'Vestidos de Fiesta': 'Vestidos de Fiesta de Lujo | Colección Exclusiva | Maye Shop'.",
          "meta_description": "Escribe una meta descripción seductora y *estrictamente* convincente (máximo 160 caracteres, *sin excederse bajo ninguna circunstancia*). Debe evocar el lujo y la exclusividad de la colección. Finaliza con una llamada a la acción sofisticada. Ejemplo para 'Vestidos de Fiesta': 'Eleva cada celebración con nuestra colección de vestidos de fiesta. Diseños que capturan la luz y todas las miradas. Exclusividad y poder, solo en Maye Shop.'",
          "meta_keywords": "Genera una lista completa de 10 a 15 palabras clave estratégicas, separadas por comas. Combina términos de cola corta (short-tail) y cola larga (long-tail). Incluye: el término principal, sinónimos, LSI (términos semánticamente relacionados) y búsquedas específicas de Colombia. Ejemplo para 'Vestidos de Fiesta': 'vestidos de fiesta de lujo, comprar vestidos de gala online, vestidos de noche exclusivos, moda para eventos en Colombia, diseñador Maye Shop, vestidos para bodas, trajes de coctel elegantes, Maye Shop vestidos de fiesta, ropa de lujo para celebraciones, outfits de noche Bogotá'."
        }
PROMPT;

        try {
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ];

            $response = Http::withHeaders(['X-goog-api-key' => $this->apiKey])->post($this->apiUrl, $payload);

            if ($response->failed()) {
                Log::error('Gemini API request failed for category SEO.', ['status' => $response->status(), 'body' => $response->body()]);
                throw new Exception('La petición a la API de Gemini para SEO de categoría falló. Código: ' . $response->status());
            }

            $responseData = $response->json();
            $jsonText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($jsonText)) {
                Log::warning('Gemini API response part was empty for category SEO.', ['response' => $responseData]);
                throw new Exception('La respuesta de la IA para SEO de categoría estaba vacía.');
            }

            $decodedJson = json_decode($jsonText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode JSON from Gemini API for category SEO.', ['json_text' => $jsonText]);
                throw new Exception('La IA devolvió un formato JSON inválido para SEO de categoría.');
            }

            // Log original values before truncation for debugging
            Log::warning('Gemini SEO Category - Original meta_title:', ['title' => $decodedJson['meta_title'] ?? 'N/A', 'length' => Str::length($decodedJson['meta_title'] ?? '')]);
            Log::warning('Gemini SEO Category - Original meta_description:', ['description' => $decodedJson['meta_description'] ?? 'N/A', 'length' => Str::length($decodedJson['meta_description'] ?? '')]);

            // Truncate SEO fields to ensure they fit within character limits
            if (isset($decodedJson['meta_title'])) {
                $decodedJson['meta_title'] = Str::limit($decodedJson['meta_title'], 60, '');
            }
            if (isset($decodedJson['meta_description'])) {
                $decodedJson['meta_description'] = Str::limit($decodedJson['meta_description'], 160, '');
            }

            // Log truncated values for debugging
            Log::warning('Gemini SEO Category - Truncated meta_title:', ['title' => $decodedJson['meta_title'] ?? 'N/A', 'length' => Str::length($decodedJson['meta_title'] ?? '')]);
            Log::warning('Gemini SEO Category - Truncated meta_description:', ['description' => $decodedJson['meta_description'] ?? 'N/A', 'length' => Str::length($decodedJson['meta_description'] ?? '')]);

            return $decodedJson;

        } catch (Exception $e) {
            Log::error('Gemini Service Error for Category SEO: ' . $e->getMessage());
            throw $e; // Re-throw the original exception
        }
    }

    /**
     * Generates SEO content for a given product type name.
     *
     * @param string $productTypeName The name of the product type.
     * @return array SEO details.
     * @throws \Exception if the API call fails.
     */
    public function generateSeoForProductType(string $productTypeName, string $gender = 'Unisex'): array
    {
        $genderContextPhrase = '';
        $genderTargetAudience = '';

        if ($gender === 'Hombre') {
            $genderContextPhrase = ' para hombre';
            $genderTargetAudience = 'dirigido a un público masculino';
        } elseif ($gender === 'Mujer') {
            $genderContextPhrase = ' para mujer';
            $genderTargetAudience = 'dirigido a un público femenino';
        } else {
            $genderTargetAudience = 'dirigido a un público unisex';
        }

        $prompt = <<<PROMPT
        Actúa como un Copywriter y Especialista SEO Senior con más de 10 años de experiencia en e-commerce de moda de lujo. Tu cliente es "Maye Shop", una marca colombiana de alta costura reconocida por sus diseños exclusivos, elegantes y únicos, que empoderan a quienes los visten. Tu objetivo es generar contenido meta (meta_title, meta_description, meta_keywords) de ALTA CALIDAD y ALTAMENTE OPTIMIZADO para la página de un tipo de producto específico.

        El tipo de producto es: "{$productTypeName}".
        El género asociado a este tipo de producto es: "{$gender}".

        Considera profundamente el tipo de producto, el género ({$genderTargetAudience}) y la esencia de "Maye Shop" (lujo, exclusividad, empoderamiento, diseño único) para crear un contenido que:
        - Sea persuasivo y atractivo para el público objetivo.
        - Integre palabras clave de forma natural y estratégica, evitando el "keyword stuffing".
        - Refleje la propuesta de valor única de la marca.
        - Impulse el CTR (Click-Through Rate) en los resultados de búsqueda.

        Tu respuesta DEBE ser un único objeto JSON válido, sin markdown, comentarios ni texto adicional. La estructura debe ser la siguiente:

        {
          "meta_title": "Genera un título SEO magnético y optimizado (máximo 60 caracteres, *sin excederse bajo ninguna circunstancia*). Incluye el nombre del tipo de producto, la marca y un diferenciador clave. Ejemplo: 'Vestidos de Lujo para mujer | Diseños Exclusivos | Maye Shop'",
          "meta_description": "Crea una meta descripción seductora y *estrictamente* convincente (máximo 160 caracteres, *sin excederse bajo ninguna circunstancia*). Destaca los beneficios, la exclusividad y el estilo. Incluye una llamada a la acción sofisticada. Enfócate en el tipo de producto y al género para personalizar el mensaje.",
          "meta_keywords": "Genera una lista de 10 a 15 palabras clave estratégicas, separadas por comas. Incluye términos de cola corta y larga, sinónimos, LSI y búsquedas relevantes en Colombia. Adapta las palabras clave al tipo de producto y al género para maximizar la relevancia."
        }
PROMPT;

        try {
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ];

            $response = Http::withHeaders(['X-goog-api-key' => $this->apiKey])->post($this->apiUrl, $payload);

            if ($response->failed()) {
                Log::error('Gemini API request failed for product type SEO.', ['status' => $response->status(), 'body' => $response->body()]);
                throw new Exception('La petición a la API de Gemini para SEO de tipo de producto falló. Código: ' . $response->status());
            }

            $responseData = $response->json();
            $jsonText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($jsonText)) {
                Log::warning('Gemini API response part was empty for product type SEO.', ['response' => $responseData]);
                throw new Exception('La respuesta de la IA para SEO de tipo de producto estaba vacía.');
            }

            $decodedJson = json_decode($jsonText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode JSON from Gemini API for product type SEO.', ['json_text' => $jsonText]);
                throw new Exception('La IA devolvió un formato JSON inválido para SEO de tipo de producto.');
            }

            // Log original values before truncation for debugging
            Log::warning('Gemini SEO Product Type - Original meta_title:', ['title' => $decodedJson['meta_title'] ?? 'N/A', 'length' => Str::length($decodedJson['meta_title'] ?? '')]);
            Log::warning('Gemini SEO Product Type - Original meta_description:', ['description' => $decodedJson['meta_description'] ?? 'N/A', 'length' => Str::length($decodedJson['meta_description'] ?? '')]);

            // Truncate SEO fields to ensure they fit within character limits
            if (isset($decodedJson['meta_title'])) {
                $decodedJson['meta_title'] = Str::limit($decodedJson['meta_title'], 60, '');
            }
            if (isset($decodedJson['meta_description'])) {
                $decodedJson['meta_description'] = Str::limit($decodedJson['meta_description'], 160, '');
            }

            // Log truncated values for debugging
            Log::warning('Gemini SEO Product Type - Truncated meta_title:', ['title' => $decodedJson['meta_title'] ?? 'N/A', 'length' => Str::length($decodedJson['meta_title'] ?? '')]);
            Log::warning('Gemini SEO Product Type - Truncated meta_description:', ['description' => $decodedJson['meta_description'] ?? 'N/A', 'length' => Str::length($decodedJson['meta_description'] ?? '')]);

            return $decodedJson;

        } catch (Exception $e) {
            Log::error('Gemini Service Error for Product Type SEO: ' . $e->getMessage());
            throw $e; // Re-throw the original exception
        }
    }

    /**
     * Generates SEO content for a given product name.
     *
     * @param string $productName The name of the product.
     * @return array SEO details.
     * @throws \Exception if the API call fails.
     */
    public function generateSeoForProduct(string $productName): array
    {
        $prompt = <<<PROMPT
        Actúa como un Copywriter y Especialista SEO Senior para "Maye Shop", una marca colombiana de alta costura que define la moda de lujo con diseños exclusivos, sexys y elegantes para una mujer poderosa y sofisticada. Tu tarea es generar el contenido meta para la página del producto: "{$productName}".

        Tu respuesta DEBE ser un único objeto JSON válido, sin markdown ni texto adicional. La estructura debe ser la siguiente:

        {
          "seo_title": "Genera un título SEO magnético y optimizado (máximo 60 caracteres, *sin excederse bajo ninguna circunstancia*). Debe ser evocador y reflejar exclusividad. Fórmula sugerida: '[Nombre del Producto] de Lujo | Exclusivo Maye Shop'. Ejemplo para 'Vestido de Noche Elegante': 'Vestido de Noche Elegante | Exclusivo Maye Shop'.",
          "seo_description": "Escribe una meta descripción seductora y *estrictamente* convincente (máximo 160 caracteres, *sin excederse bajo ninguna circunstancia*). Debe evocar el lujo y la exclusividad del producto. Finaliza con una llamada a la acción sofisticada. Ejemplo para 'Vestido de Noche Elegante': 'Descubre la elegancia en nuestro Vestido de Noche Elegante. Diseño exclusivo que realza tu figura y te hace brillar. Encuentra tu estilo único en Maye Shop.'",
          "seo_keywords": "Genera una lista completa de 10 a 15 palabras clave estratégicas, separadas por comas. Combina términos de cola corta (short-tail) y cola larga (long-tail). Incluye: el término principal, sinónimos, LSI (términos semánticamente relacionados) y búsquedas específicas de Colombia. Ejemplo para 'Vestido de Noche Elegante': 'vestido de noche elegante, comprar vestido de lujo, vestido de fiesta exclusivo, moda de alta costura Colombia, Maye Shop vestido, vestido para gala, traje de noche sofisticado, Maye Shop online, ropa de lujo mujer, vestido de diseño Bogotá'."
        }
PROMPT;

        try {
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ];

            $response = Http::withHeaders(['X-goog-api-key' => $this->apiKey])->post($this->apiUrl, $payload);

            if ($response->failed()) {
                Log::error('Gemini API request failed for product SEO.', ['status' => $response->status(), 'body' => $response->body()]);
                throw new Exception('La petición a la API de Gemini para SEO de producto falló. Código: ' . $response->status());
            }

            $responseData = $response->json();
            $jsonText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($jsonText)) {
                Log::warning('Gemini API response part was empty for product SEO.', ['response' => $responseData]);
                throw new Exception('La respuesta de la IA para SEO de producto estaba vacía.');
            }

            $decodedJson = json_decode($jsonText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode JSON from Gemini API for product SEO.', ['json_text' => $jsonText]);
                throw new Exception('La IA devolvió un formato JSON inválido para SEO de producto.');
            }

            // Log original values before truncation for debugging
            Log::warning('Gemini SEO Product - Original seo_title:', ['title' => $decodedJson['seo_title'] ?? 'N/A', 'length' => Str::length($decodedJson['seo_title'] ?? '')]);
            Log::warning('Gemini SEO Product - Original seo_description:', ['description' => $decodedJson['seo_description'] ?? 'N/A', 'length' => Str::length($decodedJson['seo_description'] ?? '')]);
            Log::warning('Gemini SEO Product - Original seo_keywords:', ['keywords' => $decodedJson['seo_keywords'] ?? 'N/A']);

            // Truncate SEO fields to ensure they fit within character limits
            if (isset($decodedJson['seo_title'])) {
                $decodedJson['seo_title'] = Str::limit($decodedJson['seo_title'], 60, '');
            }
            if (isset($decodedJson['seo_description'])) {
                $decodedJson['seo_description'] = Str::limit($decodedJson['seo_description'], 160, '');
            }

            // Log truncated values for debugging
            Log::warning('Gemini SEO Product - Truncated seo_title:', ['title' => $decodedJson['seo_title'] ?? 'N/A', 'length' => Str::length($decodedJson['seo_title'] ?? '')]);
            Log::warning('Gemini SEO Product - Truncated seo_description:', ['description' => $decodedJson['seo_description'] ?? 'N/A', 'length' => Str::length($decodedJson['seo_description'] ?? '')]);
            Log::warning('Gemini SEO Product - Truncated seo_keywords:', ['keywords' => $decodedJson['seo_keywords'] ?? 'N/A']);

            return $decodedJson;

        } catch (Exception $e) {
            Log::error('Gemini Service Error for Product SEO: ' . $e->getMessage());
            throw $e; // Re-throw the original exception
        }
    }

    /**
     * Analyzes a product image from its file path and returns a structured array of product details.
     *
     * @param string $filePath The path to the file within Laravel's public storage disk.
     * @return array The generated product details.
     * @throws \Exception if the file is not found or the API call fails.
     */
    public function describeProductImage(string $filePath, array $availableColorNames = []): array
    {
        $imageContent = null;
        $mimeType = null;

        if (Str::startsWith($filePath, Storage::disk('public')->path(''))) {
            // It's an absolute path to a file in public storage
            $imageContent = file_get_contents($filePath);
            $mimeType = mime_content_type($filePath);
        } elseif (Storage::disk('public')->exists($filePath)) {
            // It's a relative path in public storage
            $imageContent = Storage::disk('public')->get($filePath);
            $mimeType = Storage::disk('public')->mimeType($filePath);
        } else {
            throw new Exception("File not found at path: {$filePath}");
        }

        if (!$mimeType) {
            throw new Exception("Could not determine MIME type for file: {$filePath}");
        }

        return $this->generateProductDetailsFromContent($imageContent, $mimeType);
    }

    /**
     * Analyzes a product image from its raw content and returns a structured array of product details.
     *
     * @param string $imageContent The raw binary content of the image.
     * @param string $mimeType The MIME type of the image (e.g., 'image/jpeg').
     * @return array The generated product details.
     * @throws \Exception if the API call fails.
     */
    public function generateProductDetailsFromContent(
        string $imageContent,
        string $mimeType,
        ?string $category = null,
        ?string $productType = null,
        array $collections = [],
        array $colors = [],
        array $occasionTags = [],
        array $styleDesignTags = [],
        array $materialTags = [],
        array $seasonTrendTags = []
    ): array {
        $additionalDetailsText = "";
        if ($category) {
            $additionalDetailsText .= "- Categoría: {$category}\n";
        }
        if ($productType) {
            $additionalDetailsText .= "- Tipo de Producto: {$productType}\n";
        }
        if (!empty($collections)) {
            $additionalDetailsText .= "- Colecciones: " . implode(", ", $collections) . "\n";
        }
        if (!empty($colors)) {
            $additionalDetailsText .= "- Colores: " . implode(", ", $colors) . "\n";
        }
        if (!empty($occasionTags)) {
            $additionalDetailsText .= "- Atributos (Ocasión): " . implode(", ", $occasionTags) . "\n";
        }
        if (!empty($styleDesignTags)) {
            $additionalDetailsText .= "- Atributos (Estilo/Diseño): " . implode(", ", $styleDesignTags) . "\n";
        }
        if (!empty($materialTags)) {
            $additionalDetailsText .= "- Atributos (Material): " . implode(", ", $materialTags) . "\n";
        }
        if (!empty($seasonTrendTags)) {
            $additionalDetailsText .= "- Atributos (Temporada/Tendencia): " . implode(", ", $seasonTrendTags) . "\n";
        }

        $promptBase = "Actúa como un copywriter experto en marketing de contenidos y SEO para \"Maye\", una marca de moda de lujo colombiana que vende productos elegantes y sexys para mujeres. Tu tarea es analizar la imagen de un producto y generar contenido de producto excepcional en español, optimizado para atraer y convertir.\n\n";

        if (!empty($additionalDetailsText)) {
            $promptBase .= "Detalles adicionales del producto para considerar:\n" . $additionalDetailsText . "\n";
        }

        $prompt = $promptBase . <<<'PROMPT_END'
        Genera un nombre de producto, una descripción corta, una descripción larga y el contenido meta (meta_title, meta_description, meta_keywords) para el producto en la imagen. La descripción larga debe ser un párrafo único, fluido y extremadamente evocador, con formato HTML.

        Tu respuesta DEBE ser un único objeto JSON válido, sin markdown ni texto adicional. La estructura debe ser la siguiente:

        {
          "name": "Genera un nombre ÚNICO, atractivo y evocador para el producto (máx. 100 caracteres). Debe reflejar el estilo, la esencia y los detalles clave del producto. Ejemplo: 'Vestido de Noche Aurora Boreal' o 'Conjunto Sastre Elegancia Urbana'.",
          "short_description": "Crea una descripción corta y contundente (máx. 400 caracteres) en un solo párrafo HTML. Usa etiquetas HTML básicas como <p>, <strong>, <em>. Debe ser magnética, capturar la esencia del producto y generar deseo inmediato. Ideal para vistas previas de productos o redes sociales. Ejemplo: '<p>El epítome de la audacia. Nuestro vestido X, con su silueta esculpida y detalles inesperados, es tu declaración de poder para las noches que importan.</p>'",
          "long_description": "Crea una descripción de producto para un e-commerce de lujo que sea un párrafo único, largo, fluido y extremadamente evocador. El texto debe ser de **calidad editorial**, como un artículo en una revista de moda de alta gama. Debe tener aproximadamente entre 50 y 600 palabras. El tono debe ser sofisticado, poderoso y sensorial, reflejando la marca 'Maye'. **Es IMPRESCINDIBLE que el texto retorne con etiquetas HTML válidas para párrafos (<p>...</p>) y otras etiquetas de formato (<strong>, <em>, <ul>, <li>, <a>) según sea apropiado para estructurar el contenido de forma rica y legible.** Puedes usar múltiples párrafos si es necesario para la legibilidad, pero mantén un flujo narrativo coherente. Ejemplo de formato HTML esperado: '<p>Este producto es una obra maestra de la alta costura...</p><p>Confeccionado con seda de morera, su caída es impecable...</p><ul><li>Detalle 1</li><li>Detalle 2</li></ul>'.

**Instrucciones Clave para la Descripción Larga:**

1.  **Formato HTML Obligatorio:** Asegúrate de que todo el contenido de la descripción esté envuelto en etiquetas HTML apropiadas, especialmente <p> para cada párrafo. No devuelvas texto plano.
2.  **Riqueza Descriptiva:** Entrelaza los detalles del producto de forma orgánica dentro de la narrativa. Describe la inspiración, la silueta, el tejido de lujo (ej: 'un crepé de seda que susurra con cada movimiento'), los detalles exquisitos (ej: 'delicados cristales de Swarovski que capturan la luz'), el color y la sensación de la prenda sobre la piel.
3.  **Enfoque en el 'Storytelling':** No listes características. En su lugar, narra la experiencia de llevar el producto. Pinta una imagen de la mujer que lo lleva, dónde está y cómo se siente: segura, poderosa, inolvidable.
4.  **Optimización SEO Integrada:** Incluye palabras clave relevantes para la moda de lujo en Colombia (ej: 'diseñador colombiano', 'producto de gala para Bogotá', 'look exclusivo para Medellín') de forma natural dentro del texto, sin que suene forzado.
5.  **Llamada a la Acción Sutil:** Finaliza con una frase que no sea un comando directo, sino una invitación aspiracional que encapsula el deseo por la prenda. Ejemplo: '...una pieza destinada a convertirse en el nuevo tesoro de tu armario.'

          "meta_title": "Genera un título SEO magnético y optimizado (máximo 60 caracteres, *sin excederse bajo ninguna circunstancia*). Debe ser evocador y reflejar exclusividad. Fórmula sugerida: '[Nombre del Producto] de Lujo | Exclusivo Maye Shop'. Ejemplo para 'Vestido de Noche Elegante': 'Vestido de Noche Elegante | Exclusivo Maye Shop'.",
          "meta_description": "Escribe una meta descripción seductora y *estrictamente* convincente (máximo 160 caracteres, *sin excederse bajo ninguna circunstancia*). Debe evocar el lujo y la exclusividad del producto. Finaliza con una llamada a la acción sofisticada. Ejemplo para 'Vestido de Noche Elegante': 'Descubre la elegancia en nuestro Vestido de Noche Elegante. Diseño exclusivo que realza tu figura y te hace brillar. Encuentra tu estilo único en Maye Shop.'",
          "seo_keywords": "Genera una lista completa de 10 a 15 palabras clave estratégicas, separadas por comas. Combina términos de cola corta (short-tail) y cola larga (long-tail). Incluye: el término principal, sinónimos, LSI (términos semánticamente relacionados) y búsquedas específicas de Colombia. Ejemplo para 'Vestido de Noche Elegante': 'vestido de noche elegante, comprar vestido de lujo, vestido de fiesta exclusivo, moda de alta costura Colombia, Maye Shop vestido, vestido para gala, traje de coctel sofisticado, Maye Shop online, ropa de lujo mujer, vestido de diseño Bogotá'.",
          "selected_colors": "Analiza la imagen del producto y selecciona un máximo de 3 colores del siguiente listado que mejor representen el producto. Devuelve los nombres exactos de los colores en un array de strings. Si no hay colores claros o relevantes, devuelve un array vacío. Listado de colores disponibles: [{{ implode(', ', $availableColorNames) }}]. Ejemplo: ['Rojo', 'Negro']"
        }
PROMPT_END;



        try {
            $base64Image = base64_encode($imageContent);

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $base64Image
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ];

            $response = Http::withHeaders(['X-goog-api-key' => $this->apiKey])->post($this->apiUrl, $payload);

            if ($response->failed()) {
                Log::error('Gemini API request failed.', ['status' => $response->status(), 'body' => $response->body()]);
                throw new Exception('La petición a la API de Gemini falló. Código: ' . $response->status());
            }

            $responseData = $response->json();
            Log::info('Gemini API successful response:', ['response' => $responseData]);

            $jsonText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($jsonText)) {
                Log::warning('Gemini API response part was empty or missing text.', ['response' => $responseData]);
                throw new Exception('La respuesta de la IA estaba vacía o no contenía texto.');
            }

            $decodedJson = json_decode($jsonText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode JSON from Gemini API.', ['json_text' => $jsonText]);
                throw new Exception('La IA devolvió un formato JSON inválido.');
            }

            // Truncate SEO fields to ensure they fit within character limits
            if (isset($decodedJson['seo_title'])) {
                $decodedJson['seo_title'] = Str::limit($decodedJson['seo_title'], 60, '');
            }
            if (isset($decodedJson['seo_description'])) {
                $decodedJson['seo_description'] = Str::limit($decodedJson['seo_description'], 160, '');
            }

            return $decodedJson;

        } catch (Exception $e) {
            Log::error('Gemini Service Error: ' . $e->getMessage());
            throw $e; // Re-throw the original exception
        }
    }
}
