<?php
namespace App\Models\sponsorships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class sponsorship_packages extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='package_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
    'package_name','package_id','package_amount','package_description',
    'type_of_currency','gfs_code','packageStatus','packageGroup'
    ];
}
