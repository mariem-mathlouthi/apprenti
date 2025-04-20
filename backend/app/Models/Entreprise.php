<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    protected $fillable = [
        'numeroSIRET', 
        'email', 
        'password', 
        'name', 
        'secteur_id', 
        'logo', 
        'description', 
        'link'
    ];

    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }
}
