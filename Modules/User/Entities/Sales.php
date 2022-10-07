<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sales extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'visit_id',
        'seller_id',
        'customer_id',
        'sale_date',
        'order_date',
        'type',
        'status',
        'total'
    ];
    
    protected static function newFactory()
    {
        return \Modules\User\Database\factories\SalesFactory::new();
    }
}
