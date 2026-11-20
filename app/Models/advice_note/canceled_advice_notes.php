<?php

namespace App\Models\advice_note;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class canceled_advice_notes extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='id';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'id','advice_note_id','canceled_by','reasons'
    ];
}
