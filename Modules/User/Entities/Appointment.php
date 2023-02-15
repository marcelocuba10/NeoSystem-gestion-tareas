<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'idReference',
        'customer_id',
        'visit_id',
        'visit_number',
        'date',
        'hour',
        'action',
        'observation',
        'status',
        'datetime'
    ];
    
    protected static function newFactory()
    {
        //return \Modules\User\Database\factories\AppointmentFactory::new();
    }
}
