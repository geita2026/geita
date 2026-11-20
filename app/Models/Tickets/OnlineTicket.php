<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class OnlineTicket extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='requestId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'requestId','packageId','requestedBy','email','phoneNumber','visitDate',
        'requestNo','requestStatus','quantity','event','plateNumber','ticketNo'
    ];
}
