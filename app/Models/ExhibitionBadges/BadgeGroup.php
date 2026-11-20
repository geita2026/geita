<?php

namespace App\Models\ExhibitionBadges;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BadgeGroup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='group_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
    'group_id','group_name','text_color','group_background_color'
    ];
}
