<?php

namespace App\Models\Printing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FacialNameGroup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='group_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'group_id','group_name','background_color','text_color'
    ];
}
