<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parameters extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description'
    ];
}
