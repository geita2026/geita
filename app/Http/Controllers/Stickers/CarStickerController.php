<?php

namespace App\Http\Controllers\Stickers;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\companies;
use App\Models\ExhibitionBadges\BadgeGroup;
use App\Models\space\additional_services;
use App\Models\Stickers\CarSticker;
use App\Models\Stickers\StickerAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CarStickerController extends Controller
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
        if ($request->user()->cannot('VIEW_STICKER_SUBMENU', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_STICKER_SUBMENU'));
        }
        #************* INTERNAL USERS **************#
        if (Auth::user()->account_type == 1) {
            $companies = companies::limit(20)->paginate(25);
            $total_companies = companies::count();
        }
        if (Auth::user()->account_type == 2) {
            $total_companies = companies::count();
            $companies = companies::orderBy('registrationDate', 'DESC')
                ->leftJoin('company_admins', 'companies.companyID', '=', 'company_admins.companyId')
                ->where('adminId', Auth::user()->user_uuid)
                ->where('status', 1)->paginate(config('global.DATA_LIMIT'), ['companies.companyID', 'companyName', 'companyEmail', 'companyPhone', 'registrationID']);
        }
        return view('Stickers.CompanyList', compact('companies', 'total_companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('CREATE_STICKER', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('CREATE_STICKER'));
        }
        //
        $stickers = CarSticker::leftJoin('companies', 'car_stickers.company_id', '=', 'companies.companyID')
            ->leftJoin('additional_services', 'car_stickers.reference_sticker', '=', 'additional_services.serviceID')
            ->leftJoin('events', 'car_stickers.event_id', '=', 'events.event_id')
            ->where('event_status', 1)->get();

        $printedStickers = CarSticker::where('sticker_status', 1)->count();
        $UnprintedStickers = CarSticker::where('sticker_status', 0)->count();

        $companies = companies::leftJoin('exhibitors', 'companies.companyID', '=', 'exhibitors.exhibitor_id')
            ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
            ->where('event_status', 1)->get();

        return view('Stickers.PrepareStickers', compact('stickers', 'companies', 'printedStickers', 'UnprintedStickers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('CREATE_STICKER', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('CREATE_STICKER'));
        }
        //

        $validator = Validator::make(
            $request->all(),
            [
                'plate_number' => 'required',
                'design_group' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            try {
                CarSticker::create([
                    'sticker_id' => Str::uuid(),
                    'company_id' => $request->company_id,
                    'event_id' => session('liveEvent'),
                    'reference_sticker' => session('serviceId'),
                    'plate_number' => str_replace(' ', '', $request->plate_number),
                    'imegency_plate_number' => $request->imegency_plate_number,
                    'design_group' => $request->design_group,
                    'printed_by' => Auth::user()->user_uuid,
                    'backupStatus' => 0,
                ]);
                return response()->json([
                    'status' => 1,
                    'success' => 'Sticker generated successfully!'
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 2,
                    'fail' => 'Something went wrong!' . $th->getMessage()
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stickers\CarSticker  $carSticker
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('VIEW_STICKER_SUBMENU', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('VIEW_STICKER_SUBMENU'));
            }
            if (session('serviceId') == null && !$request->serviceId) {
                session(['serviceId' => additional_services::where('is_printable', 1)->where('service_event', session('liveEvent'))->first()->serviceID]);
            }

            if ($request->serviceId) {
                session(['serviceId' => $request->serviceId]);
            }

            session(['company_id' => $id]);
            $allowed = companies::where('companyName', 'LIKE', '%Tanzania Trade Development Authority%')
                ->where('status', 1)->where('companyID', $id)->count();

            $stickers = CarSticker::leftJoin('additional_services', 'car_stickers.reference_sticker', '=', 'additional_services.serviceID')
                ->leftJoin('badge_groups', 'car_stickers.design_group', '=', 'badge_groups.group_id')
                ->where('reference_sticker', session('serviceId'))
                ->where('company_id', $id)->where('event_id', session('liveEvent'))->get();

            $stickerName = additional_services::find(session('serviceId'))->serviceName;
            session(['serviceName' => $stickerName]);

            $TotalStickes = advice_notes::leftJoin('particulars', 'advice_notes.advice_note_id', '=', 'particulars.advice_note_id')
                ->where('company_id', $id)->where('particular_name', session('serviceName'))
                ->where('event', session('liveEvent'))->where('advice_note_status', 'APPROVED')->sum('particular_quantity');

            $UsedStickes = CarSticker::where('company_id', $id)
                ->where('event_id', session('liveEvent'))
                ->where('reference_sticker', session('serviceId'))->count();

            $accountBalance = advice_notes::where('event', session('liveEvent'))->where('company_id', $id)
                ->where('balance', '>', 1)->where('advice_note_status', 'APPROVED')->sum('balance');

            $freeStickers = StickerAllocation::where('event_id', session('liveEvent'))->where('sticker_id', session('serviceId'))
                ->where('company_id', session('company_id'))->sum('provided_stickers');

            $invoices = advice_notes::where('event', session('liveEvent'))->where('company_id', session('company_id'))->where('balance', '>', 0)
                ->where('advice_note_status', 'APPROVED')->get(['advice_note_id', 'balance', 'currency']);

            $groups = BadgeGroup::where('group_name', 'Car Stickers')->get();
            $companyName = companies::find(session('company_id'));

            $services = additional_services::where('service_event', session('liveEvent'))
                ->where('is_printable', 1)->get();

            $adviceNote = advice_notes::where('company_id', $id)
                ->where('advice_note_description', '!=', null)
                ->where('advice_note_status', '!=', 'CANCELED')
                ->where('event', session('liveEvent'))
                ->get([
                    'advice_note_description',
                    'advice_note_id'
                ]);
            $stickerAllocation = StickerAllocation::where('company_id', $id)
                ->where('event_id', session('liveEvent'))
                ->where('sticker_id', session('serviceId'))
                ->first();

            if (!$stickerAllocation) {
                $stickerId = Str::uuid();
                StickerAllocation::create([
                    'allocation_id' => $stickerId,
                    'sticker_id' => session('serviceId'),
                    'company_id' => $id,
                    'event_id' => session('liveEvent'),
                    'provided_stickers' => 0,
                    'provided_by' => Auth::user()->user_uuid,
                    'reasons' => 'Sponsor',
                ]);
                session(['allocationId' => $stickerId]);
            }
            else{
                session(['allocationId'=>$stickerAllocation->allocation_id]);
            }

            $service = additional_services::where('serviceID', session('serviceId'))->first();

            return view('Stickers.GeneratedStickers', compact(
                'stickers',
                'groups',
                'companyName',
                'TotalStickes',
                'UsedStickes',
                'service',
                'accountBalance',
                'freeStickers',
                'invoices',
                'services',
                'allowed',
                'adviceNote',
                'stickerAllocation'
            ));
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong! ' . $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stickers\CarSticker  $carSticker
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->user()->cannot('UPDATE_STICKER', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('UPDATE_STICKER'));
        }
        #************* INTERNAL USERS **************#
        if (Auth::user()->account_type == 1) {
            session(['service_id' => $id]);
            $event = additional_services::find($id)->service_event;
            session(['event_id' => $event]);
            $companies = companies::limit(20)->paginate(25);
            $total_companies = companies::count();
            $stickerName = additional_services::find($id)->serviceName;
            session(['serviceName' => $stickerName]);
            return view('Stickers.CompanyList', compact('companies', 'stickerName', 'total_companies'));
        }
        #************ END INTERNAL USERS ***********#

        #**************EXTERNAL USERS (CUSTOMERS)*****************#
        if (Auth::user()->account_type == 2) {
            session(['service_id' => $id]); // SERVICE ID
            $event = additional_services::find($id)->service_event;
            session(['event_id' => $event]); // Event ID

            $stickerName = additional_services::find($id)->serviceName;
            session(['serviceName' => $stickerName]); // Service Name
            session(['company_id' => Auth::user()->user_uuid]); // Company ID
            $company_id = session('company_id');

            $stickers = CarSticker::leftJoin('events', 'car_stickers.event_id', '=', 'events.event_id')
                ->leftJoin('additional_services', 'car_stickers.reference_sticker', '=', 'additional_services.serviceID')
                ->leftJoin('badge_groups', 'car_stickers.design_group', '=', 'badge_groups.group_id')
                ->where('reference_sticker', $id)
                ->where('company_id', $company_id)->where('event_status', 1)->get();

            $TotalStickes = advice_notes::leftJoin('particulars', 'advice_notes.advice_note_id', '=', 'particulars.advice_note_id')
                ->leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->where('company_id', $company_id)->where('particular_name', session('serviceName'))
                ->where('event_status', 1)->where('advice_note_status', '!=', 'CANCELED')->sum('particular_quantity');

            $UsedStickes = CarSticker::where('company_id', $company_id)
                ->leftJoin('events', 'car_stickers.event_id', '=', 'events.event_id')
                ->where('reference_sticker', $id)
                ->where('event_status', 1)->count();

            $accountBalance = advice_notes::leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->where('event_status', 1)->where('company_id', $company_id)->where('balance', '>', 0)
                ->where('advice_note_status', '!=', 'CANCELED')->count();
            $freeStickers = StickerAllocation::leftJoin('events', 'sticker_allocations.event_id', '=', 'events.event_id')
                ->where('sticker_id', $id)
                ->where('company_id', session('company_id'))->first();
            $groups = BadgeGroup::where('group_name', 'Car Stickers')->get();
            $companyName = companies::find(session('company_id'));

            $invoices = advice_notes::leftJoin('events', 'advice_notes.event', '=', 'events.event_id')
                ->where('event_status', 1)->where('company_id', $company_id)->where('balance', '>', 0)
                ->where('advice_note_status', '!=', 'CANCELED')->get(['advice_note_id', 'balance', 'currency']);
            $serviceName = additional_services::where('serviceID', $id)->first()->serviceName;
            return view('Stickers.GeneratedStickers', compact('stickers', 'groups', 'companyName', 'TotalStickes', 'UsedStickes', 'accountBalance', 'freeStickers', 'invoices', 'serviceName'));
        }
        #******** END EXTERNAL USERS *****************#

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stickers\CarSticker  $carSticker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('UPDATE_STICKER', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('UPDATE_STICKER'));
        }

        try {

            # --- BLOCK STICKER---#
            if ($request->source == 1) {
                CarSticker::find($id)->update([
                    'sticker_status' => 2,
                    'reprented_by' => Auth::user()->user_uuid
                ]);
                return back()->with('success', 'Sticker Blocked successfully!');
            }
            # ---- END BLOCK STICKER --#

            # --- UNBLOCK STICKER---#
            if ($request->source == 2) {
                CarSticker::find($id)->update([
                    'sticker_status' => 1,
                    'reprented_by' => Auth::user()->user_uuid
                ]);
                return back()->with('success', 'Sticker UnBlocked successfully!');
            }
            # ---- END UNBLOCK STICKER --#

            CarSticker::find($id)->update([
                'plate_number' => str_replace(' ', '', $request->plate_number),
                'imegency_plate_number' => $request->imegency_plate_number,
                'design_group' => $request->design_group,
                'printed_by' => Auth::user()->user_uuid,
                'sticker_status' => 0,
                'backupStatus' => 0,
            ]);
            return back()->with('success', 'Sticker Updated successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stickers\CarSticker  $carSticker
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('DELETE_STICKER', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('DELETE_STICKER'));
        }

        try {
            CarSticker::find($id)->delete();
            return back()->with('success', 'Sticker Deleted successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'something went wrong!');
        }
    }
}
