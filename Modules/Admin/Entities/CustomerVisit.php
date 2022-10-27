<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_number',
        'customer_id',
        'seller_id',
        'visit_date',
        'next_visit_date',
        'next_visit_hour',
        'result_of_the_visit',
        'objective',
        'status',
        'action',
        'type'
    ];
    
}
