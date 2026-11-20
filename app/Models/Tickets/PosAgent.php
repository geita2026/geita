<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PosAgent extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='agentId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'agentId','agentName','agentPassword','userName','mobileNumber','asignedPos',
        'asignedLocation','asignedEvent','agentStatus','agentToken','adminstrativeCompany',
        'gateIp','firstLogin','lastLogin'
    ];
}
