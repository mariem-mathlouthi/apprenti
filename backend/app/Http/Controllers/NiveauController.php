<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Illuminate\Http\Request;

class NiveauController extends Controller
{
    public function getAllNiveaux()
    {
        return response()->json(Niveau::all());
    }

    public function createNiveau(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $niveau = Niveau::create($request->all());

        return response()->json($niveau, 201);
    }

    public function getNiveauById($id)
    {
        $niveau = Niveau::find($id);
        
        return $niveau 
            ? response()->json(['success' => true, 'niveau' => $niveau], 200)
            : response()->json(['success' => false, 'message' => 'Niveau non trouvé'], 404);
    }

    public function updateNiveau(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $niveau = Niveau::find($id);

        if (!$niveau) {
            return response()->json(['message' => 'Niveau non trouvé'], 404);
        }

        $niveau->update($request->all());

        return response()->json([
            'message' => 'Niveau mis à jour avec succès',
            'niveau' => $niveau
        ], 200);
    }

    public function deleteNiveau($id)
    {
        $niveau = Niveau::find($id);

        if (!$niveau) {
            return response()->json(['message' => 'Niveau non trouvé'], 404);
        }

        $niveau->delete();

        return response()->json(['message' => 'Niveau supprimé avec succès'], 200);
    }
}