<?php

namespace App\Http\Controllers;

use App\Events\RealTimeNotification;
use App\Models\Notifications;
use Illuminate\Http\Request;

class NotificationsController extends Controller
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
        $notification = new Notifications();
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
        $notifications = Notifications::where('userId', $request->userId)->get();
        
        return response()->json($notifications, 200);
    }
}
