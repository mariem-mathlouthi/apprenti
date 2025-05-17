<?php

namespace App\Http\Controllers;

use App\Events\RealTimeNotification;
use App\Models\Appointment;
use App\Models\Tuteur;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function create( Request $request)
    {
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|string',
            'roomId' => 'required|string',
            'description' => 'nullable|string',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:etudiants,id',
            'tuteur_id' => 'required|exists:tuteurs,id',
            'cours_id' => 'required|exists:cours,id',

        ]);

        // Create the appointment
        $appointment = new Appointment();
        $appointment->title = $request->input('title');
        $appointment->date = $request->input('date');
        $appointment->roomId = $request->input('roomId');
        $appointment->description = $request->input('description');
        $appointment->student_ids = $request->input('student_ids');
        $appointment->tuteur_id = $request->input('tuteur_id');
        $appointment->cours_id = $request->input('cours_id');
        $appointment->save();
    
        // Logic to create an appointment
        return response()->json(['message' => 'Appointment created successfully']);
    }

    public function getAllAppointments()
    {
        $appointments = Appointment::all();
        return response()->json($appointments);
    }

    public function getAppointmentById($id)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            return response()->json($appointment);
        } else {
            return response()->json(['message' => 'Appointment not found'], 404);
        }
    }

    public function getAppointmentsByStudentId($studentId)
    {
        $appointments = Appointment::where('student_ids', 'like', '%' . $studentId . '%')->get();
        $formattedAppointments = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => $appointment->title,
                'date' => $appointment->date,
                'description' => $appointment->description,
                'roomId' => $appointment->roomId,
                'tuteur_name' => $appointment->tuteur->fullname,
                'cours_id' => $appointment->cours_id,
            ];
        });
        $appointments = $formattedAppointments;
        // $appointments = Appointment::where($studentId, 'in', 'student_ids')->get();
        return response()->json($appointments);
    }

    public function getAppointmentsByTuteurId($tuteurId)
    {
        $appointments = Appointment::where('tuteur_id', $tuteurId)->get();
        return response()->json($appointments);
    }


    public function deleteAppointment($id)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->delete();
            return response()->json(['message' => 'Appointment deleted successfully']);
        } else {
            return response()->json(['message' => 'Appointment not found'], 404);
        }
    }

    public function updateAppointment(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'title' => 'string|max:255',
            'date' => 'string',
            'description' => 'nullable|string',
            'student_ids' => 'array',
            'student_ids.*' => 'exists:etudiants,id',
            'tuteur_id' => 'required|exists:tuteurs,id',
            'cours_id' => 'required|exists:cours,id',

        ]);

        // Find the appointment by ID
        $appointment = Appointment::find($id);
        if ($appointment) {
            // Update the appointment details
            $appointment->title = $request->input('title');
            $appointment->description = $request->input('description');
            $appointment->date = $request->input('date');
            $appointment->student_ids = $request->input('student_ids');
            $appointment->tuteur_id = $request->input('tuteur_id');
            $appointment->cours_id = $request->input('cours_id');
            $appointment->save();

            return response()->json(['message' => 'Appointment updated successfully']);
        } else {
            return response()->json(['message' => 'Appointment not found'], 404);
        }
    }

}
