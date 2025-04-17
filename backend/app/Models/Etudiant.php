<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    use HasFactory;
    protected $fillable = [
        'fullname',
        'niveau',
        'cin',
        'email',
        'password',
        'domaine',
        'typeStage',
        'specialite_id',
        'etablissement',
        'image'
    ];
    
    public function specialite()
    {
        return $this->belongsTo(Specialite::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
