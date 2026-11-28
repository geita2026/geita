<?php

namespace App\Models\space;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class pavilions extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    public $timestamps=false;
    protected $primaryKey='pavilionID';
    public $incrementing=false;
    protected $keyType='string';
    public $fillable=[
        'pavilionID','pavilionName','pavilionCode','userID','TotalBooths','floor_plan','spaceStreet',
        'pavilionCategory','pavilionRate','pavilionDescription','bookingStatus','pavilion_size','space_status',
        'allowedForBooking','whoCanBook','numberOfPartitions','latitude','longitude','location'
    ];

    public function mapBooths()
    {
        return $this->hasMany(booths::class, 'pavilionID', 'pavilionID')
            ->leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
            ->where('booths.bookingStatus', '!=', 'SUNSPENDED')
            ->orderByRaw("CASE WHEN booths.location IS NULL OR booths.location = '' THEN 1 ELSE 0 END")
            ->orderByRaw('CAST(booths.location AS UNSIGNED)')
            ->orderBy('booths.boothNo')
            ->orderBy('booths.boothID')
            ->select('booths.*', 'booth_categories.category_cost', 'booth_categories.type_of_currency');
    }

    public function sharedMapPavilions()
    {
        if ($this->pavilionCode === null || $this->pavilionCode === '') {
            return collect();
        }

        return self::query()
            ->leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
            ->where('pavilions.pavilionCode', $this->pavilionCode)
            ->where(function ($query) {
                $query->where('pavilions.space_status', '!=', 'SUNSPENDED')
                    ->orWhere('pavilions.pavilionID', $this->pavilionID);
            })
            ->with('mapBooths')
            ->orderBy('pavilions.pavilionName')
            ->orderBy('pavilions.pavilionID')
            ->get(['pavilions.*', 'space_categories.category_name']);
    }
}
