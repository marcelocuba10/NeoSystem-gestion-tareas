<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'idReference',
        'name',
        'description',
        'purchase_price',
        'sale_price',
        'img_product',
        'inventory',
        'supplier',
        'phone_supplier',
        'brand',
        'model',
        'type'
    ];
    
    protected static function newFactory()
    {
        return \Modules\User\Database\factories\ProductsFactory::new();
    }
}
