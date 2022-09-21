<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'purchase_price',
        'sale_price',
        'img_product',
        'quantity',
        'supplier',
        'phone_supplier'
    ];
    
    protected static function newFactory()
    {
        return \Modules\User\Database\factories\ProductsFactory::new();
    }
}
