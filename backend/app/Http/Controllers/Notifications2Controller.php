<?php

namespace App\Http\Controllers;

use App\Events\RealTimeNotification;
use App\Models\Notifications2;
use Illuminate\Http\Request;

class Notifications2Controller extends Controller
{
    public function sendNotification(Request $request)
    {
        // Validate the request
        $request->validate([
            'userId' => 'required|exists:etudiants,id',
            'message' => 'required|string|max:255',
            'appointmentId' => 'sometimes|exists:appointments,id',
        ]);

        // Create a new notification
        $notification = new Notifications2();
        $notification->userId = $request->userId;
        $notification->message = $request->message;
        $notification->save();

        event(new RealTimeNotification($notification->message, $notification->userId, $request->appointmentId));

        return response()->json(['message' => 'Notification sent successfully!'], 200);
    }

    public function getNotificationsByUserId(Request $request)
    {
        $request->validate([
            'userId' => 'required|exists:etudiants,id',
        ]);
        $notifications = Notifications2::where('userId', $request->userId)->get();
        
        return response()->json($notifications, 200);
    }
}
