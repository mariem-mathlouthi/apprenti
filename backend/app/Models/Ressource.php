<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ressource extends Model
{
    use HasFactory;
    protected $fillable = ['titre', 'description', 'file', 'idCours'];

   
    public function cours()
    {
        return $this->belongsTo(Cours::class, 'idCours');
    }
}
