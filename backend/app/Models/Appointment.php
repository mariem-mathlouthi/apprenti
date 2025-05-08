<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'student_id',
        'tuteur_id',
    ];
    protected $casts = [
        'date' => 'datetime',
    ];
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}
