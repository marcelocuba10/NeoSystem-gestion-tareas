<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'comment',
    ];
    
    protected static function newFactory()
    {
        //return \Modules\User\Database\factories\CommentsFactory::new();
    }
}
