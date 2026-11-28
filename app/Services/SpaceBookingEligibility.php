<?php

namespace App\Services;

use App\Models\companies;
use App\Models\space\AreaDistribution;
use App\Models\space\booths;
use App\Models\space\pavilions;

class SpaceBookingEligibility
{
    public function sectorIds(booths|pavilions $space)
    {
        return AreaDistribution::where('spaceId', $space->getKey())->pluck('participants');
    }

    public function allows(booths|pavilions $space, companies $company): bool
    {
        $sectors = $this->sectorIds($space);
        $companyId = $space->whoCanBook;

        if ($space instanceof booths && !$companyId && $sectors->isEmpty()) {
            $pavilion = pavilions::find($space->pavilionID);

            return $pavilion !== null && $this->allows($pavilion, $company);
        }

        $matchesCompany = !$companyId || $companyId === 'ANY' || $companyId === $company->companyID;

        return $matchesCompany && ($sectors->isEmpty() || $sectors->contains($company->companySector));
    }
}
