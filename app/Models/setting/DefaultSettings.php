<?php

namespace App\Models\setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DefaultSettings extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='default_setting_id';
    public $incrementing=false;
    protected $keyType='string';
   protected $fillable = [
    'default_setting_id',
    'notification_channel',
    'setting_created_by',
    'invoice_deadline_date',
    'invoice_other_information',
    'system_title',
    'system_version',
    'self_printing',
    'badging_event',
    'active_event',
    'print_notification',
    'applicationStatus',
    'exchangeRate',
    'allowedNetworks',
    'badge_live_test',
];

}
