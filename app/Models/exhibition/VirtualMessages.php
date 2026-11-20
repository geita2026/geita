<?php

namespace App\Models\exhibition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class VirtualMessages extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='message_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'message_id','message','message_from','message_to','message_status','message_sent_on','message_read_on','message_file','chart_session','reference_email'
    ];
}
