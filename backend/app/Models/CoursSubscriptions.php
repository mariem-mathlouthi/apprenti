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
        return $this->belongsToMany(Etudiant::class, 'cours_subscriptions', 'etudiant_id', 'id');
    }
}
