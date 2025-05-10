<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Tuteur extends Model
{
    use HasFactory;
    use HasApiTokens;

    protected $fillable = ['fullname', 'email', 'password','specialite_id','experience','phone'];

    
}

