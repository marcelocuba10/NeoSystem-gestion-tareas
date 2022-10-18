<?php

namespace Modules\Admin\Entities;

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
        return \Modules\Admin\Database\factories\MultimediaFactory::new();
    }
}
