<?php

namespace App\Http\Controllers\Catalogy;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_pavilions;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\exhibition\VirtualService;
use App\Models\sectors;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogyController extends Controller
{
    public function index(Request $request)
    {
        return $this->directory($request, $request->query('sectorId'));
    }

    public function edit(Request $request, $id)
    {
        return $this->directory($request, $id);
    }

    private function directory(Request $request, $sectorId)
    {
        $event = events::where('event_status', 1)->first();
        $search = $request->query('q');
        $search = is_string($search) ? trim($search) : '';
        $sectorId = is_string($sectorId) ? $sectorId : '';

        $exhibitors = companies::query()
            ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->when($event, function ($query) use ($event) {
                $participants = exhibitors::where('exhibitor_event', $event->event_id)->select('exhibitor_id');
                $occupants = $this->publicLocations($event->event_id)->select('booked_pavilions.companyID');
                $query->where(function ($query) use ($participants, $occupants) {
                    $query->whereIn('companies.companyID', $participants)
                        ->orWhereIn('companies.companyID', $occupants);
                });
            }, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($search !== '', fn ($query) => $query->where('companies.companyName', 'like', '%' . $search . '%'))
            ->when($sectorId !== '', fn ($query) => $query->where('companies.companySector', $sectorId))
            ->orderBy('companies.companyName')
            ->orderBy('companies.companyID')
            ->paginate(24, ['companies.companyID', 'companies.companyName', 'sectors.sectorName'])
            ->appends(['q' => $search, 'sectorId' => $sectorId]);

        $sectors = sectors::orderBy('sectorName')->get(['sectorID', 'sectorName']);

        return view('FrontEnd.Public_Pages.ExhibitorCategories', compact('event', 'exhibitors', 'sectors', 'sectorId', 'search'));
    }

    public function show(Request $request, $id)
    {
        $event = events::where('event_status', 1)->firstOrFail();
        $participation = exhibitors::where('exhibitor_event', $event->event_id)
            ->where(function ($query) use ($id) {
                $query->where('participation_id', $id)->orWhere('exhibitor_id', $id);
            })->first();
        $companyId = $participation->exhibitor_id ?? $id;
        $company = companies::findOrFail($companyId);

        $locations = $this->publicLocations($event->event_id)
            ->where('booked_pavilions.companyID', $companyId)
            ->orderBy('pavilions.pavilionName')->orderBy('booths.boothNo')
            ->distinct()
            ->get(['pavilions.pavilionID', 'pavilions.pavilionName', 'booths.boothID', 'booths.boothNo']);

        abort_if(!$participation && $locations->isEmpty(), 404);
        session(['companyID' => $companyId]);

        $myProducts = $participation
            ? VirtualService::where('participation_id', $participation->participation_id)->get(['service_name'])
            : collect();
        $website = $company->website ?: DB::table('company_details')->where('companyID', $companyId)->value('website');

        return view('FrontEnd.Public_Pages.ExhibitorPavilion', compact(
            'company', 'participation', 'locations', 'myProducts', 'website'
        ));
    }

    private function publicLocations(string $eventId)
    {
        return booked_pavilions::query()
            ->leftJoin('booths', 'booked_pavilions.pavilionID', '=', 'booths.boothID')
            ->join('pavilions', 'pavilions.pavilionID', '=', DB::raw('COALESCE(booths.pavilionID, booked_pavilions.pavilionID)'))
            ->leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
            ->where('booked_pavilions.exhibition_year', $eventId)
            ->where(function ($query) {
                $query->whereNull('pavilions.space_status')->orWhere('pavilions.space_status', '!=', 'SUNSPENDED');
            })
            ->where(function ($query) {
                $query->whereNull('booths.boothID')->orWhere('booths.bookingStatus', '!=', 'SUNSPENDED');
            })
            ->where(function ($query) {
                $query->where('advice_notes.advice_note_status', '!=', 'CANCELED')
                    ->orWhere(function ($query) {
                        $query->whereNull('booked_pavilions.advice_note_id')
                            ->where('booked_pavilions.booking_status', 'ACTIVE');
                    });
            });
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function sabaSabaVisitors(){
        return view('sabasabaVisitors');
    }
}
