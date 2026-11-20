<?php

namespace App\Models\guidelines;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Guideline extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='guideline_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'guideline_id','guideline_title','guideline_descriptions'
    ];
}
