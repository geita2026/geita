<?php
namespace App\Models;

use App\Models\Companies\CompanyAdmin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class companies extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='companyID';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'companyID','registrationID','companyName','companyEmail',
        'companyPhone','companyCategory','companyLocation','companySector',
        'status','sp_customer_no','region','associate_company','isAcceptedTerms','companyLogo','status',
        'referenceModule','originality','tinNo','companyAddress','website',
        'personalName','personalEmail','personalPhone','personalTitle','tinStatus',
        'brnStatus','registrationNo','hasOtherCompanies','lastUpdatedOn'
    ];

    public function companyAdmins()
    {
        return $this->hasMany(CompanyAdmin::class, 'companyId', 'companyID');
    }

}
