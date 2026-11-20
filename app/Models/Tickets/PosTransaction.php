<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PosTransaction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='ticketId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'ticketId','ticketAmount','serviceId','printedBy','locationId','posUsed','eventId','updatedBy',
        'ticketNo','ticketStatus','scanStatus','updatedBy','ticketDescription','plateNumber','paymentChannel'
    ];
}
