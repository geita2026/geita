<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\PrefairSeminar;
use App\Models\PrefairSeminarFeedBack;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SabasabaRegController extends Controller
{
    public function PrefairSeminarRegPage(Request $request)
    {
        try {
            $liveEvent = events::where('event_status', 1)->first();
            return view('TradePromotion.PrefairSerminarRegPage', compact('liveEvent'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }


    public function PrefairSeminarReg(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required|max:13',
            'fullName' => 'required|max:100',
            'email' => 'email|max:100',
            'TSRN' => 'required|exists:companies,registrationID'
        ]);
        try {
            $liveEvent = events::where('event_status', 1)->first()->event_id;
            $company = companies::where('registrationID', $request->TSRN)->first()->companyID;
            $participation = exhibitors::where('exhibitor_event', $liveEvent)
                ->where('exhibitor_id', $company)->first();
            if (!$participation) {
                return back()->with('fail', 'The TSRN you entered could not be found in the Exhibitors Catalogue. Please double-check the number and try again. If the issue persists, ensure that your registration was completed successfully or contact support for assistance. Only valid and registered TSRNs will appear in the official catalogue!');
            }
            $participationId = $participation->participation_id;
            $registeredUsers = PrefairSeminar::where('participationId', $participationId)->count();
            if ($registeredUsers) {
                return back()->with('fail', 'You have successfully reached the maximum number of participants allowed for the pre-fair seminar. Registration is now closed. We appreciate your interest and encourage you to stay engaged for future updates and opportunities related to the SabaSaba event. Thank you for your enthusiasm and participation!');
            }
            PrefairSeminar::create([
                'serminarId' => Str::uuid(),
                'fullName' => $request->fullName,
                'email' => $request->email,
                'phoneNumber' => $request->phoneNumber,
                'description' => $request->description,
                'participationId' => $participationId
            ]);
            return back()->with('success', 'We’re excited to inform you that your information has been received! You are cordially invited to join us at the prestigious Prefair Seminar 2025');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function FeedbBackPage(Request $request)
    {
        try {
            $liveEvent = events::where('event_status', 1)->first();
            return view('TradePromotion.PrefairFeedback', compact('liveEvent'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function FeedBackReg(Request $request)
    {
        $request->validate([
            'programName' => 'max:100',
            'attendeeFeedback' => 'required|max:2000',
            'saticifactionLevel' => 'max:100',
        ]);
        try {
            PrefairSeminarFeedBack::create([
                'feedBackId' => Str::uuid(),
                'programName' => $request->programName,
                'attendeeFeedback' => $request->attendeeFeedback,
                'saticifactionLevel' => $request->saticifactionLevel,
            ]);
            return back()->with('success', 'Thank you for your feedback! Your input is valuable and will help us improve future Prefair Seminar sessions and experiences.');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
