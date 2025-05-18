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
}
