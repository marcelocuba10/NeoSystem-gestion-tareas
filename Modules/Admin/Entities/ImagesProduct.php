<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImagesProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'filename'
    ];
    
    protected static function newFactory()
    {
        
    }
}
