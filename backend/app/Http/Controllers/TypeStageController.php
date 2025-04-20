<?php

namespace App\Http\Controllers;

use App\Models\TypeStage;
use Illuminate\Http\Request;

class TypeStageController extends Controller
{
    public function getAllTypeStages()
    {
        return response()->json(TypeStage::all());
    }

    public function createTypeStage(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $typeStage = TypeStage::create($request->all());

        return response()->json($typeStage, 201);
    }

    public function getTypeStageById($id)
    {
        $typeStage = TypeStage::find($id);

        if(!$typeStage) {
            return response()->json([
                'success' => false,
                'message' => 'Type de stage non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'type_stage' => $typeStage
        ], 200);
    }

    public function updateTypeStage(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $typeStage = TypeStage::find($id);

        if(!$typeStage) {
            return response()->json([
                'message' => 'Type de stage non trouvé'
            ], 404);
        }

        $typeStage->update([
            'description' => $request->description
        ]);

        return response()->json([
            'message' => 'Type de stage mis à jour avec succès',
            'data' => $typeStage
        ], 200);
    }

    public function deleteTypeStage($id)
    {
        $typeStage = TypeStage::find($id);

        if(!$typeStage) {
            return response()->json([
                'message' => 'Type de stage non trouvé'
            ], 404);
        }

        $typeStage->delete();

        return response()->json([
            'message' => 'Type de stage supprimé avec succès'
        ], 200);
    }
}