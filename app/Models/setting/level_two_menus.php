<?php

namespace App\Models\setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class level_two_menus extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='level_two_menu_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'level_two_menu_id','level_two_common_name','level_two_menu_status','level_two_icon_name',
        'level_two_menu_link','level_two_gate_name','upper_level_id'
    ];
}
