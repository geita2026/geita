<?php

namespace App\Models\advice_note;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class particulars extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='particular_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'advice_note_id','particular_id','particular_name','particular_quantity','cost_for_one_particular','type_of_currency','contain_vat'
        ,'particular_description','particular_type','particular_category','gfs_code'
    ];
}
