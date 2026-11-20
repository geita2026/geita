<?php

namespace App\Models\Stickers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CarSticker extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='sticker_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'sticker_id','sticker_title','company_id','event_id','printed_by','reprented_by','reference_sticker','sticker_status',
        'plate_number','imegency_plate_number','design_group','search_id','print_id','backupStatus','deleteStatus'
    ];
}
