<?php

namespace App\Http\Controllers;

use App\Models\Secteur;
use Illuminate\Http\Request;

class SecteurController extends Controller
{
    /**
     * Récupérer tous les secteurs
     */
    public function getAllSecteurs()
    {
        return response()->json(Secteur::all());
    }

    /**
     * Créer un nouveau secteur
     */
    public function createSecteur(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $secteur = Secteur::create($request->all());

        return response()->json($secteur, 201);
    }

    /**
     * Récupérer un secteur par ID
     */
    public function getSecteurById($id)
    {
        $secteur = Secteur::find($id);

        if (!$secteur) {
            return response()->json([
                'success' => false,
                'message' => 'Secteur non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'secteur' => $secteur
        ], 200);
    }

    /**
     * Mettre à jour un secteur
     */
    public function updateSecteur(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $secteur = Secteur::find($id);

        if (!$secteur) {
            return response()->json([
                'message' => 'Secteur non trouvé'
            ], 404);
        }

        $secteur->update([
            'description' => $request->description
        ]);

        return response()->json([
            'message' => 'Secteur mis à jour avec succès',
            'data' => $secteur
        ], 200);
    }

    /**
     * Supprimer un secteur
     */
    public function deleteSecteur($id)
    {
        $secteur = Secteur::find($id);

        if (!$secteur) {
            return response()->json([
                'message' => 'Secteur non trouvé'
            ], 404);
        }

        $secteur->delete();

        return response()->json([
            'message' => 'Secteur supprimé avec succès'
        ], 200);
    }
}