<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TicketCollection extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='collectionId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'collectionId','customerId','amountCollected','paymentDate',
        'controlNumber','sp_invoice_no','amountPaid','collectionStatus',
        'differenceAmount','paymentReceipt','amountExpected'
        
    ];
}
