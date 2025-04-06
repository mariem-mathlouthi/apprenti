<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReponseEtudiant extends Model
{
    use HasFactory;

    protected $fillable = [
        'etudiant_id',
        'quizz_id',
        'reponses',
        'est_correcte',
        'score_obtenu'
    ];

    protected $casts = [
        'reponses' => 'array',
        'est_correcte' => 'boolean'
    ];

    public function etudiant()
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function quizz()
    {
        return $this->belongsTo(Quizz::class);
    }
}