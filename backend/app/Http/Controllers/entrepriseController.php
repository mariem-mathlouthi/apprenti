<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entreprise;

class entrepriseController extends Controller
{
    //
    public function ModifyEntrepriseInfo(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'numeroSIRET' => 'required',
        'name' => 'required',
        'secteur_id' => 'required|exists:secteurs,id',
        'description' => 'sometimes|string',
        'logo' => 'sometimes|image|max:2048',
    ]);

    $entreprise = Entreprise::where('email', $validated['email'])->first();

    if (!$entreprise) {
        return response()->json([
            'message' => 'Entreprise not found',
            'update' => false,
        ], 404);
    }

    // Gestion du logo
    if ($request->hasFile('logo')) {
        $filename = time() . '_' . $request->logo->getClientOriginalName();
        $request->logo->move(public_path('storage/uploads'), $filename);
        $entreprise->logo = asset('storage/uploads/'.$filename);
    }

    // Mise à jour des informations
    $entreprise->update([
        'numeroSIRET' => $validated['numeroSIRET'],
        'name' => $validated['name'],
        'secteur_id' => $validated['secteur_id'],
        'description' => $validated['description'] ?? null,
    ]);

    return response()->json([
        'message' => 'Account updated successfully',
        'update' => true,
        'entreprise' => $entreprise
    ]);
}

    public function getEntreprise(Request $request, $idEntreprise)
    {
        // Fetch the specific offer for the given idEntreprise and id
        $entreprise = Entreprise::where('id', $idEntreprise)->first();
    
        if (!$entreprise) {
            // Return a 404 Not Found response if the offer is not found
            return response()->json([
                'message' => 'Entreprise not found',
                'check' => false,
            ], 404);
        }
    
        // Return the details of the offer
        return response()->json([
            'entreprise' => $entreprise,
            'message' => 'Entreprises details fetched successfully',
            'check' => true,
        ]);
    }
    



    


}
