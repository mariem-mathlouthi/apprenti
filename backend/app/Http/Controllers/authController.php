<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\Entreprise;
use App\Models\Etudiant;
use App\Models\Admin;
use App\Models\Tuteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class authController extends Controller
{
    //

    public function signUpEtudiant(Request $request)
    {
        $requestData = $request->all();
    
        // Validation renforcée
        $validator = Validator::make($requestData, [
            'fullname' => 'required|string|max:255',
            'niveau' => 'required|string|max:50',
            'cin' => 'required|string|unique:etudiants,cin',
            'email' => 'required|email|unique:etudiants,email',
            'password' => 'required|min:6',
            'domaine_id' => 'required|integer|exists:domaines,id',
            'typeStage' => 'required|string|max:100',
            'specialite_id' => 'required|integer|exists:specialites,id',
            'etablissement' => 'required|string|max:255',
            'image' => 'sometimes|nullable|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'check' => false
            ], 422);
        }
    
        try {
            $newUser = Etudiant::create([
                'fullname' => $requestData['fullname'],
                'niveau' => $requestData['niveau'],
                'cin' => $requestData['cin'],
                'email' => $requestData['email'],
                'password' => Hash::make($requestData['password']),
                'domaine_id' => $requestData['domaine_id'],
                'typeStage' => $requestData['typeStage'],
                'specialite_id' => $requestData['specialite_id'],
                'etablissement' => $requestData['etablissement'],
                'image' => $requestData['image'] ?? null
            ]);
    
            return response()->json([
                'message' => 'Compte créé avec succès',
                'check' => true,
                'etudiant' => $newUser
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur serveur : ' . $e->getMessage(),
                'check' => false
            ], 500);
        }
    }




    public function signUpEntreprise(Request $request){
        $requestData = $request->all();
        // Check if email already exist
        $existingUser = Entreprise::where('email', $requestData['email'])->first();
        if ($existingUser) {
            return response()->json([
                'message' => 'Email already exists',
                'check' => false,
            ]);
        }
        // Create a new user
        $newUser = new Entreprise();
        $newUser->numeroSIRET = $requestData['numeroSIRET'];
        $newUser->email = $requestData['email'];
        $newUser->password = Hash::make($requestData['password']);
        $newUser->name = $requestData['name'];
        $newUser->secteur = $requestData['secteur'];
        $newUser->logo = $requestData['logo'];
        $newUser->description = $requestData['description'];
        $newUser->link = $requestData['link'];
        $newUser->save();

        return response()->json([
            'message' => 'Account created successfully',
            'check' => true,
        ]);
    }

    public function signUpTuteur(Request $request)
{
    $requestData = $request->all();

    // Validation avec règles Laravel
    $validator = Validator::make($requestData, [
        'fullname' => 'required|string|max:255',
        'email' => 'required|email|unique:tuteurs,email',
        'password' => 'required|min:6',
        'specialite_id' => 'required|integer|exists:specialites,id',
        'experience' => 'required|integer|min:0',
        'phone' => 'required|unique:tuteurs,phone',
        'image' => 'sometimes|nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => $validator->errors()->first(),
            'check' => false
        ], 422);
    }

    try {
        $newTuteur = Tuteur::create([
            'fullname' => $requestData['fullname'],
            'email' => $requestData['email'],
            'password' => Hash::make($requestData['password']),
            'specialite_id' => $requestData['specialite_id'],
            'experience' => $requestData['experience'],
            'phone' => $requestData['phone'],
            'image' => $requestData['image'] ?? null,
            'status' => 'en attente' // Valeur par défaut
        ]);

        return response()->json([
            'message' => 'Compte créé avec succès',
            'check' => true,
            'tuteur' => $newTuteur
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur de création : ' . $e->getMessage(),
            'check' => false
        ], 500);
    }
}


    public function LoginUser(Request $request)
    {
        $requestData = $request->all();
        $email = $requestData['email'];
        $password = $requestData['password'];

        // Check if the user exists as a student
        $student = Etudiant::where('email', $email)->first();
        if ($student && Hash::check($password, $student->password)) {
            // If the user exists and the password matches, return success
            return response()->json([
                'message' => 'Student login successful',
                'user' => $student,
                'role' => 'student',
                'check' => true,
            ]);
        }
        // Check if the user exists as an entreprise
        $entreprise = Entreprise::where('email', $email)->first();
        if ($entreprise && Hash::check($password, $entreprise->password)) {
            // If the user exists and the password matches, return success
            return response()->json([
                'message' => 'Entreprise login successful',
                'user' => $entreprise,
                'role' => 'entreprise',
                'check' => true,
            ]);
        }
        $admin = Admin::where('email', $email)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            // If the user exists and the password matches, return success
            return response()->json([
                'message' => 'Admin login successful',
                'admin' => $admin,
                'role' => 'admin',
                'check' => true,
            ]);
        }

        $tuteur = Tuteur::where('email', $email)->first();
        if ($tuteur && Hash::check($password, $tuteur->password)) {
            return response()->json([
                'message' => 'Tuteur login successful',
                'user' => $tuteur,
                'role' => 'tuteur',
                'check' => true,
            ]);
        }
        // If user doesn't exist or password doesn't match for either student or entreprise
        return response()->json([
            'message' => 'Invalid email or password',
            'check' => false,
        ]);
    }



}