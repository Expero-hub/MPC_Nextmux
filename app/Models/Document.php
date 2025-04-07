<?php

namespace App\Models;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->id = (string) Str::uuid();  // Génère un UUID lors de la création de l'utilisateur
            logger($user);
        });
        
    }

    protected $fillable = [
     
        'user_id',
        'collection_id',
        'nom',
        'photo',
        'etat',
        'authentification',
    ];

}
