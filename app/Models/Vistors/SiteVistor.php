<?php

namespace App\Models\Vistors;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteVistor extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='vistor_id';
    public $incrementing=false;
    protected $keyType='string';

    public $fillable=[
       'vistor_id','vistor_ip_address','page_visted','country','hostname','city','region','organization','timezone','coordinates'
    ];
}
