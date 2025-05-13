<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursSubscriptions extends Model
{
    use HasFactory;
    protected $fillable = [
        'cours_id',
        'etudiant_id',
        'tuteur_id'
    ];
    protected $table = 'cours_subscriptions';

        public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'cours_id');
    }

    public function tuteur()
    {
        return $this->belongsTo(Tuteur::class, 'tuteur_id');
    }
}
