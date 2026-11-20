<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProducedTicket extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='ticketId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'ticketId','bookingId','ticketNo','ticketStatus','scanCount','lastScanTime','ticketAmount','plateNumber','customerName','visitDate'
        ,'ticketName','backupStatus'
    ];
}
