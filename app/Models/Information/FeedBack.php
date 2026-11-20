<?php

namespace App\Models\Information;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FeedBack extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    public $timestamps=false;
    protected $primaryKey='feedback_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'feedback_id','asked_by','attended_by','customer_comment','answer','is_faq'
    ];

}
