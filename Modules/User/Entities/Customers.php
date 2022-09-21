<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customers extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'last_name',
        'idReference',
        'phone',
        'doc_id',
        'email',
        'address',
        'city',
        'estate',
        'latitude',
        'longitude',
        'is_vigia',
        'category',
        'potential_products',
        'unit_quantity',
        'result_of_the_visit',
        'objective',
        'next_visit_date',
        'next_visit_hour',
    ];

    protected $casts = [
        'category' => 'array',
        'potential_products' => 'array',
    ];
    
    protected static function newFactory()
    {
        //return \Modules\User\Database\factories\CustomersFactory::new();
    }
}
