<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    use HasFactory;

    public function demandes() 
    {
        return $this->hasMany(Demande::class);
    }
    public function domaine() 
    {
        return $this->belongsTo(Domaine::class);
    }
    
}
