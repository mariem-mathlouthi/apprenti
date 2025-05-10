<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\CoursSubscriptions;
use Illuminate\Http\Request;
use App\Models\Etudiant;

class studentController extends Controller
{
    //
    public function ModifyEtudiantInfo(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'fullname' => 'required|string|max:255',
        'niveau_id' => 'required|exists:niveaux,id',
        'domaine_id' => 'required|exists:domaines,id',
        'specialite_id' => 'required|exists:specialites,id',
        'etablissement' => 'required|string|max:255',
        'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $etudiant = Etudiant::where('email', $validated['email'])->first();

    if (!$etudiant) {
        return response()->json([
            'message' => 'Student not found',
            'update' => false,
        ], 404);
    }

    // Gestion de l'image
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $filename = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('storage/etudiants'), $filename);
        $etudiant->image = asset('storage/etudiants/'.$filename);
    }

    // Mise à jour des informations
    $etudiant->update([
        'fullname' => $validated['fullname'],
        'niveau_id' => $validated['niveau_id'],
        'domaine_id' => $validated['domaine_id'],
        'specialite_id' => $validated['specialite_id'],
        'etablissement' => $validated['etablissement'],
    ]);

    return response()->json([
        'message' => 'Account updated successfully',
        'update' => true,
        'etudiant' => $etudiant
    ]);
}


    public function getStudentDetail($id)
    {
        // Fetch the specific offer for the given idEntreprise and id
        $student = Etudiant::where('id', $id)->first();
    
        if (!$student) {
            // Return a 404 Not Found response if the offer is not found
            return response()->json([
                'message' => 'Student not found',
                'check' => false,
            ], 404);
        }
    
        // Return the details of the offer
        return response()->json([
            'student' => $student,
            'message' => 'Student detail fetched successfully',
            'check' => true,
        ]);
    }
    
    public function getAllStudentsSubscripted(Request $request)
    {
        $validated = request()->validate([
            'cours_id' => 'required|exists:cours,id',
        ]);
        // Fetch all students
        $students = CoursSubscriptions::where('cours_id', $validated['cours_id'])->with('etudiant')->get();
            // ->pluck('etudiant');
        // $students = 

        if ($students->isEmpty()) {
            // Return a 404 Not Found response if no students are found
            return response()->json([
                'message' => 'No students found',
                'check' => false,
            ], 404);
        }
    
        // Return the list of students
        return response()->json([
            'students' => $students,
            'message' => 'Students fetched successfully',
            'check' => true,
        ]);
    }

}
