<?php

namespace App\Models\settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class events extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='event_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'event_id','event_name','event_start_date','event_end_date','event_descriptions','event_profile_image','event_location','event_status'
    ];
}
