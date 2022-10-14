<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerParameters extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'category_id',
        'potential_product_id',
        'quantity',
    ];
    
    protected static function newFactory()
    {
        return \Modules\User\Database\factories\CustomerParametersFactory::new();
    }
}
