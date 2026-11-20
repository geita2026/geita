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
            ->where('advice_notes.advice_note_id', $id)->where('particular_name', 'LIKE', "%badges%")->first(); // get only for badges fields with respect to invoice ID
        if ($invoice) {
            $added_badges = $invoice->particular_quantity; // added badges
            $company_id = $invoice->company_id;
            $event_id = $invoice->event;
            $badges = BadgesAllocation::where('company_id', $company_id)->where('event_id', $event_id)->first();
            $total_badges = $added_badges + $badges->badges_allocated; // sum any added badges
            BadgesAllocation::find($badges->badge_allocation_id)->update(['badges_allocated' => $total_badges]);
        }
        return 1;
    }
    #--------- END UPDATED BADGES ------------#


    #---------- UPDATE USED BADGES --------#
    private function UpdateUsedBadges($event_id, $company_id)
    {
        $exist = BadgesAllocation::where('company_id', $company_id)->where('event_id', $event_id)->first();

        if ($exist) {
            $company_id = $company_id;
        } else {
            $company_id = companies::where('companyID', $company_id)->first()->associate_company;
        }
        BadgesAllocation::find($exist->badge_allocation_id)->update(['badges_used' => (1 + $exist->badges_used)]);
        return 1;
    }
    #---------- END UPDATE USED BADGES--------#

    #------ CLEAR BADGE ALLOCATION---------#
    private function ClearBadgeAllocated($id)
    {
        $badgeNo = particulars::where('advice_note_id', $id)
            ->where('particular_name', 'LIKE', "%badges%")->sum('particular_quantity');

        $invoice = advice_notes::where('advice_note_id', $id)->first();
        if ($badgeNo > 0) {
            $company_id = $invoice->company_id;
            $event_id = $invoice->event;
            $badges = BadgesAllocation::where('company_id', $company_id)->where('event_id', $event_id)->first();
            if ($badges) {
                $badges_allocated = $badges->badges_allocated - $badgeNo; // reduce number of budges for canceled invoice
                BadgesAllocation::find($badges->badge_allocation_id)->update(['badges_allocated' => $badges_allocated]);
            }
        }
        return 1;
    }


    # when badge deleted badges_used
    private function DeleteBadge($id)
    {
        $badge = CompanyMember::where('member_id', $id)->first();
        $company_id = $badge->company_id;
        $event_id = $badge->event;

        $badgesUsed = CompanyMember::where('company_id', $company_id)
            ->where('event', $event_id)->count();

        if (BadgesAllocation::where('company_id', $company_id)->exists()) {
            $company_id = $company_id;
        } else {
            $company_id = companies::where('companyID', $company_id)->first()->associate_company;
        }
        $badges = BadgesAllocation::where('company_id', $company_id)->where('event_id', $event_id)->first();
        BadgesAllocation::find($badges->badge_allocation_id)->update(['badges_used' => $badgesUsed - 1]);
        return 1;
    }
    # End when Badge deleted

    #------ CLEAR BADGE ALLOCATION---------#

}
