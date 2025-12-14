<?php

namespace App\Http\Controllers;

use App\Services\AIAssistantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class AIAssistantController extends Controller
{
    protected AIAssistantService $aiService;

    public function __construct(AIAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Process content with AI
     */
    public function process(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'content' => 'required|string|max:50000',
            'action' => 'required|string|in:format,enhance,equation,structure,simplify,custom',
            'instruction' => 'nullable|string|max:1000',
        ]);

        // Rate limiting - 15 requests per minute per user
        $userId = session('user_id', $request->ip());
        $rateLimitKey = 'ai-assistant:' . $userId;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 15)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return response()->json([
                'success' => false,
                'error' => "Too many requests. Please wait {$seconds} seconds before trying again.",
            ], 429);
        }

        RateLimiter::hit($rateLimitKey, 60);

        // Process with AI
        $result = $this->aiService->process(
            $validated['content'],
            $validated['action'],
            $validated['instruction'] ?? null
        );

        if ($result['success']) {
            Log::info('AI Assistant used', [
                'user' => $userId,
                'action' => $validated['action'],
                'input_length' => strlen($validated['content']),
                'output_length' => strlen($result['content']),
            ]);

            return response()->json([
                'success' => true,
                'content' => $result['content'],
                'message' => 'Content processed successfully!',
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Processing failed. Please try again.',
        ], 500);
    }

    /**
     * Get available AI actions
     */
    public function actions(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'actions' => AIAssistantService::getAvailableActions(),
        ]);
    }

    /**
     * Health check for AI service
     */
    public function health(): JsonResponse
    {
        $apiKey = config('ai.anthropic.api_key');
        
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'status' => 'not_configured',
                'message' => 'AI service is not configured.',
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 'ready',
            'message' => 'AI assistant is ready to use.',
            'model' => config('ai.anthropic.model'),
        ]);
    }
}
