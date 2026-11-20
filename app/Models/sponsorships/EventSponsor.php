<?php

namespace App\Models\sponsorships;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EventSponsor extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='sponsor_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
    'sponsor_id','sponsor_name','sponsor_logo','sponsor_website','sponsorship_category'
    ];
}
