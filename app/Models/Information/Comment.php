<?php

namespace App\Models\Information;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Comment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='commentId';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'commentId','commentedBy','comment','referenceId','commentStatus'
    ];
}
