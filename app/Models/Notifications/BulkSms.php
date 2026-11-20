<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BulkSms extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='sms_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'sms_id','sms_body','sms_status','sms_sent_on','phone_number','customer_name'
    ];
}
