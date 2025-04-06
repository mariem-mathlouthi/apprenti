<?php

namespace App\Http\Controllers;

use App\Models\Quizz;
use Illuminate\Http\Request;
use App\Models\ReponseEtudiant;
use Illuminate\Support\Facades\Auth;

class QuizzController extends Controller
{
    /**
     * Récupérer tous les quizz.
     */
    public function getAllQuizz()
    {
        return response()->json(Quizz::all(), 200);
    }

    /**
     * Ajouter un nouveau quizz.
     */
    public function addQuizz(Request $request)
    {
        $request->validate([
            'idCours' => 'required|exists:cours,id',
            'idTuteur' => 'nullable|exists:tuteurs,id',
            'titre' => 'required|string|max:255',
            'question' => 'required|string',
            'reponseCorrecte' => 'required|array', // Maintenant un tableau JSON
            'reponsesFausses' => 'required|array', // Assure que c'est bien un tableau
            'score' => 'required|integer|min:1',
        ]);

        $quizz = Quizz::create([
            'idCours' => $request->idCours,
            'idTuteur' => $request->idTuteur,
            'titre' => $request->titre,
            'question' => $request->question,
            'reponseCorrecte' => json_encode($request->reponseCorrecte), // Convertir en JSON
            'reponsesFausses' => json_encode($request->reponsesFausses), // Convertir en JSON
            'score' => $request->score
        ]);

        return response()->json($quizz, 201);
    }

    /**
     * Récupérer un quizz par son ID.
     */
    public function getQuizzById($id)
    {
        try {
            $quizz = Quizz::where('id', $id)
                ->with(['cours' => function($query) {
                    $query->select('id', 'titre');
                }])
                ->firstOrFail();

            // Décoder les colonnes JSON pour les renvoyer sous forme de tableau
            $quizz->reponseCorrecte = json_decode($quizz->reponseCorrecte, true);
            $quizz->reponsesFausses = json_decode($quizz->reponsesFausses, true);

            return response()->json([
                'success' => true,
                'data' => $quizz
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Quizz introuvable'
            ], 404);
        }
    }

    /**
     * Modifier un quizz existant.
     */
    public function updateQuizz(Request $request, $id)
    {
        $quizz = Quizz::findOrFail($id);

        $request->validate([
            'idCours' => 'required|exists:cours,id',
            'titre' => 'required|string|max:255',
            'question' => 'required|string',
            'reponseCorrecte' => 'required|array', // Maintenant un tableau JSON
            'reponsesFausses' => 'sometimes|array', // Optionnel, mais doit être un tableau
            'score' => 'required|integer|min:1',
        ]);

        $quizz->update([
            'idCours' => $request->idCours,
            'titre' => $request->titre,
            'question' => $request->question,
            'reponseCorrecte' => json_encode($request->reponseCorrecte), // Convertir en JSON
            'reponsesFausses' => $request->has('reponsesFausses')
                ? json_encode($request->reponsesFausses)
                : $quizz->reponsesFausses,
            'score' => $request->score
        ]);

        return response()->json($quizz, 200);
    }

    /**
     * Supprimer un quizz.
     */
    public function deleteQuizz($id)
    {
        $quizz = Quizz::findOrFail($id);
        $quizz->delete();
        return response()->json(['message' => 'Quizz supprimé avec succès'], 200);
    }

    /**
     * Récupérer les quizz par tuteur.
     */
    public function getQuizzByTuteur(Request $request)
    {
        $request->validate([
            'tuteurId' => 'required|exists:tuteurs,id'
        ]);

        $quizz = Quizz::where('idTuteur', $request->tuteurId)
            ->with(['cours' => function($query) {
                $query->select('id', 'titre');
            }])
            ->get();

        // Décoder JSON avant d'envoyer la réponse
        foreach ($quizz as $q) {
            $q->reponseCorrecte = json_decode($q->reponseCorrecte, true);
            $q->reponsesFausses = json_decode($q->reponsesFausses, true);
        }

        return response()->json($quizz, 200);
    }

    public function submitReponses(Request $request, $quizzId)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $request->validate([
        'reponses' => 'required|array',
        'reponses.*' => 'required|string'
    ]);

    $quizz = Quizz::findOrFail($quizzId);
    $user = Auth::user();

    $correctAnswers = $this->parseAnswers($quizz->reponseCorrecte);
    $isCorrect = empty(array_diff($request->reponses, $correctAnswers)) 
              && empty(array_diff($correctAnswers, $request->reponses));

    $reponse = ReponseEtudiant::create([
        'etudiant_id' => $user->id,
        'quizz_id' => $quizzId,
        'reponses' => json_encode($request->reponses),
        'est_correcte' => $isCorrect,
        'score_obtenu' => $isCorrect ? $quizz->score : 0
    ]);

    return response()->json([
        'success' => true,
        'score' => $reponse->score_obtenu,
        'is_correct' => $isCorrect,
        'correct_answers' => $correctAnswers
    ]);
}
}
