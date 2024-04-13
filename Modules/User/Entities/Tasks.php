<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tasks extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'assigned_to',
        'priority',
        'status',
    ];
    
    protected static function newFactory()
    {
        //return \Modules\User\Database\factories\TasksFactory::new();
    }
}
