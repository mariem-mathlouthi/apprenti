<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    }

    /**
     * Send message to Gemini AI and get response
     */
    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000',
                'conversation_history' => 'nullable|array'
            ]);

            if (!$this->apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Clé API Gemini non configurée'
                ], 500);
            }

            // Prepare the conversation context
            $conversationHistory = $request->input('conversation_history', []);
            
            // Add system context for the apprenti platform
            $systemContext = "Vous êtes un assistant IA utile pour la plateforme Apprenti+, un système de gestion d'apprentissage et de stages. Vous aidez les étudiants, tuteurs et entreprises avec des questions sur les stages, cours, candidatures et fonctionnalités de la plateforme. Soyez amical, professionnel et concis dans vos réponses. Si vous ne connaissez pas quelque chose de spécifique sur la plateforme, reconnaissez-le et offrez des conseils généraux utiles. Répondez toujours en français.";
            
            // Build the conversation parts
            $parts = [];
            
            // Add system context
            $parts[] = ['text' => $systemContext];
            
            // Add conversation history
            foreach ($conversationHistory as $message) {
                $parts[] = ['text' => $message['role'] . ': ' . $message['content']];
            }
            
            // Add current user message
            $parts[] = ['text' => 'User: ' . $request->input('message')];

            // Prepare the request payload
            $payload = [
                'contents' => [
                    [
                        'parts' => $parts
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ];

            // Make the API request to Gemini
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    return response()->json([
                        'success' => true,
                        'message' => $aiResponse,
                        'timestamp' => now()->toISOString()
                    ]);
                } else {
                    Log::error('Unexpected Gemini API response structure', ['response' => $data]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Réponse inattendue du service IA'
                    ], 500);
                }
            } else {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Service IA temporairement indisponible'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Gemini chat error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors du traitement de votre demande'
            ], 500);
        }
    }

    /**
     * Get quick suggestions for common questions
     */
    public function getQuickSuggestions()
    {
        $suggestions = [
            "Comment postuler pour un stage ?",
            "Quels cours sont disponibles ?",
            "Comment contacter mon tuteur ?",
            "Comment télécharger mon CV ?",
            "Quelles sont les fonctionnalités de la plateforme ?",
            "Comment planifier un rendez-vous ?",
            "Comment suivre mes candidatures ?",
            "Quels documents ai-je besoin ?"
        ];

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Health check for the Gemini service
     */
    public function healthCheck()
    {
        if (!$this->apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Clé API non configurée'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service Gemini disponible',
            'timestamp' => now()->toISOString()
        ]);
    }
}
