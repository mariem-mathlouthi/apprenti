<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Entreprise extends Model
{
    use HasFactory;
    use HasApiTokens;
    protected $primaryKey = 'id';

    protected $fillable = [
        'numeroSIRET', 
        'email', 
        'password', 
        'name', 
        'secteur_id', 
        'logo', 
        'description', 
        'link',
        'password_token',
    'password_token_send_at'
    ];

    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }
}
