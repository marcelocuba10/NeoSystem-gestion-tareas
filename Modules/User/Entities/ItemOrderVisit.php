<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class itemOrderVisit extends Model
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
        return \Modules\User\Database\factories\ItemOrderVisitFactory::new();
    }
}
