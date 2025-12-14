<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIAssistantService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('ai.anthropic.api_key');
        $this->model = config('ai.anthropic.model');
        $this->baseUrl = config('ai.anthropic.base_url');
        $this->maxTokens = config('ai.anthropic.max_tokens');
    }

    /**
     * Process content with AI based on the specified action
     */
    public function process(string $content, string $action, ?string $customInstruction = null): array
    {
        try {
            // Validate content length
            if (strlen($content) > config('ai.rate_limit.max_content_length')) {
                return [
                    'success' => false,
                    'error' => 'Content too long. Please reduce the content size.',
                ];
            }

            // Get the appropriate prompt
            $prompt = $this->buildPrompt($action, $content, $customInstruction);
            
            // Make API call
            $result = $this->callAnthropic($prompt, $content);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('AI Assistant Error: ' . $e->getMessage(), [
                'action' => $action,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'AI processing failed. Please try again.',
            ];
        }
    }

    /**
     * Build the prompt based on action type
     */
    protected function buildPrompt(string $action, string $content, ?string $customInstruction): string
    {
        $prompts = config('ai.prompts');
        
        switch ($action) {
            case 'format':
                return $prompts['format'];
            case 'enhance':
                return $prompts['enhance'];
            case 'equation':
                return $prompts['equation'];
            case 'structure':
                return $prompts['structure'];
            case 'simplify':
                return $prompts['simplify'];
            case 'custom':
                return $prompts['custom'] . "\n\nUSER INSTRUCTION: " . ($customInstruction ?? 'Improve this content.');
            default:
                return $prompts['format'];
        }
    }

    /**
     * Call Anthropic Claude API
     */
    protected function callAnthropic(string $prompt, string $content): array
    {
        $systemPrompt = config('ai.prompts.system');

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(60)->post($this->baseUrl, [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'system' => $systemPrompt,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt . "\n\n---\nCONTENT TO PROCESS:\n---\n" . $content,
                ],
            ],
        ]);

        if ($response->failed()) {
            Log::error('Anthropic API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'AI service temporarily unavailable. Status: ' . $response->status(),
            ];
        }

        $data = $response->json();

        if (isset($data['content'][0]['text'])) {
            $processedContent = $data['content'][0]['text'];
            
            // Clean up any markdown code blocks that might have been added
            $processedContent = $this->cleanResponse($processedContent);

            return [
                'success' => true,
                'content' => $processedContent,
                'usage' => [
                    'input_tokens' => $data['usage']['input_tokens'] ?? 0,
                    'output_tokens' => $data['usage']['output_tokens'] ?? 0,
                ],
            ];
        }

        return [
            'success' => false,
            'error' => 'Invalid response from AI service.',
        ];
    }

    /**
     * Clean the AI response to ensure valid HTML
     */
    protected function cleanResponse(string $response): string
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/^```html?\s*/i', '', $response);
        $response = preg_replace('/\s*```$/i', '', $response);
        
        // Remove any leading/trailing whitespace
        $response = trim($response);
        
        // Ensure we don't have escaped HTML
        if (strpos($response, '&lt;') !== false && strpos($response, '<') === false) {
            $response = html_entity_decode($response);
        }

        return $response;
    }

    /**
     * Get available actions with descriptions
     */
    public static function getAvailableActions(): array
    {
        return [
            'format' => [
                'name' => 'Magic Format',
                'icon' => 'fas fa-magic',
                'description' => 'Auto-fix spacing, lists, tables, and formatting issues',
                'color' => 'purple',
            ],
            'enhance' => [
                'name' => 'Enhance Quality',
                'icon' => 'fas fa-star',
                'description' => 'Improve grammar, clarity, and academic language',
                'color' => 'yellow',
            ],
            'equation' => [
                'name' => 'Format Equations',
                'icon' => 'fas fa-square-root-alt',
                'description' => 'Convert text to proper math symbols (x^2 → x²)',
                'color' => 'blue',
            ],
            'structure' => [
                'name' => 'Fix Structure',
                'icon' => 'fas fa-sitemap',
                'description' => 'Restructure into proper exam format with numbering',
                'color' => 'green',
            ],
            'simplify' => [
                'name' => 'Simplify Language',
                'icon' => 'fas fa-compress-alt',
                'description' => 'Make instructions clearer without changing difficulty',
                'color' => 'orange',
            ],
            'custom' => [
                'name' => 'Custom Request',
                'icon' => 'fas fa-edit',
                'description' => 'Type your own instruction for the AI',
                'color' => 'indigo',
            ],
        ];
    }
}
