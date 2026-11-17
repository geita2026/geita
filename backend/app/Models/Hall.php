<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    protected $guarded = ['id'];

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function spaces()
    {
        return $this->hasMany(Space::class)->orderBy('map_row')->orderBy('map_col');
    }
}
