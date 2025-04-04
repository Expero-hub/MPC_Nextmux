<?php

namespace App\Models;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->id = (string) Str::uuid();  // Génère un UUID lors de la création de l'utilisateur
        });
    }
    
    protected $fillable = [
        
        'user_id',
        'document_id',
        'montant',
        'statut',
    ];

}
