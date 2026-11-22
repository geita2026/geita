<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\space\pavilions;
use App\Models\space\booths;
use App\Models\booking\booked_pavilions;
use App\Models\companies;
use App\Models\booking\booked_booths;
use App\Models\advice_note\service_charges;
use App\Models\exhibition\exhibition_products;
use App\Models\exhibition\Product;
use App\Models\space\additional_services;
use App\Models\space\space_categories;
use App\Models\settings\events;
use App\Models\space\AreaDistribution;
use App\Traits\BookingTrait;
use App\Traits\VistorsTrait;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use App\Traits\BookingTimeController;
use App\Traits\System;
use Illuminate\Support\Facades\DB;

class bookingController extends Controller
{
    use BookingTrait;
    use VistorsTrait;
    use BookingTimeController;
    use System;
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
        if ($request->user()->cannot('VIEW_BOOKING_SUBMENU', Role::class)
            && $request->user()->cannot('BOOK_SPACE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_BOOKING_SUBMENU'));
        }
        # $this->Vistors($ip = $request->ip()); # save new vistor
        $space_categories = space_categories::orderBy('category_name', 'ASC')
            ->where('category_event', session('liveEvent'))->get();
        $event = events::find(session('liveEvent'));

        $bookingOpen = $this->checkApplicationStatus();

        return view('booking.space-category-booking', compact('space_categories', 'event', 'bookingOpen'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('VIEW_BOOKING_SPACE_CATEGORIES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_BOOKING_SPACE_CATEGORIES'));
        }
        #
        $companies = companies::get(['companyID', 'companyName']);

        $space_categories = space_categories::orderBy('category_name', 'ASC')->get();

        $spaces = pavilions::leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')->get()->where('event', $request->event_id);
        $event_id = $request->event_id;

        return view('booking.space-category-booking', compact('companies', 'spaces', 'space_categories', 'event_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        if($this->checkApplicationStatus() == false){
            return back()->with('fail','Application is currently not opened, please stay tuned for updates');
        }
       
        if ($request->user()->cannot('BOOK_SPACE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('BOOK_SPACE'));
        }
        #
       
        ############ CHECK BALANCE FROM PREVIOUS EVENT ##########
        $balanceAmount = advice_notes::where('advice_note_status', 'APPROVED')
            ->where('company_id', $request->company_name)
            ->where('event', '!=', session('liveEvent'))
            ->where('control_number', '!=', Null)->where('balance', '>', 1)->sum('total_amount');
        if ($balanceAmount > 1) {
            return back()->with('fail', 'You have a balance of ' . number_format($balanceAmount, 2) . ' From privious Event please complete it to be able to Book Space');
        }
        #--- Booking Session -----#
        $date = Carbon::create(date('Y-m-d H:i:s'))->addMinutes(1440)->format('Y-m-d H:i:s');
        session(['bookingLastTime' => $date]);
        #----- END BOOKING SESSION ----#

        ##### FOR ADDITIONAL SERVICES ####
         
        if ($request->additional_service) {
          
            $request->validate([
                'service_name' => 'required',
                'quantity' => 'required|numeric',
                'company_name' => 'required'
            ]);
            $service_charge_id = Str::uuid();
            $service_name = $request->service_name;
            $company_id = $request->company_name;
            $user_id = Auth::user()->user_uuid;
            $exhibition_year = $request->exhibition_year;
            $service_quantity = $request->quantity;
                
                # add service to service charges
                $this->AdditionalServices($service_charge_id, $service_name, $company_id, $user_id, $exhibition_year, $service_quantity);
                # end
     
                return back()->with('success', config('Messages.200'));
            
        }

        ########## END AdditionalServices #######

        ################ SPACE BOOKING ###########
        else {
            try {
               
                $request->validate([
                    'company_name' => 'required'
                ]);

                $event = session('liveEvent');
                $user_id = Auth::user()->user_uuid; # user id

                ### CHECK OTHER BOOKING IF AVAILABLE
                $exist = booked_pavilions::where('advice_note_id', Null)
                    ->where('userID', $user_id)->where('companyID', '!=', $request->company_name)->first();
                if ($exist) {
                    return redirect()->route('booking.edit', ['booking'])->with('fail', 'Their is another booking in Cart please complete it to proceed');
                }

                # this calculates booked space size
                $space_size = 0;
                $boothCounter = 0;
                foreach ($request->spaceName as $data) {
                    #----- CHECK IF ALLOWED ---#
                    $bookedSpaces = booked_pavilions::where('pavilionID', $data)
                        ->where('exhibition_year', session('liveEvent'))->first();
                    if ($bookedSpaces) {
                        return back()->with('fail', 'This space has been booked by other customer while you was processing your booking, please select other space.');
                    }
                    $pav = pavilions::where('pavilionID', $data)
                        ->where('whoCanBook', '!=', 'ANY')->first();
                    if ($pav) {
                        if ($pav->whoCanBook != $request->company_name) {
                            return back()->with('fail', 'This is a self built area can not be booked, please select other area');
                        }
                    }
                    $sector = companies::find($request->company_name);
                    if ($sector) {
                        $exists = AreaDistribution::where('spaceId', $data)
                            ->where('participants', $sector->companySector)->first();
                        if ($sector->companySector == null) {
                            return redirect()->route('company_profile', ['companyId' => $sector->registrationID])->with('fail', 'You do not have company sector, Please update your company sector to proceed!');
                        } elseif (!$exists) {
                            $booth = booths::find($data);
                            $space = pavilions::find($data);
                            if ($booth) {
                                $allowedSectors = $booth->allowedForBooking;
                            }
                            if ($space) {
                                $allowedSectors = $space->allowedForBooking;
                            }
                            return back()->with('fail', 'Your company sector is not allowed in the selected space, allowed sectors are: ' . $allowedSectors);
                        }
                    }
                    $booths = booths::leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')->where('boothID', $data)->get();


                    foreach ($booths as $value) {
                        $space_size = $space_size + $value->boothSize;
                        session(['currency' => $value->type_of_currency]);
                        $currency = session('currency');
                    }

                    $spaces = pavilions::leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
                        ->where('pavilionID', $data)->first();
                    if ($spaces) {
                        $space_size = $spaces->pavilion_size;
                        session(['currency' => $spaces->type_of_currency]);
                        $currency = session('currency');
                    }
                    $boothCounter++;
                }

                # end of calculating booked space size

                $company_id = $request->company_name; # id of the booking company

                $this->AddMandatoryServices($space_size, $currency, $company_id, $event,$boothCounter);  # this methode add all mandatory service to the booked bill

                # add booked booth to the booked booths table
                foreach ($request->spaceName as $data) {
                    # $pavilionID=booths::find($data)->pavilionID.'|'.$data; # get PavilionID
                    booked_pavilions::create([
                        'bookingID' => Str::uuid(),
                        'pavilionID' => $data,
                        'userID' => Auth::user()->user_uuid,
                        'companyID' => $company_id,
                        'exhibition_year' => $event,
                        'transferStatus'=>'UNTRANSFERED',
                    ]);
                    #
                }
                #end

                #end product exhibited
                $expiresAt = Carbon::now()->addMinutes(env('BOOKING_SESSION_TIME'));
                session(['expiresAt' => $expiresAt]); // for clearing uncomplished bookings
               
                return redirect()->route('booking.edit', ['booking'])->with('success', config('Messages.200'));
                
            } catch (\Throwable $th) {
                #throw $th;
                DB::rollback();
                return back()->with('fail', config('Messages.500') . ', Please select atleast one Stand or Booth'. $th->getMessage());
            }
        }
        ############ AND SPACE BOOKING #############


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if($this->checkApplicationStatus() == false){
            return back()->with('fail','Application is currently not opened, please stay tuned for updates');
        }
        if ($request->user()->cannot('BOOK_SPACE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('BOOK_SPACE'));
        }
        #
        session(['moduleNo' => 1]);
        if (Auth::user()->account_type == 1) {
            $companies = companies::where('status', 1)->get(['companyID', 'companyName']);
        } elseif (Auth::user()->account_type == 2) {
            $companies = companies::leftJoin('company_admins', 'companies.companyID', '=', 'company_admins.companyId')
                ->where('status', 1)->where('adminId', Auth::user()->user_uuid)
                ->get(['companies.companyID', 'companyName']);
        }
        $uid = Auth::user()->user_uuid;
        $booked_pavilion = pavilions::leftJoin('booked_pavilions', 'pavilions.pavilionID', '=', 'booked_pavilions.pavilionID')
            ->where('booked_pavilions.userID', $uid)->where('advice_note_id', Null)->count();
        $number_of_booths = booths::query()
            ->where('pavilionID', $id)
            ->where('bookingStatus', '!=', 'SUNSPENDED')
            ->count();

        $pavilion = pavilions::query()
            ->leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
            ->where('pavilions.pavilionID', $id)
            ->first([
                'pavilions.pavilionID',
                'pavilions.pavilionName',
                'pavilions.pavilionCategory',
                'pavilions.floor_plan',
                'pavilions.pavilion_size',
                'pavilions.pavilionDescription',
                'pavilions.allowedForBooking',
                'pavilions.space_status',
                'space_categories.category_name',
                'space_categories.cost_one_msq',
                'space_categories.type_of_currency',
            ]);

        if (! $pavilion) {
            return back()->with('fail', 'Pavilion not found.');
        }

        $booths = booths::query()
            ->leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
            ->leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
            ->where('booths.bookingStatus', '!=', 'SUNSPENDED')
            ->where('pavilions.space_status', 'UNRESERVED')
            ->where('booths.pavilionID', $id)
            ->orderByRaw('CASE WHEN booths.location IS NULL OR booths.location = "" THEN 1 ELSE 0 END')
            ->orderByRaw('CAST(booths.location AS UNSIGNED)')
            ->orderBy('booths.boothNo')
            ->orderBy('booths.boothID')
            ->get([
                'booths.boothID',
                'booths.pavilionID',
                'booths.boothNo',
                'booths.location as boothLocation',
                'booths.boothSize',
                'booths.bookingStatus',
                'pavilions.pavilionName',
                'booth_categories.category_name',
                'booth_categories.category_cost',
                'booth_categories.type_of_currency',
                'booth_categories.booth_category_id',
            ]);

        $boothIdsInPavilion = $booths->pluck('boothID');

        $bookedBooths = booked_pavilions::query()
            ->where('exhibition_year', session('liveEvent'))
            ->whereIn('pavilionID', $boothIdsInPavilion)
            ->pluck('pavilionID')
            ->unique()
            ->values()
            ->map(fn (string $boothId) => (object) ['boothID' => $boothId]);

        $event = events::where('event_status', 1)->first();
        $activeEvent = ['event_id' => $event->event_id, 'event_name' => $event->event_name];

        $bookedSpace = booked_pavilions::query()
            ->leftJoin('companies', 'booked_pavilions.companyID', '=', 'companies.companyID')
            ->whereIn('booked_pavilions.pavilionID', $boothIdsInPavilion)
            ->where('booked_pavilions.exhibition_year', $event->event_id)
            ->get([
                'booked_pavilions.pavilionID as boothID',
                'companies.companyName',
            ]);

        $products = Product::get(['product_name', 'product_id']);

        $bookingStatus = booked_pavilions::query()
            ->leftJoin('companies', 'booked_pavilions.companyID', '=', 'companies.companyID')
            ->leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
            ->whereIn('booked_pavilions.pavilionID', $boothIdsInPavilion)
            ->where('booked_pavilions.exhibition_year', $event->event_id)
            ->get([
                'booked_pavilions.pavilionID as boothID',
                'companies.companyName',
                'advice_notes.advice_note_id',
                'advice_notes.total_amount',
                'advice_notes.balance',
                'advice_notes.currency',
                'companies.companyID',
            ]);

        return view('booking.booth-booking', compact(
            'booths',
            'companies',
            'pavilion',
            'number_of_booths',
            'products',
            'bookedSpace',
            'activeEvent',
            'id',
            'bookedBooths',
            'bookingStatus'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if($this->checkApplicationStatus() == false){
            return back()->with('fail','Application is currently not opened, please stay tuned for updates');
        }
        if ($request->user()->cannot('BOOK_SPACE', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('BOOK_SPACE'));
        }
        #
        $booking = booked_pavilions::where('advice_note_id', Null)
            ->where('userID', Auth::user()->user_uuid)->first();
        if (!$booking) {
            session()->forget('bookingLastTime');
        }

        // return session('bookingLastTime');
        $company_id = 0;
        $company = 'Unknown'; # company by default

        $bookedBooths = booths::leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
            ->leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
            ->leftJoin('booked_pavilions', 'booths.boothID', '=', 'booked_pavilions.pavilionID')
            ->leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
            ->where('booked_pavilions.userID', Auth::user()->user_uuid)->where('advice_note_id', Null)
            ->orderBy('boothNo', 'ASC')->get();


        $event_id = events::where('event_status', 1)->first()->event_id;

        foreach ($bookedBooths as $value) {
            $company_id = $value->companyID;
            session(['currency' => $value->type_of_currency]);
        }
        foreach ($bookedBooths as $value) {
            $company_id = $value->companyID;
            session(['currency' => $value->type_of_currency]);
        }

        $booked_pavilions = pavilions::leftJoin('booked_pavilions', 'pavilions.pavilionID', '=', 'booked_pavilions.pavilionID')
            ->leftJoin('space_categories', 'pavilions.pavilionCategory', '=', 'space_categories.category_id')
            ->where('booked_pavilions.userID', Auth::user()->user_uuid)->where('advice_note_id', Null)->get();

        foreach ($booked_pavilions as $value) {
            $company_id = $value->companyID;
            session(['currency' => $value->type_of_currency]);
        }

        $companies = booked_booths::leftJoin('companies', 'booked_booths.companyID', '=', 'companies.companyID')
            ->where('booked_booths.userID', Auth::user()->user_uuid)
            ->where('advice_note_id', Null)->get();
        foreach ($companies as $key) {
            $company = $key->companyName;
            $company_id = $key->companyID;
        }
        $services = additional_services::where('service_event', $event_id)
            ->where('gfs_code', '!=', Null)->where('type_of_currency', session('currency'))
            ->where('self_booking', 1)->get(); # services must have gfs code to appear

        if (Auth::user()->account_type == 1) {
            $services = additional_services::where('service_event', $event_id)
                ->where('gfs_code', '!=', Null)->where('type_of_currency', session('currency'))->get(); # services must have gfs code to appear
        }
        $serviceCharges = service_charges::leftJoin('additional_services', 'service_charges.service_id', '=', 'additional_services.serviceID')
            ->where('user_id', Auth::user()->user_uuid)->where('advice_note_id', Null)->get();

        $space = booked_pavilions::leftJoin('companies', 'booked_pavilions.companyID', '=', 'companies.companyID')
            ->where('advice_note_id', Null)->where('userID', Auth::user()->user_uuid)->get();
        foreach ($space as $key) {
            $company = $key->companyName;
            $company_id = $key->companyID;
        }
        $differenceMins = now()->diffInMinutes(session('bookingLastTime'));
        if ($differenceMins == 0) {
            $this->BookingTimeControl();
            $request->session()->forget('bookingLastTime');
        }
        if (session('bookingLastTime') == null) {
            return redirect()->route('booking.index')->with('fail', 'No booking available in cat, please book new spaces or service');
        }

        $country = companies::where('companyID', $company_id)->first();
        session(['originarity' => $country->companyLocation]);
        return view('booking.booked-booths', compact('bookedBooths', 'services', 'serviceCharges', 'company', 'booked_pavilions', 'company_id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if($this->checkApplicationStatus() == false){
            return back()->with('fail','Application is currently not opened, please stay tuned for updates');
        }
        try {
            if ($request->user()->cannot('CANCEL_BOOKING', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('CANCEL_BOOKING'));
            }

            $space = booked_pavilions::find($id);
            if ($space) {
                $availableBookedSpace = booked_pavilions::where('companyID', $space->companyID)
                    ->where('exhibition_year', session('liveEvent'))->where('advice_note_id', Null)->count();
                if ($availableBookedSpace == 1) {
                    $service = service_charges::where('company_id', $space->companyID)
                        ->where('exhibition_year', session('liveEvent'))
                        ->where('advice_note_id', null)->delete();
                    $space->delete();
                } else {
                    $space->delete();
                }
                return back()->with('success', config('Messages.200'));
            }

            $service = service_charges::find($id);
            if ($service) {
                $service->delete();
                return back()->with('success', config('Messages.200'));
            }
        } catch (\Throwable $th) {
            #throw $th;
            return back()->with('fail', config('Messages.500'));
        }
    }



    # add additional services
    public function AdditionalServices($service_charge_id, $service_name, $company_id, $user_id, $exhibition_year, $service_quantity)
    {
        service_charges::create([
            'service_charge_id' => $service_charge_id,
            'service_id' => $service_name,
            'company_id' => $company_id,
            'user_id' => $user_id,
            'exhibition_year' => $exhibition_year,
            'service_quantity' => $service_quantity,
        ]);
    }



    # booking the whole space
    private function BookWholeSpace($company_id, $user_id, $booked_space, $event)
    {
        #add booking details to booked_pavilions table
        $booked = booked_pavilions::create([
            'bookingID' => str::uuid(),
            'userID' => $user_id,
            'companyID' => $company_id,
            'pavilionID' => $booked_space,
            'exhibition_year' => $event,
            'event' => $event,


        ]);

        if ($booked) {
            $pavilion = pavilions::find($booked_space);
            $pavilion->update([
                'bookingStatus' => 1,
            ]);
            return redirect()->route('bookedPavilions.index')->with('success', 'Pavilion Booked Successfully!');
        }
    }

    #booking wholes space


    # exhibited products
    public function RegisterExhibitedProducts($company_id, $user_id, $product_name, $event)
    {
        exhibition_products::create([
            'exhibition_product_id' => Str::uuid(),
            'exhibitor_id' => $company_id,
            'user_id' => $user_id,
            'product_list' => $product_name,
            'event' => $event,
        ]);
    }


    public function pendingBooking(Request $request)
    {
        if ($request->user()->cannot('VIEW_PENDING_BOOKING', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_PENDING_BOOKING'));
        }
        $bookedBooths = booked_pavilions::leftJoin('booths', 'booths.boothID', '=', 'booked_pavilions.pavilionID')
            ->leftJoin('pavilions', 'booked_pavilions.pavilionID', '=', 'pavilions.pavilionID')
            ->leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
            ->leftJoin('companies', 'booked_pavilions.companyID', '=', 'companies.companyID')
            ->where('advice_note_id', Null)
            ->orderBy('booked_pavilions.created_at', 'DESC')
            ->get(['bookingID', 'booths.pavilionID', 'companyName', 'companyPhone', 'pavilionName', 'boothNo', 'booked_pavilions.created_at']);

        $serviceCharges = service_charges::leftJoin('additional_services', 'service_charges.service_id', '=', 'additional_services.serviceID')
            ->leftJoin('companies', 'service_charges.company_id', '=', 'companies.companyID')
            ->where('advice_note_id', Null)
            ->orderBy('service_charges.created_at', 'DESC')
            ->get([
                'service_charge_id',
                'companyName',
                'companyPhone',
                'serviceName',
                'service_charges.created_at'
            ]);
        return view('booking.PendingBooking', compact('bookedBooths', 'serviceCharges'));
    }
}
