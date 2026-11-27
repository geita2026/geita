<?php

namespace App\Traits;

use App\Models\advice_note\advice_notes;
use App\Models\advice_note\particulars;
use App\Models\companies;
use App\Models\ExhibitionBadges\BadgesAllocation;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\settings\events;
use Illuminate\Support\Str;

trait BadgesTraits
{

    # ------- ALLOCATE BADGES -----#
    private function AllocateBages($id)
    {
        $invoice = advice_notes::leftJoin('particulars', 'advice_notes.advice_note_id', '=', 'particulars.advice_note_id')
            ->where('advice_notes.advice_note_id', $id)->where('particular_name', 'LIKE', "%badges%")->first(); // get only for badges fields with respect to invoice ID

        if ($invoice) {
            $badges_Allocated = $invoice->particular_quantity;
            $company_id = $invoice->company_id;
            $event_id = $invoice->event;

            $badges = BadgesAllocation::where('company_id', $company_id)->where('event_id', $event_id)->first(); // Checking if exists

            if (!$badges) // if company does not exists
            {
                BadgesAllocation::create([
                    'badge_allocation_id' => Str::uuid(),
                    'company_id' => $company_id,
                    'badges_allocated' => $badges_Allocated,
                    'event_id' => $event_id,
                ]);
                return 1;
            } else {
                $this->UpdateAllocatedBadges($id);
            }
        }
    }
    #--------- END ALLOCATE BADGES--------#

    #----------- UPDATE ALLOCATED BADGES-------#
    private function UpdateAllocatedBadges($id)
    {
        $invoice = advice_notes::leftJoin('particulars', 'advice_notes.advice_note_id', '=', 'particulars.advice_note_id')
            ->where('advice_notes.advice_note_id', $id)
            ->where('particular_name', 'LIKE', '%badges%')
            ->first();

        if (!$invoice) {
            return 0;
        }

        $allocation = BadgesAllocation::where('company_id', $invoice->company_id)
            ->where('event_id', $invoice->event)
            ->lockForUpdate()
            ->first();

        if (!$allocation) {
            BadgesAllocation::create([
                'badge_allocation_id' => Str::uuid(),
                'company_id' => $invoice->company_id,
                'event_id' => $invoice->event,
                'badges_allocated' => (int) $invoice->particular_quantity,
                'badges_used' => 0,
            ]);
            return 1;
        }

        $allocation->increment('badges_allocated', (int) $invoice->particular_quantity);
        return 1;
    }
    #--------- END UPDATED BADGES ------------#


    #---------- UPDATE USED BADGES --------#
    private function UpdateUsedBadges($event_id, $company_id, $associateCompany = null)
    {
        // Child usage is stored against the child's own allocation record.
        if ($associateCompany) {
            $childAllocation = BadgesAllocation::where('company_id', $associateCompany)
                ->where('event_id', $event_id)
                ->lockForUpdate()
                ->first();

            $childUsed = CompanyMember::where('company_id', $company_id)
                ->where('associateCompany', $associateCompany)
                ->where('event', $event_id)
                ->where('badge_status', '!=', 'ARCHIVED')
                ->count();

            if ($childAllocation) {
                $childAllocation->update(['badges_used' => $childUsed]);
            }

            $this->RecalculateParentBadgeUsage($event_id, $company_id);
            return 1;
        }

        $this->RecalculateParentBadgeUsage($event_id, $company_id);
        return 1;
    }

    /**
     * Parent usage includes direct active members and free badges reserved
     * for its children. Child-purchased allocations remain child-owned.
     */
    private function RecalculateParentBadgeUsage($event_id, $parentCompanyId)
    {
        $parentAllocation = BadgesAllocation::where('company_id', $parentCompanyId)
            ->where('event_id', $event_id)
            ->lockForUpdate()
            ->first();

        if (!$parentAllocation) {
            return 0;
        }

        $directMembers = CompanyMember::where('company_id', $parentCompanyId)
            ->where('event', $event_id)
            ->where(function ($query) {
                $query->whereNull('associateCompany')->orWhere('associateCompany', '');
            })
            ->where('badge_status', '!=', 'ARCHIVED')
            ->count();

        $childIds = companies::where('associate_company', $parentCompanyId)->pluck('companyID');
        $childReserved = BadgesAllocation::whereIn('company_id', $childIds)
            ->where('event_id', $event_id)
            ->sum('free_badges');

        $parentAllocation->update([
            'badges_used' => $directMembers + (int) $childReserved,
        ]);

        return 1;
    }
    #---------- END UPDATE USED BADGES--------#

    #------ CLEAR BADGE ALLOCATION---------#
    private function ClearBadgeAllocated($id)
    {
        $badgeNo = particulars::where('advice_note_id', $id)
            ->where('particular_name', 'LIKE', '%badges%')
            ->sum('particular_quantity');

        $invoice = advice_notes::where('advice_note_id', $id)->first();
        if (!$invoice || $badgeNo <= 0) {
            return 1;
        }

        $allocation = BadgesAllocation::where('company_id', $invoice->company_id)
            ->where('event_id', $invoice->event)
            ->lockForUpdate()
            ->first();

        if ($allocation) {
            $allocation->update([
                'badges_allocated' => max(0, (int) $allocation->badges_allocated - (int) $badgeNo),
            ]);
        }

        return 1;
    }


    # when badge deleted badges_used
    private function DeleteBadge($id)
    {
        $badge = CompanyMember::where('member_id', $id)->first();
        if (!$badge) return 0;

        $eventId = $badge->event;
        $companyId = $badge->company_id;
        $associateCompany = $badge->associateCompany ?: null;

        if ($associateCompany) {
            $childUsed = CompanyMember::where('company_id', $companyId)
                ->where('associateCompany', $associateCompany)
                ->where('event', $eventId)
                ->where('member_id', '!=', $id)
                ->where('badge_status', '!=', 'ARCHIVED')
                ->count();

            BadgesAllocation::where('company_id', $associateCompany)
                ->where('event_id', $eventId)
                ->update(['badges_used' => $childUsed]);
        } else {
            $parentUsed = CompanyMember::where('company_id', $companyId)
                ->where('event', $eventId)
                ->where('member_id', '!=', $id)
                ->where(function ($query) {
                    $query->whereNull('associateCompany')->orWhere('associateCompany', '');
                })
                ->where('badge_status', '!=', 'ARCHIVED')
                ->count();

            $childIds = companies::where('associate_company', $companyId)->pluck('companyID');
            $childReserved = BadgesAllocation::whereIn('company_id', $childIds)
                ->where('event_id', $eventId)->sum('free_badges');

            BadgesAllocation::where('company_id', $companyId)
                ->where('event_id', $eventId)
                ->update(['badges_used' => $parentUsed + (int) $childReserved]);
        }

        return 1;
    }
    # End when Badge deleted

    #------ CLEAR BADGE ALLOCATION---------#

}
