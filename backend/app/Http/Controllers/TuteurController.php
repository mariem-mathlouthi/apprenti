<?php

namespace App\Http\Controllers;

use App\Models\Tuteur;
use Illuminate\Http\Request;

class TuteurController extends Controller
{
    public function ModifyTuteurInfo(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email|unique:tuteurs,email,'.$request->id,
        'fullname' => 'required|string|max:255',
        'specialite_id' => 'required|exists:specialites,id',
        'experience' => 'required|integer|min:0',
        'phone' => 'required|string|unique:tuteurs,phone,'.$request->id,
        'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $tuteur = Tuteur::findOrFail($request->id);

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $filename = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('storage/tuteurs'), $filename);
        $tuteur->image = asset('storage/tuteurs/'.$filename);
    }

    $tuteur->update($validated);

    return response()->json([
        'message' => 'Profil mis à jour',
        'update' => true,
        'tuteur' => $tuteur
    ]);
}



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
