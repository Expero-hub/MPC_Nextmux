<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Offre extends Model
{
    protected static function booted()
    {
        static::creating(function ($document) {
            $document->id = (string) Str::uuid();  // Génère un UUID lors de la création de l'utilisateur
        });
    }


    protected $fillable = [
       
        'poste',
        'description',
        
    ];

}
