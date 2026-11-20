<?php
namespace App\Http\Controllers\FrontEnd\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\space\pavilions;
use App\Models\space\booths;
use App\Models\companies;
use Illuminate\Support\Facades\Auth;
use App\Models\exhibition\Product;

class ViewSpacesController extends Controller
{
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
        if($request->user()->cannot('View Customer Booking',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Customer Booking',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
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
        if($request->user()->cannot('View Customer Booking',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $spaces=pavilions::leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
        ->where('event',$request->event_id)->where('pavilionCategory',$id)->get();
        $i=0;
        foreach($spaces as $value){
            $i++;
        }
        if($i==0){
          return back()->with('fail','No registered spaces under this space category.');
        }
        return view('FrontEnd.Booking.exhibition_spaces',compact('spaces'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if($request->user()->cannot('Update Customer Booking',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $companies=companies::get(['companyID','companyName']);
        $uid=Auth::user()->user_uuid;

            $bookedBooth=booths::leftJoin('pavilions','booths.pavilionID','=','pavilions.pavilionID')
            ->leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
            ->rightJoin('booked_booths','booths.boothID','=','booked_booths.boothID')
            ->get(['booked_booths.userID','advice_note_id','advice_note_generated'])
            ->where('userID',$uid)
            ->where('advice_note_id',Null)->count();

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
        return view('FrontEnd.Booking.space_partitions',compact('booths','companies','bookedBooth','pavilionName','pavilions','number_of_booths','products'));
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
        if($request->user()->cannot('Update Customer Booking',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
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
        if($request->user()->cannot('Delete Customer Booking',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
    }
}
