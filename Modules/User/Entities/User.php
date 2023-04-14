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

    use HasFactory, Notifiable, HasRoles, HasApiTokens;  //importante adicionar HasRoles

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'last_name',
        'idReference',
        'phone_1',
        'phone_2',
        'address',
        'doc_id',
        'email',
        'password',
        'main_user',
        'status',
        'seller_contact_1',
        'seller_contact_2',
        'city',
        'estate',
        'latitude',
        'longitude',
        'img_profile',
        'meta_visits',
        'meta_billing',
        'count_meta_visits',
        'count_meta_billing'
    ];

    public function reports()
    {
        return $this->hasMany(Reports::class);
    }

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
