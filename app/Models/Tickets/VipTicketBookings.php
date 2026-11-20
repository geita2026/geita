<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class VipTicketBookings extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='ticket_booking_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'ticket_booking_id','customer_name','email_address','phone_number','control_number','payment_deadline',
        'amount_paid','ticket_issued','ticket_issued_on','booked_package','ticket_amount','currency','sp_customer_no','gfs_code_mapping',
        'ticket_status','sp_invoice_no','invoice_status','verification_status','scan_count','ticketDescription',
        'TicketTitle','ticketType','visitDate','plateNumber','requestId','bookingEvent','serviceCode','spReference'
    ];
}
