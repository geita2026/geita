<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaceCategory extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['price_per_sqm' => 'float'];
    }

    public function spaces()
    {
        return $this->hasMany(Space::class);
    }
}
