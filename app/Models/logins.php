<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class logins extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'mysql';
    public $timestamps = false;
    protected $fillable=[
        'login_at','logout_at','user_email','status','login_attempts_count','blocked','blocked_at','requesting_ip','password_submited'
    ];


}
