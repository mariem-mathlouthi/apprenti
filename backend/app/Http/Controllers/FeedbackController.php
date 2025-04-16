<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Cours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Add this import
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    /**
     * Récupérer tous les feedbacks d'un cours
     */
    public function getFeedbacksByCourse($courseId)
    {
        try {
            $feedbacks = Feedback::with(['etudiant', 'cours'])
                ->where('cours_id', $courseId)
                ->get();

            return response()->json([
                'success' => true,
                'feedbacks' => $feedbacks
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des feedbacks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ajouter un nouveau feedback
     */
    public function createFeedback(Request $request)
    {
        try {
            $validated = $request->validate([
                'etudiant_id' => [
                    'required',
                    'exists:etudiants,id',
                    Rule::unique('feedbacks')->where(function ($query) use ($request) {
                        return $query->where('cours_id', $request->cours_id);
                    })
                ],
                'cours_id' => 'required|exists:cours,id',
                'note' => 'required|integer|between:1,5',
                'commentaire' => 'required|string|max:500'
            ]);

            $feedback = Feedback::create($validated);

            return response()->json([
                'message' => 'Feedback enregistré',
                'data' => $feedback
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Feedback error: '.$e->getMessage()); // Now properly imported
            return response()->json([
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Récupérer un feedback spécifique
     */
    public function getFeedback($id)
    {
        try {
            $feedback = Feedback::with(['etudiant', 'cours'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'feedback' => $feedback
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback non trouvé'
            ], 404);
        }
    }
}