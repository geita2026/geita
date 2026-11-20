<?php

namespace App\Models\setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class level_one_menus extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='level_one_menu_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'level_one_menu_id','level_one_common_name','level_one_menu_status','level_one_icon_name',
        'level_one_menu_link','level_one_gate_name','moduleNo'
    ];
}
