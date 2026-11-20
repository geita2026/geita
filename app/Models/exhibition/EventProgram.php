<?php

namespace App\Models\exhibition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EventProgram extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='program_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'program_id','program_title','program_description','program_file','event','share_program','program_date','characters'
    ];
}
