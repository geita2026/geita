<?php

namespace App\Models\Vistors;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroundVisitor extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='countId';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
       'countId','countType','totalCount','countDate'
    ];
}

//   