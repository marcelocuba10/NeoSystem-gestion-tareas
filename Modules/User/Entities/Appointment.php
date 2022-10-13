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
        'date',
        'hour',
        'action',
        'observation',
    ];
    
    protected static function newFactory()
    {
        return \Modules\User\Database\factories\AppointmentFactory::new();
    }
}
