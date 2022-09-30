<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sales extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'sale_date',
        'type',
        'status',
        'total'
    ];
    
    protected static function newFactory()
    {
        return \Modules\User\Database\factories\SalesFactory::new();
    }
}
