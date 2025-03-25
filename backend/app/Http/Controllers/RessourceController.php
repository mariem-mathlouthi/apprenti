<?php

namespace App\Http\Controllers;

use App\Models\Ressource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class RessourceController extends Controller
{
    /**
     * Récupérer toutes les ressources
     */
    public function getAllRessources()
{
    $ressources = Ressource::all()->map(function($item) {
        if ($item->file) {
            // Ensure the path is correct and prepend the base URL
            $cleanPath = ltrim($item->file, '/'); // Remove leading slash if present
            $item->file = url($cleanPath); // This will prepend http://localhost:8000/
        }
        return $item;
    });
    
    return response()->json($ressources, 200);
}

public function getRessourcesByCours($idCours)
{
    $ressources = Ressource::where('idCours', $idCours)->get()->map(function($item) {
        if ($item->file) {
           
            $cleanPath = ltrim($item->file, '/'); 
            $item->file = url($cleanPath); 
        }
        return $item;
    });

    return response()->json($ressources);
}
    /**
     * Ajouter une nouvelle ressource
     */
    public function createRessource(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|string', // Chemin du fichier après upload
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

    /**
     * Uploader un fichier
     */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt,mp4,mov,avi|max:102400', // 100 MB max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Nom unique pour le fichier
            $filePath = $file->storeAs('uploads', $fileName, 'public'); // Stocker dans le dossier public/uploads

            return response()->json([
                'success' => true,
                'filePath' => '/storage/' . $filePath, // Chemin d'accès public
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Aucun fichier trouvé.'], 400);
    }
}