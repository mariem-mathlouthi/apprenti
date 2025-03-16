<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'category_id',
        'prix',
        'idTuteur',
        'idApprenant',
        'duration',
        'file',
        'createdBy'
    ];
    

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tuteur() {
        return $this->belongsTo(Tuteur::class, 'idTuteur');
    }

    public function apprenant() {
        return $this->belongsTo(Etudiant::class, 'idApprenant');
    }
    public function createur() {
        return $this->belongsTo(Tuteur::class, 'createdBy');
    }
    
    
}

