<?php

namespace App\Models\exhibition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CatalogueSetting extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='leader_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'leader_id','leader_name','leader_title','leader_image','appriciation_word','order_number','event'
    ];
}
