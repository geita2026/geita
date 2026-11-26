<?php

namespace App\Http\Controllers\Badges;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\ExhibitionBadges\BadgesAllocation;
use App\Models\settings\events;
use App\Models\Stickers\StickerAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class BadgesAllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

        $this->middleware('auth');
    }

    public function edit(Request $request, $id)
    {
       
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('VIEW_BADGE_ALLOCATION', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('VIEW_BADGE_ALLOCATION'));
            }
            $allocations = BadgesAllocation::leftJoin('companies', 'badges_allocations.company_id', '=', 'companies.companyID')
                ->leftJoin('events', 'badges_allocations.event_id', '=', 'events.event_id')
                ->where('badges_allocations.company_id', $id)
                ->orderBy('allocation_created_at', 'DESC')
                ->paginate(10, [
                    'badge_allocation_id',
                    'company_id',
                    'companyName',
                    'badges_allocated',
                    'free_badges',
                    'badges_used',
                    'event_name',
                    'free_badges as provissionalBadges',
                    'badges_allocations.event_id'
                ]);
            $exist = BadgesAllocation::where('company_id', $id)
            ->where('event_id',session('liveEvent'))->first();
            $invoice=advice_notes::where('company_id',$id)
            ->where('advice_note_status','APPROVED')
            ->where('event',session('liveEvent'))->first();
            if (!$exist) {
                BadgesAllocation::create([
                    'badge_allocation_id' => Str::uuid(),
                    'company_id' => $id,
                    'badges_allocated' => 0,
                    'event_id' => session('liveEvent'),
                    'badges_used' => 0,
                    'free_badges' => 0,
                    'reasons' => Null,
                ]);
            }
            $adviceNote = advice_notes::where('event', session('liveEvent'))
                ->where('company_id', $id)->where('advice_note_status', 'APPROVED')
                ->where('advice_note_description', '!=', null)
                ->get(['advice_note_id', 'company_id', 'advice_note_description', 'total_amount']);
            return view('Badges.BadgeAllocation', compact('allocations', 'adviceNote'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('UPDATE_BADGES_ALLOCATIONS', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('UPDATE_BADGES_ALLOCATIONS'));
            }

            $allocations = BadgesAllocation::where('badge_allocation_id', $id)
                ->where('event_id', session('liveEvent'))->first();

            if ($allocations) {
                BadgesAllocation::find($allocations->badge_allocation_id)->update([
                    'free_badges' => $request->free_badges,
                    'reasons' => $request->reasons,
                    'free_badge_added_by' => Auth::user()->user_uuid,
                ]);
            }

            return back()->with('success', 'Provission badges added successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('CLEAR_BADGE_ALLOCATION', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('CLEAR_BADGE_ALLOCATION'));
        }
    }
}
