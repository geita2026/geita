<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CreatedEmails extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='emailId';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'emailId','createdBy','emailTo','copyTo','linkLable','receiverName','regardsName',
        'emailSubject','emailLink','emailContent','emailStatus','scheduledAt'
    ];
}
