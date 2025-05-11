<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function create( Request $request)
    {
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|string',
            'description' => 'nullable|string',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:etudiants,id',
            'tuteur_id' => 'required|exists:tuteurs,id',
            'cours_id' => 'required|exists:cours,id',

        ]);

        // Create the appointment
        $appointment = new Appointment();
        $appointment->title = $request->input('title');
        $appointment->description = $request->input('description');
        $appointment->date = $request->input('date');
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
