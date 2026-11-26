<?php

namespace App\Http\Controllers\Stickers;

use App\Http\Controllers\Controller;
use App\Models\settings\events;
use App\Models\Stickers\StickerAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class StickerAllocationController extends Controller
{
    /**
     * Display a listing of the resource.'allocation_id','sticker_id','company_id','event_id','provided_stickers','provided_by','reasons','attachment'
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('ALLOCATE_STICKERS', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('ALLOCATE_STICKERS'));
        }
        $request->validate([
            'attachment' => 'mimes:png,jpg,jpeg,pdf'
        ]);
        session(['serviceId'=>$request->serviceId]);
        try {
            $allocations = StickerAllocation::where('company_id', session('company_id'))
                ->where('event_id', session('liveEvent'))
                ->where('sticker_id', session('serviceId'))->first();
            if ($allocations) {
                $attachment_file = '';
                if ($request->attachment != null) {
                    $attachment_file = 'Stickers_' . Str::uuid() . $request->attachment->extension();
                    $request->attachment->move(public_path('ApprovalAttachments'), $attachment_file);
                }
                $new_stickers = $allocations->provided_stickers + $request->provided_stickers;
                $allocations->update([
                    'provided_stickers' => $new_stickers,
                    'provided_by' => Auth::user()->user_uuid,
                    'reasons' => $request->reasons,
                    'attachment' => $attachment_file,
                ]);
                return back()->with('success', 'Stickers allocated successfully!');
            } else {
                $attachment_file = '';
                if ($request->attachment != null) {
                    $attachment_file = 'Stickers_' . Str::uuid() . $request->attachment->extension();
                    $request->attachment->move(public_path('ApprovalAttachments'), $attachment_file);
                }
                StickerAllocation::create([
                    'allocation_id' => Str::uuid(),
                    'sticker_id' => session('serviceId'),
                    'company_id' => session('company_id'),
                    'event_id' => session('liveEvent'),
                    'provided_stickers' => $request->provided_stickers,
                    'provided_by' => Auth::user()->user_uuid,
                    'reasons' => $request->reasons,
                    'attachment' => $attachment_file,
                ]);
                return back()->with('success', 'Stickers Allocated successfully!');
            }
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong!') . $th->getMessage();
        }
    }

    public function update(Request $request,$id){
        try {
             $allocation=StickerAllocation::find($id);
            if ($allocation) {
                $allocation->update([
                    'provided_stickers'=>$request->provided_stickers,
                ]);
                return back()->with('success',config('Messages.200'));
            }
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
        }
    }
}
