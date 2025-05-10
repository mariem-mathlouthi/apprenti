<?php

namespace App\Http\Controllers;

use App\Models\CoursSubscriptions;
use Illuminate\Http\Request;

class CoursSubscriptionsController extends Controller
{
    public function subscribeToCourse(Request $request)
    {
        $request->validate([
            'cours_id' => 'required|exists:cours,id',
            'etudiant_id' => 'required|exists:etudiants,id',
            'tuteur_id' => 'required|exists:tuteurs,id',
        ]);

        $subscription = CoursSubscriptions::create([
            'cours_id' => $request->cours_id,
            'etudiant_id' => $request->etudiant_id,
            'tuteur_id' => $request->tuteur_id,
        ]);

        return response()->json([
            'message' => 'Subscription successful',
            'subscription' => $subscription
        ]);
    }
}
