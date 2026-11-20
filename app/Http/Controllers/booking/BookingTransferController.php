<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_pavilions;
use App\Models\settings\events;
use App\Models\space\booths;
use App\Models\space\pavilions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class BookingTransferController extends Controller
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
    public function index(Request $request)
    {
        try {
            if ($request->user()->cannot('transfer-booking', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            if ($request->eventName!= null) {
                session(['transferEvent' => $request->eventName]);
            }
            $transfers = booked_pavilions::leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->leftJoin('companies', 'booked_pavilions.companyID', '=', 'companies.companyID')
                ->leftJoin('booths', 'booked_pavilions.pavilionID', '=', 'booths.boothID')
                ->leftJoin('users', 'booked_pavilions.transferredBy', '=', 'users.user_uuid')
                ->where('oldPavilionId', '!=', null)
                ->where('booked_pavilions.exhibition_year', session('transferEvent'))
                ->get();
                $events=events::get(['event_id', 'event_name']);
            return view('booking.TransferList', compact('transfers','events'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            if ($request->user()->cannot('transfer-booking', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            session('companyID');

            if ($request->companyName) {
                session(['companyID' => $request->companyName]);
            }
            if ($request->pavilionId) {
                session(['pavilionId' => $request->pavilionId]);
            }

            $pavilions = booked_pavilions::leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->leftJoin('pavilions', 'booked_pavilions.pavilionID', '=', 'pavilions.pavilionID')
                ->leftJoin('companies', 'booked_pavilions.companyID', '=', 'companies.companyID')
                ->leftJoin('events', 'booked_pavilions.exhibition_year', '=', 'events.event_id')
                ->where('booked_pavilions.companyID', session('companyID'))
                ->whereIn('booked_pavilions.pavilionID', pavilions::where('space_status', 'UNRESERVED')->get(['pavilionID']))
                ->get([
                    'bookingID',
                    'booked_pavilions.pavilionID as spaceId',
                    'companyName',
                    'pavilions.pavilionName as spaceName',
                    'events.event_name',
                    'advice_notes.advice_note_id',
                    'total_amount',
                    'amount_paid',
                    'currency',
                    'transferStatus',
                ]);

            $booths = booked_pavilions::leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->leftJoin('booths', 'booked_pavilions.pavilionID', '=', 'booths.boothID')
                ->leftJoin('companies', 'booked_pavilions.companyID', '=', 'companies.companyID')
                ->leftJoin('events', 'booked_pavilions.exhibition_year', '=', 'events.event_id')
                ->where('booked_pavilions.companyID', session('companyID'))
                ->whereIn('booked_pavilions.pavilionID', booths::where('bookingStatus', 'UNRESERVED')->pluck('boothID'))
                ->get([
                    'bookingID',
                    'booths.boothID as spaceId',
                    'companyName',
                    'booths.boothNo as spaceName',
                    'events.event_name',
                    'advice_notes.advice_note_id',
                    'total_amount',
                    'amount_paid',
                    'currency',
                    'transferStatus',
                ]);
            $booking = $pavilions->merge($booths);
            // This query return the list of pavilions but must have boots
            $pavilions = pavilions::where('space_status', 'UNRESERVED')
                ->whereIn('pavilionID', booths::where('bookingStatus', 'UNRESERVED')->get('pavilionID'))->get([
                    'pavilionID',
                    'pavilionName',
                ]);

            $booths = booths::where('bookingStatus', 'UNRESERVED')
                ->where('pavilionID', session('pavilionId'))
                ->whereNotIn('boothID', booked_pavilions::where('exhibition_year', session('liveEvent'))->get(['pavilionID']))
                ->orderBy('boothNo', 'ASC')
                ->get([
                    'boothID',
                    'boothNo',
                ]);


            return view('booking.CreateTransfer', compact('booking', 'pavilions', 'booths'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if ($request->user()->cannot('transfer-booking', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validator = Validator::make($request->all(), [
                'bookingId' => 'required',
                'newBoothId' => 'required',
                'transferReasons' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', $validator->errors()->first());
            }
            $booking = booked_pavilions::find($request->bookingId);
            $booking->update([
                'oldPavilionId' => $booking->pavilionID,
                'transferStatus' => "UNDER_REVIEW",
                'pavilionID' => $request->newBoothId,
                'transferredBy' => Auth::user()->user_uuid,
                'transferReasons' => $request->transferReasons,
            ]);
            $booth = booths::find($booking->oldPavilionId);
            $booth->update([
                'bookingStatus' => 'RESERVED',
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $booths = booths::where('pavilionID', '0')->get(); // default No Booths;

        if ($request->pavilionId) {
            $booths = booths::orderBy('boothNo', 'ASC')
                ->leftJoin('booked_pavilions', 'booths.boothID', '=', 'booked_pavilions.pavilionID')
                ->where('booths.pavilionID', $request->pavilionId)
                ->where('advice_note_id', null)->get();
        }
        $booked_pavilions = booths::leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
            ->leftJoin('booked_pavilions', 'booths.boothID', '=', 'booked_pavilions.pavilionID')
            ->where('advice_note_id', $id)
            ->leftJoin('users', 'booked_pavilions.transferredBy', '=', 'users.user_uuid')
            ->get();

        $oldPavilions = booths::leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
            ->leftJoin('booked_pavilions', 'booths.boothID', '=', 'booked_pavilions.oldPavilionId')
            ->where('advice_note_id', $id)->get();
        session(['adviceNote' => $id]);
        $pavilions = pavilions::where('bookingStatus', 'UNRESERVED')->get();
        return view('booking.bookingTransfer', compact('booked_pavilions', 'booths', 'pavilions', 'oldPavilions',));
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
            if ($request->user()->cannot('approve-transfer', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $booking = booked_pavilions::find($id);
            $booking->update([
                'transferStatus' => $request->transferStatus,
                'transferApprovedBy' => Auth::user()->user_uuid,
            ]);
            $booking = booked_pavilions::find($id);
            $booth = booths::find($booking->oldPavilionId);
            $booth->update([
                'bookingStatus' => 'UNRESERVED',
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }
}
