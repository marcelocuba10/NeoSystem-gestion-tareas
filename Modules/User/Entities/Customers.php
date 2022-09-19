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
        'localidad',
        'latitud',
        'longitud',
        'is_vigia',
        'category',
        'potential_products',
        'unit_quantity',
        'result_of_the_visit',
        'objective',
        'next_visit_date',
    ];
    
    protected static function newFactory()
    {
        //return \Modules\User\Database\factories\CustomersFactory::new();
    }
}
