<?php

namespace App\Http\Controllers\TradePromotion;

use App\Http\Controllers\Controller;
use App\Models\TradePromotion\EventParticipant;
use App\Models\TradePromotion\PromotionEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class EventParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('MANAGE_EVENT_PARTICIPANT', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_EVENT_PARTICIPANT'));
        }
        $event=$request->eventNumber;
        $participants = EventParticipant::orderBy('participantRank','ASC')
        ->where('eventNumber',$event)
        ->get([
            'participantId', 'participantName', 'participantEmail', 'participantPhone',
            'participantWebsite','participantProfile', 'participantCompany', 'participantTitle', 
            'eventNumber','companyProfile', 'participantPhoto', 'companyLogo','participantStatus','participantRank','sector'
        ]);
        $events=PromotionEvent::orderBy('eventCreatedAt','DESC')->get([
            'eventId','eventTitle','eventNumber'
        ]);
        return view('TradePromotion.EventParticipants', compact('participants','events'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('MANAGE_EVENT_PARTICIPANT', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_EVENT_PARTICIPANT'));
        }
        $request->validate([
            'participantName' => 'required',
            'participantEmail' => 'required',
            'participantPhone' => 'required',
            'participantPhoto' => 'file|mimes:png,jpg,jpeg',
            'companyLogo' => 'file|mimes:png,jpg,jpeg',
        ]);
        try {
            $eventNumber= $request->eventNumber;
            $companyLogo = Null;
            $participantPhoto = Null;
            if (!empty($request->participantPhoto)) {
                $imageName = Str::uuid() . '.' . $request->participantPhoto->extension();
                $request->participantPhoto->move(public_path('TradePromotionFiles'), $imageName);
                $participantPhoto = env('APP_URL') . '/public/TradePromotionFiles/' . $imageName;
            }
            if (!empty($request->companyLogo)) {
                $imageName = Str::uuid() . '.' . $request->companyLogo->extension();
                $request->companyLogo->move(public_path('TradePromotionFiles'), $imageName);
                $companyLogo = env('APP_URL') . '/public/TradePromotionFiles/' . $imageName;
            }
            EventParticipant::create([
                'participantId' => 'TPN-' . date('y-') . random_int(500, 999),
                'participantName' => $request->participantName,
                'participantEmail' => $request->participantEmail,
                'participantPhone' => $request->participantPhone,
                'participantWebsite' => $request->participantWebsite,
                'participantProfile' => $request->participantProfile,
                'participantCompany' => $request->participantCompany,
                'participantTitle' => $request->participantTitle,
                'eventNumber' => $eventNumber,
                'companyProfile' => $request->companyProfile,
                'participantPhoto' => $participantPhoto,
                'companyLogo' => $companyLogo,
                'participantRank'=>$request->participantRank,
                'sector'=>$request->sector
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
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
        if ($request->user()->cannot('MANAGE_EVENT_PARTICIPANT', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_EVENT_PARTICIPANT'));
        }
    }

    public function show(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('DELETE_EVENT_PARTICIPANT', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('DELETE_EVENT_PARTICIPANT'));
        }

        try {

            $event = EventParticipant::find($id);
            $companyLogo = $event->companyLogo;
            $participantPhoto=$event->participantPhoto;
            if ($companyLogo != null) {
                $imageName = str_replace(env('APP_URL') . '/public/TradePromotionFiles/', '', $companyLogo);
                File::delete(public_path('TradePromotionFiles/') . $imageName);
            }
            if ($participantPhoto != null) {
                $imageName = str_replace(env('APP_URL') . '/public/TradePromotionFiles/', '', $participantPhoto);
                File::delete(public_path('TradePromotionFiles/') . $imageName);
            }
            $event->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function updateParticipant(Request $request){
        if ($request->user()->cannot('MANAGE_EVENT_PARTICIPANT', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_EVENT_PARTICIPANT'));
        }
        $id=$request->participantId;
        $request->validate([
            'participantName' => 'required',
            'participantEmail' => 'required',
            'participantPhone' => 'required',
            'participantPhoto' => 'file|mimes:png,jpg,jpeg',
            'companyLogo' => 'file|mimes:png,jpg,jpeg',
        ]);

        try {
            $participant = EventParticipant::find($id);

            $companyLogo = $participant->companyLogo;
            $participantPhoto =$participant->participantPhoto;

            if (!empty($request->participantPhoto)) {
                #### DELETE OLD IMAGE ######
                $imageUrl = $participant->eventPoster;
                if ($participantPhoto != null) {
                    $imageName = str_replace(env('APP_URL') . '/public/TradePromotionFiles/', '', $participantPhoto);
                    File::delete(public_path('TradePromotionFiles/') . $imageName);
                }
                ############## DELETE OLD IMAGE ###
                
                $imageName = Str::uuid() . '.' . $request->participantPhoto->extension();
                $request->participantPhoto->move(public_path('TradePromotionFiles'), $imageName);
                $participantPhoto = env('APP_URL') . '/public/TradePromotionFiles/' . $imageName;
            }
            if (!empty($request->companyLogo)) {
                #### DELETE OLD IMAGE ######
                $imageUrl = $participant->eventPoster;
                if ($companyLogo != null) {
                    $imageName = str_replace(env('APP_URL') . '/public/TradePromotionFiles/', '', $companyLogo);
                    File::delete(public_path('TradePromotionFiles/') . $imageName);
                }
                ############## DELETE OLD IMAGE ###

                $imageName = Str::uuid() . '.' . $request->companyLogo->extension();
                $request->companyLogo->move(public_path('TradePromotionFiles'), $imageName);
                $companyLogo = env('APP_URL') . '/public/TradePromotionFiles/' . $imageName;
            }

            $participant->update([
                'participantName' => $request->participantName,
                'participantEmail' => $request->participantEmail,
                'participantPhone' => $request->participantPhone,
                'participantWebsite' => $request->participantWebsite,
                'participantProfile' => $request->participantProfile,
                'participantCompany' => $request->participantCompany,
                'participantTitle' => $request->participantTitle,
                'eventNumber' => $request->eventNumber,
                'companyProfile' => $request->companyProfile,
                'participantPhoto' => $participantPhoto,
                'companyLogo' => $companyLogo,
                'participantStatus'=>$request->participantStatus,
                'participantRank'=>$request->participantRank,
                'sector'=>$request->sector
            ]);
            return back()->with('success', 'Event updated successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong') . $th->getMessage();
        }
    }
}
