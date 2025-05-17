<?php

namespace App\Http\Controllers;

use App\Events\RealTimeNotification;
use App\Models\Entreprise;
use App\Models\Etudiant;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Tuteur;

class notificationController extends Controller
{
    //
    public function notification(Request $request){
        $requestData = $request->all();
        $new = new Notification();
        $new->idEtudiant =$requestData['idEtudiant'];
        $new->idEntreprise = $requestData['idEntreprise'];
        $new->idTuteur = $requestData['idTuteur'];
        $new->message= $requestData['message'];
        $new->destination= $requestData['destination'];
        $new->type= $requestData['type'];
        $new->visibility=$requestData['visibility'];
        $date = $requestData['date'];;
        $formattedDate = date('Y-m-d', strtotime($date));
        $new->date = $formattedDate;
        $new->appointmentId = $requestData['appointmentId'];
        $new->save();

        if ($new->destination == "Etudiant") {
            $userId = $new->idEtudiant;
        } elseif ($new->destination == "Entreprise") {
            $userId = $new->idEntreprise;
        } elseif ($new->destination == "Tuteur") {
            $userId = $new->idTuteur;
        } else {
            return response()->json([
                'message' => 'Invalid destination',
                'check' => false,
            ], 400);
        }

        if ($userId === 0) {
            if ( $new->destination === 'Etudiant') {
                $allStudents = Etudiant::all()->pluck('id');
                foreach ($allStudents as $studentId) {
                    event(new RealTimeNotification($new->message, strtolower($new->destination), $new->type, $new->date, $studentId, $new->appointmentId));
                }
            }else if ($new->destination === 'Entreprise') {
                $allEntreprise = Entreprise::all()->pluck('id');
                foreach ($allEntreprise as $entrepriseId) {
                    event(new RealTimeNotification($new->message, strtolower($new->destination), $new->type, $new->date, $entrepriseId, $new->appointmentId));
                }
            } else if ($new->destination === 'Tuteur') {
                $allTuteurs = Tuteur::all()->pluck('id');
                foreach ($allTuteurs as $tuteurId) {
                    event(new RealTimeNotification($new->message, strtolower($new->destination), $new->type, $new->date, $tuteurId, $new->appointmentId));
                }
            }

        }else {
            event(new RealTimeNotification($new->message, strtolower($new->destination), $new->type, $new->date, $userId, $new->appointmentId));
        }


        return response()->json([
            'message' => 'Notification Added successfully',
            'check' => true,
        ]);
        

    }


    public function getAllNotifications()
    {
        $notifications = Notification::all();
        return response()->json([
            'notifications' => $notifications,
            'message' => 'notifications  fetched successfully',
            'check' => true,
        ]);
    }









}
