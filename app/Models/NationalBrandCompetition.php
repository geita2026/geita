<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class NationalBrandCompetition extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='competitionNumber';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'competitionNumber','fullName','phoneNumber','zippedFile','logoDescription',
        'status','emailAddress','totalMarks','averageMarks'
    ];
}
