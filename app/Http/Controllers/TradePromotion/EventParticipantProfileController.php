<?php

namespace App\Http\Controllers\TradePromotion;

use App\Http\Controllers\Controller;
use App\Models\sectors;
use App\Models\TradePromotion\EventParticipant;
use App\Models\TradePromotion\PromotionEvent;
use App\Traits\VistorsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EventParticipantProfileController extends Controller
{
    use VistorsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $events = PromotionEvent::where('eventStatus', 1)->get();
        $participants = EventParticipant::orderBy('participantRank', 'ASC')
            ->orderBy('participantCompany', 'ASC')
            ->where('participantStatus', 1)
            ->get([
                'participantId',
                'participantName',
                'participantEmail',
                'participantPhone',
                'participantWebsite',
                'participantProfile',
                'participantCompany',
                'participantTitle',
                'eventNumber',
                'companyProfile',
                'participantPhoto',
                'companyLogo',
                'participantStatus',
                'sector',
            ]);
        return view('TradePromotion.EventParticipantProfiles', compact('participants', 'events'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'participantName' => 'required',
            'participantEmail' => 'required',
            'participantPhone' => 'required',
            'areaOfInterest' => 'required|max:2000',
        ]);
        try {
            $exists = EventParticipant::where('participantCompany', $request->participantCompany)
                ->where('eventNumber', session('eventNumber'))->first();
            if ($exists) {
                return back()->with('success', 'We have already received your information Please Be Cam, we will contact your after Process completed!');
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
                'eventNumber' => session('eventNumber'),
                'companyProfile' => $request->areaOfInterest,
                'participantRank' => $request->participantRank,
                'sector' => $request->sector,
                'country' => $request->country,
                'region' => $request->region,
                'participantRank' => 1,

            ]);
            $event = PromotionEvent::where('eventNumber', session('eventNumber'))->first();
            $data = [
                'name' => $request->participantName,
                'eventName' => $event->eventTitle,
                'emailAddress' => $request->participantEmail,
                'subject' => 'Registration Notification',
            ];
            Mail::send('mails.TPERegistrationEmail', $data, function ($message) use ($data) {
                $message
                    ->to($data['emailAddress'], $data['eventName'])
                    ->priority(1)
                    ->subject($data['subject']);
            });
            return back()->with('success', 'Congratulation Your information received successfully, we will contact you as process completes!');
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
        $this->Vistors($ip = $request->ip()); // save new vistor
        $events = PromotionEvent::where('eventStatus', 1)->get();

        $participants = EventParticipant::orderBy('registeredAt', 'DESC')
            ->where('participantId', $id)
            ->get([
                'participantId',
                'participantName',
                'participantEmail',
                'participantPhone',
                'participantWebsite',
                'participantProfile',
                'participantCompany',
                'participantTitle',
                'eventNumber',
                'companyProfile',
                'participantPhoto',
                'companyLogo',
                'participantStatus',
                'sector'
            ]);

        return view('TradePromotion.MyProfile', compact('participants', 'events'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eventParticipantRegistration($event)
    {
        try {
            session(['eventNumber' => $event]);
            $event = PromotionEvent::where('eventNumber', $event)->first();
            if ($event) {
                if ($event->eventStatus == 0) {
                    return back()->with('fail', 'Registration for ' . $event->eventTitle . 'Link Not yet, Opened for Registration Call: ' . config('global.CALL_CENTER') . ' For Help');
                }
                $sectors = sectors::orderBy('sectorName', 'ASC')->get(['sectorID', 'sectorName']);
                return view('TradePromotion.ParticipantsRegistration', compact('event', 'sectors'));
            }
            return redirect()->route('index')->with('fail', 'Promotion Event Not Found!');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . ' Please Contact: ' . config('global.CALL_CENTER') . ' For help');
        }
    }
}
