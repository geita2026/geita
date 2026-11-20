<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExpoParticipant extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='participantId';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'participantId','firstName','middleName','lastName','dateOfBirth',
        'sex','passportNumber','typeOfPassport','emailAddress',
        'phoneNumber','companyName','companyEmail','companyPhoneNumber',
        'companyProfile','typeOfParticipation','programOfInterest',
        'anyOtherDescription','dateOfVisit','passportSizePhoto','travelPassport',
        'companySector','returningDate','workingId','country','participantStatus'
    ];
}
