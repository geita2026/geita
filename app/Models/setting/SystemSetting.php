<?php

namespace App\Models\setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SystemSetting extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='setting_id';
    public $incrementing=false;
    protected $keyType='string';
    protected $fillable=[
        'setting_id','edited_by','control_number_deadline','invoice_other_information',
        'level_two_menu_link','level_two_gate_name','upper_level_id'
    ];
}
