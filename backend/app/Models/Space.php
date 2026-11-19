<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['size_label'];

    protected function casts(): array
    {
        return [
            'width_m' => 'float', 'length_m' => 'float', 'area_sqm' => 'float', 'price' => 'float',
            'facilities' => 'array',
        ];
    }

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function category()
    {
        return $this->belongsTo(SpaceCategory::class, 'space_category_id');
    }

    /** The one live allocation, if any (see allocations.active_space_id). */
    public function activeAllocation()
    {
        return $this->hasOne(Allocation::class, 'active_space_id');
    }

    public function getSizeLabelAttribute(): string
    {
        return rtrim(rtrim(number_format($this->width_m, 2), '0'), '.').'m × '.rtrim(rtrim(number_format($this->length_m, 2), '0'), '.').'m';
    }
}
