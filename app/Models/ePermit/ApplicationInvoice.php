<?php

namespace App\Models\ePermit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ApplicationInvoice extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='invoiceNo';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'invoiceNo','invoiceAmount','vatAmount','spInvoiceNo',
        'paymentMode','invoiceStatus','invoiceDescription',
        'customerId','createdBy','expireDate','newExpireDate','amountPaid',
        'controlNumber','currency','referenceApplication'
    ];
}
