<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    //

    protected $fillable = [
        'title',
        'content',
        'image_path',
        'video_url',
        'start_date',
        'reminder_date',
        'end_date',
    ];
  
}
