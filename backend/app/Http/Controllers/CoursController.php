<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CoursController extends Controller
{
    /**
     * Récupérer tous les cours.
     */
    public function getAllCourses()
    {
        $cours = Cours::with(['category', 'tuteur', 'apprenant', 'createur'])->get();
        return response()->json([
            'success' => true,
            'cours' => $cours
        ], 200);
    }

    /**
     * Ajouter un nouveau cours.
     */
    public function createCourse(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'prix' => 'required|numeric',
            'idTuteur' => 'required|exists:tuteurs,id',
            'idApprenant' => 'nullable|exists:etudiants,id',
            'duration' => 'required|integer',
            'file' => 'nullable|string',
            'createdBy' => 'required|exists:tuteurs,id',
        ]);

        try {
            $cours = Cours::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Cours ajouté avec succès',
                'cours' => $cours
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du cours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer un cours par son ID.
     */
    public function getCourseById($id)
    {
        try {
            $cours = Cours::with(['category', 'tuteur', 'apprenant', 'createur'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'cours' => $cours
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cours non trouvé'
            ], 404);
        }
    }

    /**
     * Mettre à jour un cours.
     */
    public function updateCourse(Request $request, $id)
    {
        try {
            $cours = Cours::findOrFail($id);

            $request->validate([
                'titre' => 'sometimes|string|max:255',
                'description' => 'sometimes',
                'category_id' => 'sometimes|exists:categories,id',
                'prix' => 'sometimes|numeric',
                'idTuteur' => 'sometimes|exists:tuteurs,id',
                'idApprenant' => 'nullable|exists:etudiants,id',
                'duration' => 'sometimes|integer',
                'file' => 'nullable|string',
                'createdBy' => 'sometimes|exists:tuteurs,id',
            ]);

            $cours->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cours mis à jour avec succès',
                'cours' => $cours
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cours non trouvé'
            ], 404);
        }
    }

    /**
     * Supprimer un cours.
     */
    public function deleteCourse($id)
    {
        try {
            $cours = Cours::findOrFail($id);
            $cours->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cours supprimé avec succès'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cours non trouvé'
            ], 404);
        }
    }
}
