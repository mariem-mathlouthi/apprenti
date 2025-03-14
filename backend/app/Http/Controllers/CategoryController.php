<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller 
{
    /**
     * Récupérer toutes les catégories.
     */
    public function getAllCategories() 
    {
        return response()->json(Category::all());
    }

    /**
     * Ajouter une nouvelle catégorie.
     */
    public function createCategory(Request $request) 
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $category = Category::create($request->all());

        return response()->json($category, 201);
    }

    /**
     * Récupérer une catégorie par son ID.
     */
    public function getCategoryById($id) 
    {
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Catégorie non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'category' => $category
        ], 200);
    }

    /**
     * Mettre à jour une catégorie existante.
     */
    public function updateCategory(Request $request, $id) 
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie non trouvée'
            ], 404);
        }

        $category->update([
            'description' => $request->description
        ]);

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès',
            'data' => $category
        ], 200);
    }

    /**
     * Supprimer une catégorie.
     */
    public function deleteCategory($id) 
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie non trouvée'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ], 200);
    }
}
