<?php

namespace App\Models\Stickers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class StickerAllocation extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='allocation_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'allocation_id','sticker_id','company_id','event_id','provided_stickers','provided_by','reasons','attachment'
    ];
}
