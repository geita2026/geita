<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class menus extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='menu_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'menu_id','user_id','resource_name','gate_name','common_name','status','icon_name','sidebar_season'
    ];
}
