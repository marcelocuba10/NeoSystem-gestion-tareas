<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

//spatie
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasRoles, HasApiTokens; 

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'address',
        'doc_id',
        'email',
        'password',
        'status',
        'city',
        'estate',
        'img_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    protected static function newFactory()
    {
        return '\Modules\User\Database\factories\UserFactory'::new();
    }
}
