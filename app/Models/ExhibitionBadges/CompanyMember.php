<?php
namespace App\Models\ExhibitionBadges;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CompanyMember extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='member_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
    'member_id','company_id','first_name','middle_name','last_name','member_group','member_image','badge_status','event','phone_number'
    ,'blocked_by','unblocked_by','print_session_id','search_session_id','scan_count','base64Image','backupStatus','other_descriptions','associateCompany'
    ];
}
