<?php

namespace App\Http\Controllers\FrontEnd\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\space\pavilions;
use App\Models\space\booths;
use App\Models\companies;
use Illuminate\Support\Facades\Auth;
use App\Models\exhibition\Product;
use App\Models\booking\booked_booths;
use App\Models\advice_note\service_charges;
use Illuminate\Support\Str;
use App\Models\exhibition\exhibition_products;
use App\Models\space\additional_services;
use App\Models\booking\booked_pavilions;
use App\Traits\BookingTrait;
use Spatie\Permission\Models\Role;

class CustomerBookingController extends Controller
{
   use BookingTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if($request->user()->cannot('BOOK_SPACE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('BOOK_SPACE'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->cannot('BOOK_SPACE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('BOOK_SPACE'));
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
        if($request->user()->cannot('BOOK_SPACE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('BOOK_SPACE'));
        }

        $event=session('event_id'); // get event selected
        $user_id=Auth::user()->user_uuid; // user id


        if($request->booth)
        {
            $request->validate(['product'=>'required','booth_name'=>'required']); // validates if products checked

        try {
              // this calculates booked space size
        $space_size=0;
        foreach ($request->booth_name as $data) {
           $booths=booths::leftJoin('booth_categories','booths.booth_category','=','booth_categories.booth_category_id')->where('boothID',$data)->get();
            foreach ($booths as $value) {  $space_size=$space_size+$value->boothSize;
                session(['currency'=>$value->type_of_currency]);
                $currency=session('currency');
            }
        }
        // end of calculating booked space size

        $company_id=Auth::user()->user_uuid; // user uuid is the same as companyID
        $this->AddMandatoryServices($space_size,$currency,$company_id);  // this methode add all mandatory service to the booked bill

        // add booked booth to the booked booths table
            foreach ($request->booth_name as $data) {
             booked_booths::create([
                 'bookingID'=>Str::uuid(),
                 'boothID'=>$data,
                 'userID'=>Auth::user()->user_uuid,
                 'companyID'=>Auth::user()->user_uuid, // user uuid is the same as companyID
                 'waiting_time'=>$request->waiting_time,
                 'exhibition_year'=>$event,
             ]);
            //
            booths::find($data)->update(['bookingStatus'=>1]);// update booking status, this differentiate booked and unbooked booths
            }
            //end


            //insert exhibition products to exhibition product table
            foreach ($request->product as $product_name) {
             $this->RegisterExhibitedProducts($company_id,$user_id,$product_name,$event);
         }
         //end product exhibited
         return back()->with('success','Booth booked Successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong, please contact system adminstrator for your concern'.$th->getMessage());
        }
         // end booking booths.

        }




         // booking whole space (pavilions)
         if($request->space=='pavilion'){
            $company_id=Auth::user()->user_uuid; $booked_space=$request->booked_space;

            $request->validate(['product'=>'required','booked_space'=>'required']);
             try {
                // check if their is another booking in progress
             $booked_pavilions=booked_pavilions::leftJoin('pavilions','booked_pavilions.pavilionID','=','pavilions.pavilionID')
             ->leftJOin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
             ->where('advice_note_id',Null)
             ->where('booked_pavilions.userID',$user_id)->get(['booked_pavilions.userID','advice_note_id','companyID']);
             foreach($booked_pavilions as $value)
             {
                if($value->companyID!=$company_id)
                {
                    return redirect()->route('bookedPavilions.index')->with('fail','There is Booking for onother company in queue, To proceed complete this booking first.');
                }
            }
            // end of checking

            $space=pavilions::leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
            ->where('pavilionID',$booked_space)->get();

            foreach ($space as $key) {
               $space_size=$key->pavilion_size; // get booked space size
               session(['currency'=>$key->type_of_currency]);
               $currency=session('currency');
            }
            $this->AddMandatoryServices($space_size,$currency,$company_id);
            // for whole space (pavilion)
        $this->BookWholeSpace($company_id,$user_id,$booked_space,$event);

         //insert exhibition products to exhibition product table
        foreach ($request->product as $product_name) {
        $this->RegisterExhibitedProducts($company_id,$user_id,$product_name,$event);
        }
       //end product exhibited
        // whole pavilion booking

        return back()->with('success','Space booked successfully!');
             } catch (\Throwable $th) {
               return back()->with('fail','something went wrong!');
             }
        }
        // end pavilion booking



         //adding additional service
         if($request->additional_service)
         {
             $service_charge_id=Str::uuid(); $service_name=$request->service_name; $company_id=$request->company_id;
             $user_id=Auth::user()->user_uuid; $exhibition_year=$request->exhibition_year; $advice_note_geerated='No';
             $service_quantity=$request->quantity;

            $exists=additional_services::where('serviceID',$service_name)
            ->where('type_of_currency',session('currency'))->get();
            $i=0;
            foreach ($exists as $value) {
                $i++;
            }
            if($i==0)
            {
                return back()->with('fail','Only '.session('currency').' Payments are received to this booking');
            }


             $request->validate([
                 'service_name'=>'required',
                 'quantity'=>'required|numeric',
                 'company_id'=>'required'
             ]);

             try {
             // add service to service charges
            $this->AdditionalServices($service_charge_id,$service_name,$company_id,$user_id,$exhibition_year,$advice_note_geerated,$service_quantity);
            // end
            return back()->with('success','Service added successfully!');

             } catch (\Throwable $th) {
                //throw $th;
                return back()->with('fail','Sothing went wrong!'.$th->getMessage());
             }
         }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if($request->user()->cannot('BOOK_SPACE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('BOOK_SPACE'));
        }
        //
        $companies=companies::get(['companyID','companyName']);
        $uid=Auth::user()->user_uuid;

            $bookedBooth=booths::leftJoin('pavilions','booths.pavilionID','=','pavilions.pavilionID')
            ->leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
            ->rightJoin('booked_booths','booths.boothID','=','booked_booths.boothID')
            ->get(['booked_booths.userID','advice_note_id'])
            ->where('userID',$uid)
            ->where('advice_note_id',Null)->count();

            $booked_pavilion=pavilions::leftJoin('booked_pavilions','pavilions.pavilionID','=','booked_pavilions.pavilionID')
            ->where('booked_pavilions.userID',$uid)->where('advice_note_id',Null)->count();

            $number_of_booked_space=$bookedBooth+$booked_pavilion;

        $booths=booths::leftJoin('pavilions','booths.pavilionID','=','pavilions.pavilionID')
        ->leftJoin('booth_categories','booths.booth_category','=','booth_categories.booth_category_id')
        ->leftJoin('booked_booths','booths.boothID','=','booked_booths.boothID')
        ->leftJoin('companies','booked_booths.companyID','=','companies.companyID')
        ->orderBY('boothNo','ASC')
        ->where('pavilions.pavilionID',$id)
        ->get(['pavilions.pavilionID','booths.boothID','pavilionName','boothNo','category_name','boothSize','booths.bookingStatus',
        'floor_plan','category_name','category_cost','type_of_currency','booth_category_id','companyName','booking_status','event']);

        $number_of_booths=booths::leftJoin('pavilions','booths.pavilionID','=','pavilions.pavilionID')->where('pavilions.pavilionID',$id)->count();

        $pavilions=pavilions::leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
        ->leftJoin('booked_pavilions','pavilions.pavilionID','=','booked_pavilions.pavilionID')
        ->leftJoin('companies','booked_pavilions.companyID','=','companies.companyID')
        ->get(['pavilions.pavilionID','pavilionName','pavilionCategory','event','category_name','pavilion_size','cost_one_msq',
        'type_of_currency','bookingStatus','companyName','advice_note_id','booking_status','booked_pavilions.created_at'])
        ->where('pavilionID',$id)
        ->where('bookingStatus','!=','Canceled');
        foreach($pavilions as $item)
        {
            $pavilionName=$item->pavilionName;
        }

        $products=Product::get(['product_name','product_id']);
        return view('FrontEnd.Booking.space_partitions',compact('booths','companies','number_of_booked_space','pavilionName','pavilions','number_of_booths','products'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if($request->user()->cannot('BOOK_SPACE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('BOOK_SPACE'));
        }

            $bookedBooths=booths::leftJoin('pavilions','booths.pavilionID','=','pavilions.pavilionID')
            ->leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
            ->rightJoin('booked_booths','booths.boothID','=','booked_booths.boothID')
            ->leftJoin('booth_categories','booths.booth_category','=','booth_categories.booth_category_id')
            ->get()
            ->where('userID',Auth::user()->user_uuid)
            ->where('advice_note_id',Null);

            $company='Unknown'; // company by defalt
            $companies=booked_booths::leftJoin('companies','booked_booths.companyID','=','companies.companyID')
            ->where('booked_booths.userID',Auth::user()->user_uuid)
            ->where('advice_note_id',Null)->get();
            foreach ($companies as $key) { $company=$key->companyName; } // return company Name

            $event_id=0; // by default Id
            foreach ($bookedBooths as $value) { $event_id=$value->event; } // return event of booked space

            $services=additional_services::where('service_event',$event_id)
            ->where('type_of_currency',session('currency'))
            ->get();

            $booked_pavilions=pavilions::leftJoin('booked_pavilions','pavilions.pavilionID','=','booked_pavilions.pavilionID')
            ->leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
            ->where('booked_pavilions.userID',Auth::user()->user_uuid)->where('advice_note_id',Null)->get();

            $serviceCharges=service_charges::leftJoin('additional_services','service_charges.service_id','=','additional_services.serviceID')
            ->where('user_id',Auth::user()->user_uuid)
            ->where('advice_note_id',Null)
            ->get();



            return view('FrontEnd.Booking.booked_booths',compact('bookedBooths','services','serviceCharges','company','booked_pavilions'));
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
        if($request->user()->cannot('BOOK_SPACE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('BOOK_SPACE'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('BOOK_SPACE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('BOOK_SPACE'));
        }

        try {
            if($request->pavilionID)
        {
            $booked=booked_pavilions::find($request->bookingID);
            $pavilion=pavilions::find($request->pavilionID);
            $booked->delete();
            $pavilion->update([
                'bookingStatus'=>0,
            ]);
            return back()->with('success','Pavilion removed from booking!');

        }

        elseif($request->booth)
        {
            $booked=booked_booths::find($id);
            $booth=booths::find($request->boothID);
            $booked->delete();
            $booth->update([
                'bookingStatus'=>0,
            ]);
            return back()->with('success','Booth Booking canceled Successfully!');
        }

        elseif($request->service_charge_id)
        {
            $booked=service_charges::find($id);
            $booked->delete();
            return back()->with('success','Services canceled Successfully!');
        }

        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong!');
        }
    }


    // adding services
    public function AdditionalServices($service_charge_id,$service_name,$company_id,$user_id,$exhibition_year,$advice_note_geerated,$service_quantity){
        service_charges::create([
            'service_charge_id'=>$service_charge_id,
            'service_id'=>$service_name,
            'company_id'=>$company_id,
            'user_id'=>$user_id,
            'exhibition_year'=>$exhibition_year,
            'advice_note_generated'=>$advice_note_geerated,
            'service_quantity'=>$service_quantity,
        ]);

    }
    // services



   // booking the whole space
   private function BookWholeSpace($company_id,$user_id,$booked_space,$event){
    //add booking details to booked_pavilions table
    $booked=booked_pavilions::create([
        'bookingID'=>str::uuid(),
        'userID'=>$user_id,
        'companyID'=>$company_id,
        'pavilionID'=>$booked_space,
        'exhibition_year'=>$event,
        'event'=>$event,


    ]);

    if($booked)
    {
        $pavilion=pavilions::find($booked_space);
        $pavilion->update([
            'bookingStatus'=>1,
        ]);
        return redirect()->route('bookedPavilions.index')->with('success','Pavilion Booked Successfully!');
    }

}

    //booking wholes space


// exhibited products
    public function RegisterExhibitedProducts($company_id,$user_id,$product_name,$event){
        exhibition_products::create([
            'exhibition_product_id'=>Str::uuid(),
            'exhibitor_id'=>$company_id,
            'user_id'=>$user_id,
            'product_list'=>$product_name,
            'event'=>$event,
        ]);
    }
}
