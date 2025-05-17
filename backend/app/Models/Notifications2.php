<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications2 extends Model
{
    use HasFactory;
    protected $fillable = [
        'userId',
        'message',
    ];
    protected $table = 'notifications2';
}
