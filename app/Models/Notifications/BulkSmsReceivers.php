<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BulkSmsReceivers extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='receiver_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'receiver_id','phone_number','customer_group_name','customer_name'
    ];
}
