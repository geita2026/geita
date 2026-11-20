<?php

namespace App\Models\ExhibitionBadges;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Badges extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='badge_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
    'badge_id','organization_id','badge_status','event_id','member_id'
    ];
}
