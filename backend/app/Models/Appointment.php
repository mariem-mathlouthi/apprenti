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
        'student_ids',
        'tuteur_id',
        'cours_id'
    ];
    protected $casts = [
        // 'date' => 'datetime',
        'student_ids' => 'array',
    ];
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}
