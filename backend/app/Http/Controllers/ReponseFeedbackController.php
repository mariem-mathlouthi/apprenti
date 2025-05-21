<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\ReponseFeedback;
use Illuminate\Http\Request;

class ReponseFeedbackController extends Controller
{
    public function reponseFeedback(Request $request, $id) 
    {
        $reponse = $request->input('reponse');
        $user_id = $request->input('user_id');
        $user_role = $request->input('user_role');
        $feedback = new ReponseFeedback;
        $feedback->feedback_id = $id;
        $feedback->reponse = $reponse;
        $feedback->user_id = $user_id;
        $feedback->user_role = $user_role;
        $feedback->save();
        return response()->json(['message' => 'Feedback répondu avec succès']);
    }

    public function getReponsesByFeedback($feedbackId)
    {
        $reponses = ReponseFeedback::where('feedback_id', $feedbackId)->get();
        return response()->json(['reponses' => $reponses]);
    }

    public function updateReponse(Request $request, $id)
    {
        $reponse = ReponseFeedback::find($id);

        $request->validate([
                'reponse' => 'required|string',
                'user_role' => 'required|string'
        ]);
        
        if (!$reponse) {
            return response()->json(['message' => 'Response not found'], 404);
        }

        // Check if the authenticated user is the owner of this response
        if ($reponse->user_id !== auth()->user()->id && $reponse->user_role !== $request->user_role) {
            return response()->json(['message' => 'Unauthorized to update this response'], 403);
        }

        $reponse->reponse = $request->input('reponse');
        $reponse->save();

        return response()->json([
            'message' => 'Response updated successfully',
            'reponse' => $reponse
        ]);
    }

    public function deleteReponse(Request $request, $id)
    {
        $reponse = ReponseFeedback::find($id);

        $request->validate([
            'user_role' =>'required|string'
        ]);
        
        if (!$reponse) {
            return response()->json(['message' => 'Response not found'], 404);
        }

        // Check if the authenticated user is the owner of this response
        if ($reponse->user_id !== auth()->user()->id && $reponse->user_role !== $request->user_role) {
            return response()->json(['message' => 'Unauthorized to delete this response'], 403);
        }

        $reponse->delete();

        return response()->json(['message' => 'Response deleted successfully']);
    }
}
