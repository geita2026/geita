<?php

namespace App\Models\ExhibitionBadges;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BadgesAllocation extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='badge_allocation_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
    'badge_allocation_id','company_id','badges_allocated','event_id','badges_used','free_badges','reasons','attachment','free_badge_added_by','migrated_badges'
    ];
}
