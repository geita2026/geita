<?php

namespace App\Http\Controllers\TradePromotion;

use App\Http\Controllers\Controller;
use App\Models\TradePromotion\PromotionEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PromotionEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('Manage PromotionEvents', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $promotionEvents = PromotionEvent::get(['eventId', 'eventTitle', 'startFrom', 'endTo', 'eventParticipants', 'eventDescription', 'eventPoster', 'eventStatus', 'registrationDeadline','eventNumber']);
        return view('TradePromotion.PromotionEvents', compact('promotionEvents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Manage PromotionEvent', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $request->validate([
            'eventTitle' => 'required|max:200',
            'startFrom' => 'required',
            'endTo' => 'required',
            'eventParticipants' => 'required',
            'eventDescription' => 'required',
            'eventPoster' => 'mimes:png,jpg,jpeg',
            'registrationDeadline' => 'required'
        ]);
        try {
            if (!empty($request->eventPoster)) {
                $imageName = Str::uuid() . '.' . $request->eventPoster->extension();
                $request->eventPoster->move(public_path('TradePromotionFiles'), $imageName);
                $eventPoster = env('APP_URL') . '/public/TradePromotionFiles/' . $imageName;
            } else {
                $eventPoster = Null;
            }
            PromotionEvent::create([
                'eventId' => Str::uuid(),
                'eventNumber' => strtolower(strtoupper(Str::random(5) . date('y'))),
                'eventTitle' => $request->eventTitle,
                'startFrom' => $request->startFrom,
                'endTo' => $request->endTo,
                'eventParticipants' => $request->eventParticipants,
                'eventDescription' => $request->eventDescription,
                'eventStatus' => $request->eventStatus,
                'eventPoster' => $eventPoster,
                'registrationDeadline' => $request->registrationDeadline
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
        if ($request->user()->cannot('Manage PromotionEvents', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $request->validate([
            'eventTitle' => 'required',
            'startFrom' => 'required',
            'endTo' => 'required',
            'eventParticipants' => 'required',
            'eventDescription' => 'required',
        ]);

        try {
            $event = PromotionEvent::find($id);
            if (!empty($request->eventPoster)) {
                #### DELETE OLD IMAGE ######
                $imageUrl = $event->eventPoster;
                if ($imageUrl != null) {
                    $imageName = str_replace(env('APP_URL') . '/public/TradePromotionFiles/', '', $imageUrl);
                    File::delete(public_path('TradePromotionFiles/') . $imageName);
                }
                ############## DELETE OLD IMAGE ###
                $imageName = Str::uuid() . '.' . $request->eventPoster->extension();
                $request->eventPoster->move(public_path('TradePromotionFiles'), $imageName);
                $eventPoster = env('APP_URL') . '/public/TradePromotionFiles/' . $imageName;
            } else {
                $eventPoster = $event->eventPoster;
            }
            $event->update([
                'eventTitle' => $request->eventTitle,
                'startFrom' => $request->startFrom,
                'endTo' => $request->endTo,
                'eventParticipants' => $request->eventParticipants,
                'eventDescription' => $request->eventDescription,
                'eventStatus' => $request->eventStatus,
                'eventPoster' => $eventPoster,
                'registrationDeadline' => $request->registrationDeadline
            ]);
            return back()->with('success', config('Messages.200'));
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
        if ($request->user()->cannot('Delete PromotionEvents', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        try {
            $event = PromotionEvent::find($id);
            $imageUrl = $event->eventPoster;
            if ($imageUrl != null) {
                $imageName = str_replace(env('APP_URL') . '/public/TradePromotionFiles/', '', $imageUrl);
                File::delete(public_path('TradePromotionFiles/') . $imageName);
            }
            $event->delete();
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
