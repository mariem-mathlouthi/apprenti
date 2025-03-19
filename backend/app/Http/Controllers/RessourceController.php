<?php

namespace App\Http\Controllers;

use App\Models\Ressource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RessourceController extends Controller
{
    /**
     * Récupérer toutes les ressources
     */
    public function getAllRessources()
    {
        return response()->json(Ressource::all(), 200);
    }

    /**
     * Ajouter une nouvelle ressource
     */
    public function createRessource(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|string',
            // 'file' => 'required|file|mimes:pdf,doc,docx,txt,mp4,mov,avi|max:102400', 
            'idCours' => 'required|exists:cours,id',
        ]);

        $ressource = Ressource::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Ressource ajoutée avec succès',
            'data' => $ressource
        ], 201);
    }

    /**
     * Récupérer une ressource par ID
     */
    public function getRessourceById($id)
    {
        try {
            $ressource = Ressource::findOrFail($id);
            return response()->json($ressource, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ressource non trouvée'], 404);
        }
    }

    /**
     * Mettre à jour une ressource
     */
    public function updateRessource(Request $request, $id)
    {
        try {
            $ressource = Ressource::findOrFail($id);

            $request->validate([
                'titre' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'file' => 'nullable|string',
                'idCours' => 'sometimes|exists:cours,id',
            ]);

            $ressource->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Ressource mise à jour avec succès',
                'data' => $ressource
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ressource non trouvée'], 404);
        }
    }

    public function uploadFile(Request $request)
{
    // Valider le fichier
    $request->validate([
        'file' => 'required|file|mimes:pdf,doc,docx,txt,mp4,mov,avi|max:102400', // 100MB
    ]);

    // Stocker le fichier
    $filePath = $request->file('file')->store('public/files');

    // Retourner le chemin du fichier
    return response()->json([
        'success' => true,
        'filePath' => $filePath,
    ]);
}

    /**
     * Supprimer une ressource
     */
    public function deleteRessource($id)
    {
        try {
            $ressource = Ressource::findOrFail($id);
            $ressource->delete();

            return response()->json(['message' => 'Ressource supprimée avec succès'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ressource non trouvée'], 404);
        }
    }
}
