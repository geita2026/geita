<?php

namespace App\Http\Controllers\ExpoControllers;

use App\Http\Controllers\Controller;
use App\Models\ExpoParticipant;
use Illuminate\Http\Request;

class ExpoBackEndController extends Controller
{
    public function ExpoDashboard(Request $request)
    {
        if ($request->status) {
            $expoData = ExpoParticipant::where('country','!=','Tanzania')
            ->where('participantStatus',$request->status)
            ->orderBy('createdAt', 'DESC')
            ->orderBy('companyName', 'ASC')->get();
    
        } else {
            $expoData = ExpoParticipant::orderBy('createdAt', 'DESC')->orderBy('companyName', 'ASC')->get();
           
        }

        $registered = ExpoParticipant::count();
        $confirmed = ExpoParticipant::where('participantStatus','APPROVED')->count();
        return view('ExpoPage.ExpoDashbooard', compact('expoData','registered','confirmed'));
    }

    public function approveConfirmedParticipant(Request $request, $participantId)
    {
        try {
            $participant = ExpoParticipant::find($participantId);
            if ($participant) {
                if ($participant->participantStatus == 'APPROVED') {
                    $participant->update([
                        'participantStatus' => 'UNAPPROVED',
                    ]);
                    return back()->with('success', config('Messages.200'));
                }
                $participant->update([
                    'participantStatus' => 'APPROVED',
                ]);
                return back()->with('success', config('Messages.200'));
            }
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
