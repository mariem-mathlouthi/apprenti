<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    use HasFactory;
    protected $fillable = [
        'fullname',
        'niveau_id',
        'cin',
        'email',
        'password',
        'domaine_id',
        'type_stage_id',
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
    public function domaine()
    {
        return $this->belongsTo(Domaine::class);
    }

    public function niveau()
{
    return $this->belongsTo(Niveau::class);
}
    public function typeStage()
    {
        return $this->belongsTo(TypeStage::class);
    }
}
