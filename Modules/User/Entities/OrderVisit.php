<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'product_id',
        'price',
        'quantity',
        'amount'
    ];
    
    protected static function newFactory()
    {

    }
}
