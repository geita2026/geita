<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $connection = 'mysql';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_uuid', 'phone_number', 'email', 'role', 'profile_image', 'password', 'id', 'user_signature', 'first_name', 'middle_name', 'last_name',
        'account_type', 'account_status', 'password_updated', 'profile_updated', 'myCompany', 'isAcceptedTerms','api_token'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
