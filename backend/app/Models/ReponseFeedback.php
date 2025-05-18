<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReponseFeedback extends Model
{
    use HasFactory;

    protected $table = 'reponse_feedback'; // Spécifiez explicitement le nom de la table

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }
}
