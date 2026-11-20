<?php

namespace App\Models\sponsorships;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PackageGroup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='packageGroupId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
    'packageGroupId','packageGroupName','packageGroupDescription','packageGroupStatus',
    'backgroundColor','textColor'
    ];
}
