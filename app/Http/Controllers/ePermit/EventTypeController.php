<?php

namespace App\Http\Controllers\ePermit;

use App\Http\Controllers\Controller;
use App\Models\ePermit\PromotionEventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Throwable;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class EventTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->user()->cannot('MANAGE_PERMIT_EVENT_TYPE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PERMIT_EVENT_TYPE'));
            }
            $eventTypes = PromotionEventType::get();
            return view('ePermit.eventTypes.eventTypeList', compact('eventTypes'));
        } catch (Throwable $e) {
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
            if ($request->user()->cannot('MANAGE_PERMIT_EVENT_TYPE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PERMIT_EVENT_TYPE'));
            }
            return view('ePermit.eventTypes.CreateEventType');
        } catch (Throwable $e) {
            return back()->with('fail', config('Messages.500'));
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
            if ($request->user()->cannot('MANAGE_PERMIT_EVENT_TYPE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PERMIT_EVENT_TYPE'));
            }
            $validator = Validator::make($request->all(), [
                'promotionEventTypeName' => 'required',
                'promotionEventTypeDescription' => 'required',
                'eventTypePoster' => 'required|mimes:png,jpg,jpeg',
                'eventTypeAmountUsd' => 'required',
                'eventTypeAmountTzs' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            $fileName = str_replace(' ', '', $request->promotionEventTypeName) . date('Y') . '.' . $request->eventTypePoster->extension();
            $path = public_path('ePermitFiles/uploadedFiles/');
            $image = Image::make($request->eventTypePoster)->resize(200, 200);
            $image->save($path . $fileName);
            $eventTypePoster = env('APP_URL') . '/public/ePermitFiles/uploadedFiles/' . $fileName;
            PromotionEventType::create([
                'eventTypeId' => Str::uuid(),
                'promotionEventTypeName' => $request->promotionEventTypeName,
                'promotionEventTypeDescription' => $request->promotionEventTypeDescription,
                'eventTypePoster' => $eventTypePoster,
                'eventTypeAmountUsd' => $request->eventTypeAmountUsd,
                'eventTypeAmountTzs' => $request->eventTypeAmountTzs
            ]);
            return redirect()->route('event_types.index')->with('success', config('Messages.200'));
        } catch (Throwable $e) {
            return back()->with('fail', config('Messages.500') . $e->getMessage());
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
        if ($request->user()->cannot('MANAGE_PERMIT_EVENT_TYPE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PERMIT_EVENT_TYPE'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('MANAGE_PERMIT_EVENT_TYPE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PERMIT_EVENT_TYPE'));
            }
            $eventType = PromotionEventType::where('eventTypeId', $id)->get();
            return view('ePermit.eventTypes.EditEventType', compact('eventType'));
        } catch (Throwable $e) {
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
        try {
            if ($request->user()->cannot('MANAGE_PERMIT_EVENT_TYPE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PERMIT_EVENT_TYPE'));
            }
            $validator = Validator::make($request->all(), [
                'promotionEventTypeName' => 'required',
                'promotionEventTypeDescription' => 'required',
                'eventTypeAmountUsd' => 'required',
                'eventTypeAmountTzs' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            $eventType = PromotionEventType::find($id);
            $eventTypePoster = $eventType->eventTypePoster;
            if ($request->eventTypePoster) {
                $validator = Validator::make($request->all(), [
                    'eventTypePoster' => 'required|mimes:png,jpg,jpeg'
                ]);
                if ($validator->fails()) {
                    return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
                }
                $fileName = str_replace(env('APP_URL').'ePermitFiles/uploadedFiles/', '', $eventTypePoster);
                File::delete(public_path($fileName));
                $fileName = str_replace(' ', '', $request->promotionEventTypeName) . date('Y') . '.' . $request->eventTypePoster->extension();
                $image = Image::make($request->eventTypePoster)->resize(500, 200);
                $path = public_path('ePermitFiles/uploadedFiles/');
                $image->save($path . $fileName);
                $eventTypePoster = env('APP_URL') . '/public/ePermitFiles/uploadedFiles/' . $fileName;
            }
            $eventType->update([
                'promotionEventTypeName' => $request->promotionEventTypeName,
                'promotionEventTypeDescription' => $request->promotionEventTypeDescription,
                'eventTypePoster' => $eventTypePoster,
                'eventTypeAmountUsd' => $request->eventTypeAmountUsd,
                'eventTypeAmountTzs' => $request->eventTypeAmountTzs
            ]);
            return redirect()->route('event_types.index')->with('success', config('Messages.200'));
        } catch (Throwable $e) {
            return back()->with('fail', config('Messages.500') . $e->getMessage());
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
        try {
            if ($request->user()->cannot('MANAGE_PERMIT_EVENT_TYPE', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PERMIT_EVENT_TYPE'));
            }
            $eventType = PromotionEventType::find($id);
            $eventTypePoster = $eventType->eventTypePoster;
            $fileName = str_replace(env('APP_URL'), '', $eventTypePoster);
            File::delete(public_path($fileName));
            $eventType->delete();
            return redirect()->route('event_types.index')->with('success', config('Messages.200'));
        } catch (Throwable $e) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
