<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Multimedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'description',
        'type',
        'size'
    ];
    
    protected static function newFactory()
    {
        return \Modules\User\Database\factories\MultimediaFactory::new();
    }
}
