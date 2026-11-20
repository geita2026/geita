<?php

namespace App\Models\advice_note;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class advice_note_deadlines extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='deadline_id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'advice_note_id','deadline_id','user_id','deadline_date'
    ];
}
