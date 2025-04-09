<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Collection extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    


    protected static function booted()
    { 
        static::creating(function ($collection) {
            $collection->id = (string) Str::uuid();  // Génère un UUID lors de la création de l'utilisateur
           
        });
    }

    protected $fillable = [
        'user_id',
        'nom',
    ];
    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function documents() {
         return $this->hasMany(Document::class);
    }

}
