<?php

namespace App\Models\exhibition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class JudgingCategory extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='categoryId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
        'categoryId','categoryName','description','categoryStatus'
    ];

}
