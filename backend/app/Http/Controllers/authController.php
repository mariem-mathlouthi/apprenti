<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\Entreprise;
use App\Models\Etudiant;
use App\Models\Admin;
use App\Models\Tuteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
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
            'niveau_id' => 'required|integer|exists:niveaux,id', 
            'cin' => 'required|string|unique:etudiants,cin',
            'email' => 'required|email|unique:etudiants,email',
            'password' => 'required|min:6',
            'domaine_id' => 'required|integer|exists:domaines,id',
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
                'niveau_id' => $requestData['niveau_id'],
                'cin' => $requestData['cin'],
                'email' => $requestData['email'],
                'password' => Hash::make($requestData['password']),
                'domaine_id' => $requestData['domaine_id'],
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

    public function signUpEntreprise(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'numeroSIRET' => 'required|string|unique:entreprises,numeroSIRET',
            'email' => 'required|email|unique:entreprises,email',
            'password' => 'required|min:6',
            'name' => 'required|string|max:255',
            'secteur_id' => 'required|integer|exists:secteurs,id', 
            'logo' => 'sometimes|nullable|string',
            'description' => 'required|string',
            'link' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'check' => false
            ], 422);
        }

        try {
            $newUser = Entreprise::create([
                'numeroSIRET' => $requestData['numeroSIRET'],
                'email' => $requestData['email'],
                'password' => Hash::make($requestData['password']),
                'name' => $requestData['name'],
                'secteur_id' => $requestData['secteur_id'], 
                'logo' => $requestData['logo'] ?? null,
                'description' => $requestData['description'],
                'link' => $requestData['link']
            ]);

            return response()->json([
                'message' => 'Account created successfully',
                'check' => true,
                'entreprise' => $newUser
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error: ' . $e->getMessage(),
                'check' => false
            ], 500);
        }
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
            $token = $student->createToken('auth_token')->plainTextToken;
            // If the user exists and the password matches, return success
            return response()->json([
                'message' => 'Student login successful',
                'user' => $student,
                'role' => 'student',
                'token' => $token,
                'check' => true,
            ]);
        }
        // Check if the user exists as an entreprise
        $entreprise = Entreprise::where('email', $email)->first();
        if ($entreprise && Hash::check($password, $entreprise->password)) {
            $token = $entreprise->createToken('auth_token')->plainTextToken;
            // If the user exists and the password matches, return success
            return response()->json([
                'message' => 'Entreprise login successful',
                'user' => $entreprise,
                'role' => 'entreprise',
                'token' => $token,
                'check' => true,
            ]);
        }
        
        $admin = Admin::where('email', $email)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            $token = $admin->createToken('auth_token')->plainTextToken;
            // If the user exists and the password matches, return success
            return response()->json([
                'message' => 'Admin login successful',
                'admin' => $admin,
                'role' => 'admin',
                'token' => $token,
                'check' => true,
            ]);
        }

        $tuteur = Tuteur::where('email', $email)->first();
        if ($tuteur && Hash::check($password, $tuteur->password)) {
            if ($tuteur->status === 'en attente') {
                return response()->json([
                   'message' => 'Votre compte est en attente de validation',
                    'check' => false,
                ]);
            }
            $token = $tuteur->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Tuteur login successful',
                'user' => $tuteur,
                'role' => 'tuteur',
                'token' => $token,
                'check' => true,
            ]);
        }
        // If user doesn't exist or password doesn't match for either student or entreprise
        return response()->json([
            'message' => 'Invalid email or password',
            'check' => false,
        ]);
    }

    public function forgot_password(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'check' => false
                ], 422);
            }

            $user = null;
            $models = [Etudiant::class, Entreprise::class, Tuteur::class, Admin::class];
            
            foreach ($models as $model) {
                $user = $model::where('email', $request->email)->first();
                if ($user) break;
            }

            if (!$user) {
                return response()->json([
                    'message' => 'Aucun compte trouvé avec cet email',
                    'check' => false
                ], 404);
            }

            $token = mt_rand(100000, 999999);
            $user->update([
                'password_token' => $token,
                'password_token_send_at' => now()
            ]);

            Mail::to($request->email)->send(new ResetPassword($token, $request->email));

            return response()->json([
                'message' => 'Code de réinitialisation envoyé',
                'check' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'envoi du code',
                'check' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changer_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'check' => false
            ], 422);
        }

        try {
            $user = null;
            $models = [Etudiant::class, Entreprise::class, Tuteur::class, Admin::class];
            
            foreach ($models as $model) {
                $user = $model::where('email', $request->email)->first();
                if ($user) break;
            }

            if (!$user) {
                return response()->json([
                    'message' => 'Aucun compte trouvé avec cet email',
                    'check' => false
                ], 404);
            }

            if ($user->password_token !== $request->code) {
                return response()->json([
                    'message' => 'Code de vérification invalide',
                    'check' => false
                ], 400);
            }

            if (now()->diffInMinutes($user->password_token_send_at) > 30) {
                return response()->json([
                    'message' => 'Code expiré (30 minutes max)',
                    'check' => false
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'password_token' => null,
                'password_token_send_at' => null
            ]);

            return response()->json([
                'message' => 'Mot de passe mis à jour avec succès',
                'check' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur : ' . $e->getMessage(),
                'check' => false
            ], 500);
        }
    }


}