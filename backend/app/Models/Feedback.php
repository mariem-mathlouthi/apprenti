<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks'; // Spécifiez explicitement le nom de la table

    protected $fillable = [
        'etudiant_id',
        'cours_id',
        'note',
        'commentaire'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class);
    }

    public function reponses()
    {
        return $this->hasMany(ReponseFeedback::class);
    }
}