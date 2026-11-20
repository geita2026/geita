<?php

namespace App\Models\sponsorships;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SponsorSpace extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='booking_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
    'booking_id','space_id','packageId'
    ];
}
