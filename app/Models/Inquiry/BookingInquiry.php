<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BookingInquiry extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    public $timestamps=false;
    protected $primaryKey='enquiryId';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'enquiryId','enquiryNo','companyName','companyEmail','companyPhone','eventId',
        'products','spaceCategory','country','inquiryStatus','experienceStatus',
        'equiryFeedBack','registrationCertificate','companyProfile','moreDescription'
    ];
}
