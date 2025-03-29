<?php

namespace App\Http\Controllers;

use App\Models\Quizz;
use Illuminate\Http\Request;

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
            'reponseCorrecte' => 'required|string',
            'score' => 'required|integer|min:1',
        ]);
    
        $quizz = Quizz::create([
            'idCours' => $request->idCours,
            'idTuteur' => $request->idTuteur,
            'titre' => $request->titre,
            'question' => $request->question,
            'reponseCorrecte' => $request->reponseCorrecte,
            'reponsesFausses' => json_encode([]), // Initialiser avec un tableau JSON vide
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
            'reponseCorrecte' => 'required|string',
            'reponsesFausses' => 'sometimes|array', // Changé à 'sometimes' pour optionnel
            'score' => 'required|integer|min:1',
        ]);
    
        $quizz->update([
            'idCours' => $request->idCours,
            'titre' => $request->titre,
            'question' => $request->question,
            'reponseCorrecte' => $request->reponseCorrecte,
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
    
        // Modification clé : requête directe sur la table quizz
        $quizz = Quizz::where('idTuteur', $request->tuteurId)
            ->with(['cours' => function($query) {
                $query->select('id', 'titre');
            }])
            ->get();
    
        return response()->json($quizz, 200);
    }
}
