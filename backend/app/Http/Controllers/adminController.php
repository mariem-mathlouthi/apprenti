<?php

namespace App\Http\Controllers;

use App\Mail\DemandeTuteurMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Etudiant;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\Tuteur;
use Illuminate\Support\Facades\Mail;

class adminController extends Controller
{
    public function getPendingTuteurs()
    {
        $pendingTuteurs = Tuteur::where('status', 'en attente')->get();
        
        if ($pendingTuteurs->isEmpty()) {
            return response()->json([
                'message' => 'No pending tuteur accounts found',
                'tuteurs' => [],
            ], 404);
        }
        
        return response()->json([
            'tuteurs' => $pendingTuteurs,
            'message' => 'Pending tuteurs retrieved successfully',
        ]);
    }

    public function updateTuteurStatus(Request $request, $id)
    {
        $tuteur = Tuteur::findOrFail($id);
        $request->validate([
           'status' =>'required|string|in:accepté,refusé' // Corriger les valeurs autorisées
        ]);
        $newStatus = $request->status;

        if ($newStatus === 'accepté') {
            $tuteur->status = $newStatus;
            $tuteur->save();
            Mail::to($tuteur->email)->send(new DemandeTuteurMail($tuteur, true));
        } elseif ($newStatus === 'refusé') {
            Mail::to($tuteur->email)->send(new DemandeTuteurMail($tuteur, false));
            $tuteur->delete();
        }

        return response()->json([
            'message' => 'Tuteur status updated successfully',
            'tuteur' => $tuteur
        ]);
    }

    //
    public function signUpAdmin(Request $request){
        $requestData = $request->all();
        // Check if email already exists
        $existingAdmin = Admin::where('email', $requestData['email'])->first();
        if ($existingAdmin) {
            return response()->json([
                'message' => 'Email already exists',
                'check' => false,
            ]);
        }
       
        // Create a new admin
        $newAdmin = new Admin();
        $newAdmin->email = $requestData['email'];
        $newAdmin->password = Hash::make($requestData['password']);
        $newAdmin->save();

        return response()->json([
            'message' => 'Account created successfully',
            'check' => true,
        ]);
    }

    public function states(){
        $newOrders = Offre::count();
        $companies = Entreprise::count();
        $students  = Etudiant::count();    
    
        return response()->json([
            'newOrders' => $newOrders,
            'companies' => $companies,
            'students' => $students
        ]);
    }
    public function getAllOffresAdmin(){
        // Fetch all offers
        $offres = Offre::all();
        return response()->json([
            'offres' => $offres,
            'message' => 'All offres fetched successfully',
            'check' => true,
        ]);
    }

    public function updateOfferStatus(Request $request, $id) {  
        $offer = Offre::findOrFail($id);
        $newStatus = $request->input('status');
    
        // Vérifiez si le statut fourni est valide
        if ($newStatus !== 'en attente' && $newStatus !== 'accepté' && $newStatus !== 'refusé') {
            return response()->json([
                'error' => 'Invalid status provided'
            ], 400);
        }
    
        // Mettez à jour le statut de l'offre
        $offer->status = $newStatus;
        $offer->save();
    
        return response()->json([
            'message' => 'Offer status updated successfully',
            'offer' => $offer
        ]);
    }
    public function getAllStudents()
    {
        // Récupérer tous les étudiants
        $students = Etudiant::all();
    
        // Vérifier si des étudiants sont trouvés
        if ($students->isEmpty()) {
            // Retourner une réponse 404 Not Found si aucun étudiant n'est trouvé
            return response()->json([
                'message' => 'Aucun étudiant trouvé',
                'students' => [],
            ], 404);
        }
    
        // Retourner la liste des étudiants
        return response()->json([
            'students' => $students,
            'message' => 'Étudiants récupérés avec succès',
        ]);
    }
    public function deleteStudent($id)
    {
        // Recherche de l'étudiant à supprimer
        $student = Etudiant::find($id);

        // Vérifie si l'étudiant existe
        if (!$student) {
            return response()->json([
                'message' => 'Étudiant non trouvé',
            ], 404);
        }

        // Suppression de l'étudiant
        $student->delete();

        return response()->json([
            'message' => 'Étudiant supprimé avec succès',
        ]);
    }
    public function getAllEnterprises()
    {
        // Fetch all enterprises
        $enterprises = Entreprise::all();
    
        // Check if any enterprises are found
        if ($enterprises->isEmpty()) {
            // Return a 404 Not Found response if no enterprises are found
            return response()->json([
                'message' => 'No enterprises found',
                'enterprises' => [],
            ], 404);
        }
    
        // Return the list of enterprises
        return response()->json([
            'enterprises' => $enterprises,
            'message' => 'Enterprises fetched successfully',
        ]);
    }
    public function deleteEntreprise($id)
    {
        // Rechercher l'entreprise à supprimer
        $entreprise = Entreprise::find($id);

        // Vérifier si l'entreprise existe
        if (!$entreprise) {
            // Retourner une réponse 404 Not Found si l'entreprise n'est pas trouvée
            return response()->json([
                'message' => 'Entreprise not found',
            ], response()::HTTP_NOT_FOUND);
        }

        // Supprimer l'entreprise
        $entreprise->delete();

        // Retourner une réponse de succès
        return response()->json([
            'message' => 'Entreprise deleted successfully',
        ]);
    }

    public function addTuteur(Request $request)
{
    $request->validate([
        'fullname' => 'required|string|max:255',
        'email' => 'required|email|unique:tuteurs,email',
        'password' => 'required|string|min:6',
        'specialite' => 'required|string|max:255',
        'experience' => 'required|integer|min:0',
        'phone' => 'required|string|unique:tuteurs,phone',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Gestion de l'image (optionnelle)
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('tuteurs', 'public');
    }

    // Création du tuteur
    $tuteur = new Tuteur();
    $tuteur->fullname = $request->fullname;
    $tuteur->email = $request->email;
    $tuteur->password = Hash::make($request->password);
    $tuteur->specialite = $request->specialite;
    $tuteur->experience = $request->experience;
    $tuteur->phone = $request->phone;
    $tuteur->image = $imagePath;
    $tuteur->save();

    return response()->json([
        'message' => 'Tuteur ajouté avec succès',
        'tuteur' => $tuteur
    ], 201);
}



public function getAllTuteurs()
{
    // Récupérer tous les tuteurs
    $tuteurs = Tuteur::all();

    // Vérifier si des tuteurs existent
    if ($tuteurs->isEmpty()) {
        return response()->json([
            'message' => 'Aucun tuteur trouvé',
            'tuteurs' => [],
        ], 404);
    }

    return response()->json([
        'tuteurs' => $tuteurs,
        'message' => 'Tuteurs récupérés avec succès',
    ]);
}


public function deleteTuteur($id)
{
    $tuteur = Tuteur::find($id);

    if (!$tuteur) {
        return response()->json([
            'message' => 'Tuteur non trouvé'
        ], 404);
    }

    $tuteur->delete();

    return response()->json([
        'message' => 'Tuteur supprimé avec succès'
    ]);
}






    






}
