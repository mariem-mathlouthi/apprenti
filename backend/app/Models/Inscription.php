<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'idCours', 'idApprenant', 'dateInscription'
    ];

    // Relation avec la table `cours`
    public function cours()
    {
        return $this->belongsTo(Cours::class, 'idCours');
    }

    // Relation avec la table `etudiants`
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'idApprenant');
    }
}