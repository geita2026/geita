<?php

namespace App\Models\exhibition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class exhibitors extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='participation_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'participation_id','exhibitor_id','exhibitor_event','pavilion_descriptions',
        'welcome_word','pavilion_image_name','visibility_status','judging_location','judging_status','judgingCategory',
        'pStatus','participationCommitimentFile','fileIsEditable','exhibitedProducts','makeSales',
        'exhibitionSpace'
    ];
}
