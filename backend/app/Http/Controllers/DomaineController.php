<?php

namespace App\Http\Controllers;

use App\Models\Domaine;
use Illuminate\Http\Request;

class DomaineController extends Controller
{
    public function getAllDomaines()
    {
        return response()->json(Domaine::all());
    }

    public function createDomaine(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $domaine = Domaine::create($request->all());

        return response()->json($domaine, 201);
    }

    public function getDomaineById($id)
    {
        $domaine = Domaine::find($id);
        
        if (!$domaine) {
            return response()->json([
                'success' => false,
                'message' => 'Domaine non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'domaine' => $domaine
        ], 200);
    }

    public function updateDomaine(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $domaine = Domaine::find($id);

        if (!$domaine) {
            return response()->json([
                'message' => 'Domaine non trouvé'
            ], 404);
        }

        $domaine->update([
            'description' => $request->description
        ]);

        return response()->json([
            'message' => 'Domaine mis à jour avec succès',
            'data' => $domaine
        ], 200);
    }

    public function deleteDomaine($id)
    {
        $domaine = Domaine::find($id);

        if (!$domaine) {
            return response()->json([
                'message' => 'Domaine non trouvé'
            ], 404);
        }

        $domaine->delete();

        return response()->json([
            'message' => 'Domaine supprimé avec succès'
        ], 200);
    }
}