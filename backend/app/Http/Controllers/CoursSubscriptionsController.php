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

    public function getIsStudentSubscribedToCourse(Request $request, $coursId) {
        $request->validate([
            'etudiant_id' =>'required|exists:etudiants,id',
            'tuteur_id' => 'required|exists:tuteurs,id'
        ]);

        $subscription = CoursSubscriptions::where('cours_id', $coursId)
            ->where('etudiant_id', $request->etudiant_id)
            ->where('tuteur_id', $request->tuteur_id)
            ->first();
        if ($subscription) {
            return response()->json([
                'message' => 'Student is subscribed to the course',
                'isSubscribed' => true,
            ]);
        }
    }

    public function getAllStudentsSubscriptedCours(Request $request)
    {
        $request->validate([
            'tuteur_id' => 'required|exists:tuteurs,id',
            'cours_id' => 'required|exists:cours,id',
        ]);
        // Fetch all students who are subscribed to courses
        // $students = CoursSubscriptions::with('etudiant')->get();
        $students = CoursSubscriptions::where('tuteur_id', $request->tuteur_id)
            ->where('cours_id', $request->cours_id)
            ->with(['etudiant' => function ($query) {
                $query->select('etudiants.id', 'fullname');
            }])
            ->get()
            ->pluck('etudiant')
            ->unique('id')
            ->values();
        return response()->json([
            'students' => $students,
            'message' => 'Students fetched successfully',
            'check' => true,
        ]);
    }

    public function getAllTutorsSubscripted(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
        ]);

        $tutors = CoursSubscriptions::where('etudiant_id', $request->etudiant_id)
            ->with(['tuteur' => function ($query) {
                $query->select('tuteurs.id', 'fullname');
            }])
            ->get()
            ->pluck('tuteur')
            ->unique('id')
            ->values();

        return response()->json([
            'tutors' => $tutors,
            'message' => 'Tutors fetched successfully',
            'check' => true,
        ]);
    }

    public function getAllStudentsSubscripted(Request $request) {
        $request->validate([
            'tuteur_id' =>'required|exists:tuteurs,id',
        ]);
        $students = CoursSubscriptions::where('tuteur_id', $request->tuteur_id)
            ->with(['etudiant' => function ($query) {
                $query->select('etudiants.id', 'fullname');
            }])
            ->get()
            ->pluck('etudiant')
            ->unique('id')
            ->values();
        return response()->json([
            'students' => $students,
           'message' => 'Students fetched successfully',
            'check' => true,
        ]);

    }
}
