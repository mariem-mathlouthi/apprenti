<?php

namespace App\Http\Controllers;

use App\Models\Tuteur;
use Illuminate\Http\Request;

class TuteurController extends Controller
{


    public function getTuteurDetail(Request $request, $id)
{
    // Récupérer les détails du tuteur par ID
    $tuteur = Tuteur::where('id', $id)->first();

    // Vérifier si le tuteur existe
    if (!$tuteur) {
        return response()->json([
            'message' => 'Tuteur non trouvé',
            'check' => false,
        ], 404);
    }

    // Retourner les détails du tuteur
    return response()->json([
        'tuteur' => $tuteur,
        'message' => 'Détails du tuteur récupérés avec succès',
        'check' => true,
    ]);
}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tuteur $tuteur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tuteur $tuteur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tuteur $tuteur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tuteur $tuteur)
    {
        //
    }
}
