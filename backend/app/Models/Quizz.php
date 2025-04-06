<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quizz extends Model
{
    protected $table = 'quizz'; // 'quizz' au lieu de 'quizzs'

    protected $fillable = [
        'idCours',
        'idTuteur', 
        'titre',
        'question',
        'reponseCorrecte',
        'reponsesFausses',
        'score'
    ];
    protected $casts = [
        'reponsesFausses' => 'array' // Conversion automatique JSON <-> array
    ];

    public function cours()
{
    return $this->belongsTo(Cours::class, 'idCours');
}

// App\Models\Cours.php
public function tuteur()
{
    return $this->belongsTo(Tuteur::class, 'idTuteur');
}

public function quizz()
{
    return $this->hasMany(Quizz::class, 'idCours');
}

}