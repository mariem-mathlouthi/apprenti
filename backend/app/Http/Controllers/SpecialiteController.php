<?php

namespace App\Http\Controllers;

use App\Models\Specialite;
use Illuminate\Http\Request;

class SpecialiteController extends Controller
{
    public function getAllSpecialites()
    {
        return response()->json(Specialite::all());
    }

    
    public function createSpecialite(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $specialite = Specialite::create($request->all());

        return response()->json($specialite, 201);
    }

    
    public function getSpecialiteById($id)
    {
        $specialite = Specialite::find($id);
    
        if (!$specialite) {
            return response()->json([
                'success' => false,
                'message' => 'Spécialité non trouvée'
            ], 404);
        }
    
        // Structure de réponse à adapter dans le frontend
        return response()->json([
            'success' => true,
            'specialite' => $specialite
        ], 200);
    }

    
    public function updateSpecialite(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $specialite = Specialite::find($id);

        if (!$specialite) {
            return response()->json([
                'message' => 'Spécialité non trouvée'
            ], 404);
        }

        $specialite->update($request->all());

        return response()->json([
            'message' => 'Spécialité mise à jour',
            'data' => $specialite
        ], 200);
    }

    
    public function deleteSpecialite($id)
    {
        $specialite = Specialite::find($id);

        if (!$specialite) {
            return response()->json([
                'message' => 'Spécialité non trouvée'
            ], 404);
        }

        $specialite->delete();

        return response()->json([
            'message' => 'Spécialité supprimée'
        ], 200);
    }
}
