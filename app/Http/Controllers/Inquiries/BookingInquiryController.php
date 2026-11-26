<?php

namespace App\Http\Controllers\Inquiries;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Inquiry\BookingInquiry;
use App\Models\setting\Country;
use App\Models\settings\events;
use App\Models\space\booths;
use App\Models\space\pavilions;
use App\Models\space\space_categories;
use Google\Service\Dfareporting\Resource\Countries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Traits\IntergrationTraits;
use App\Traits\VistorsTrait;

class BookingInquiryController extends Controller
{
    use IntergrationTraits;
    use VistorsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $spaceCategories = space_categories::orderBy('category_name', 'ASC')
            ->leftJoin('events', 'space_categories.category_event', '=', 'events.event_id')
            ->where('event_status', 1)->get([
                'category_id','category_name',
                'cost_one_msq','type_of_currency',
                'category_icon','event_name',
                'category_descriptions'
            ]);
        return view('FrontEnd.Public_Pages.OnFairBookings.ExhibitionSpaceCategories', compact('spaceCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('VIEW_BOOKING_INQUIRIES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_BOOKING_INQUIRIES'));
        }
        $eventId = events::where('event_status', 1)->first()->event_id;
        $inquiries = BookingInquiry::orderBy('inquiryStatus', 'ASC')->orderBy('registeredAt', 'ASC')->where('eventId', $eventId)->paginate(10);

        return view('Inquiries.InquiriesList', compact('inquiries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return back()->with('fail','Booking is now closed!');
        $this->Vistors($ip = $request->ip()); // save new vistor
        $request->validate([
            'companyName' => 'required|unique:booking_inquiries,companyName',
            'spaceCategory' => 'required',
            'companyEmail' => 'required|unique:booking_inquiries,companyEmail',
            'companyPhone' => 'required',
            'spaceCategory' => 'required',
            'ResindenceCountry' => 'required',
            'productsToBeExhibited' => 'required',
            'registrationCertificate' => 'required|file|mimes:pdf,png,jpg,jpeg',
            'companyProfile' => 'required|file|mimes:pdf,png,jpg,jpeg',
        ]);

        try {
            $spaceCategories = implode(',', $request->spaceCategory);
            $eventId = events::where('event_status', 1)->first()->event_id;
            $enquiryNo = 'E' . date('y-dm') . '-' . random_int(1000, 9999);
            $CprofileFile = Str::uuid() . '.' . $request->companyProfile->extension();
            $CRegCertifcateFile = Str::uuid() . '.' . $request->registrationCertificate->extension();

            BookingInquiry::create([
                'enquiryId' => Str::uuid(),
                'enquiryNo' => $enquiryNo,
                'companyName' => $request->companyName,
                'companyEmail' => $request->companyEmail,
                'companyPhone' => $request->companyPhone,
                'products' => $request->productsToBeExhibited,
                'spaceCategory' => $spaceCategories,
                'country' => $request->ResindenceCountry,
                'inquiryStatus' => 0,
                'experienceStatus' => $request->experienceStatus,
                'eventId' => $eventId,
                'registrationCertificate' => $CRegCertifcateFile,
                'companyProfile' => $CprofileFile,
                'moreDescription' => $request->moreDescription,
            ]);
            $request->registrationCertificate->move(public_path('OnlineBookingFiles'), $CRegCertifcateFile);
            $request->companyProfile->move(public_path('OnlineBookingFiles'), $CprofileFile);

            // # ******************** EMAIL VERIFICATION ************ #
                $email_data=[
                    'fromName'=>config('app.name'),
                    'email'=>$request->companyEmail,
                    'name'=>$request->companyName,
                    'requestNo'=>$enquiryNo,
                    'subject'=>'CUSTOMER REQUEST NUMBER',
                ];
                Mail::send('mails.SpaceRequestMail', $email_data, function ($message) use ($email_data) {
                    $message->to($email_data['email'],$email_data['name'])
                    ->bcc('rkabandwa@gmail.com')
                    ->subject($email_data['subject'])
                    ->priority(1);
            });
            # ***************** END EMAIL VERIFICATION ************* #

            return back()->with('success', 'Submitted successfully!');
        } catch (\Throwable $th) {
            return redirect()->route('space_booking_inquiries.index')->with('fail', 'Something went wrong...!') . $th->getMessage();
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
        
        if ($request->user()->cannot('VIEW_BOOKING_INQUIRIES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_BOOKING_INQUIRIES'));
        }
        $inquiries = BookingInquiry::where('enquiryId', $id)->get();
        return view('Inquiries.PreviewEnquiry', compact('inquiries'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return back()->with('fail','Booking is now closed!');
        $spaceCategories = space_categories::get();
        $country = Country::get();
        $pavilions = pavilions::orderBy('pavilionName', 'ASC')
            ->where('space_status', 1)->get();

        $booths = booths::orderBy('boothNo')->where('booths.pavilionID', $id)
            ->leftJoin('booked_pavilions', 'booths.boothID', '=', 'booked_pavilions.pavilionID')
            ->leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
            ->where('booked_pavilions.pavilionID', Null)->get();

        $exists = booths::where('booths.pavilionID', $id)->exists();
        $pavilion = pavilions::find($id);

        $pavilions = pavilions::orderBy('pavilionName', 'ASC')
        ->where('event', $event)->where('pavilionCategory', $categoryId)
        ->where('space_status', 1)->get();
        return view('Inquiries.onlineBooking', compact('country', 'pavilions', 'booths', 'spaceCategories', 'pavilion', 'exists'));
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
        return back()->with('fail','Booking is now closed!');
        if ($request->user()->cannot('VIEW_BOOKING_INQUIRIES', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_BOOKING_INQUIRIES'));
        }

        try {
            $spaceRequest = BookingInquiry::find($id);
            $spaceRequest->update([
                'equiryFeedBack' => $request->equiryFeedBack,
                'inquiryStatus' => $request->inquiryStatus,
            ]);
            # ******************** FEEDBACK ************
            $exists=companies::where('companyName',$spaceRequest->companyName)->exists();
            $companyId=Str::uuid();
            $reg_id = 'T-' . substr(date('Y'), -3) . '-' . random_int(100, 999) . '-' . random_int(100, 999);
            if ($request->inquiryStatus == 1 && $exists!=1) {
                companies::create([
                    'companyID' => $companyId,
                    'registrationID' => $reg_id,
                    'companyName' => $spaceRequest->companyName,
                    'companyEmail' => $spaceRequest->companyEmail,
                    'companyPhone' => $spaceRequest->companyPhone,
                    'status'=>1,
                ]);
                return redirect()->route('companies.edit',['company'=>$companyId])->with('success', 'Request Attended successfully! and Company Account created');
            }

            //     $email_data=[
            //         'fromName'=>config('app.name'),
            //         'email'=>$spaceRequest->companyEmail,
            //         'name'=>$spaceRequest->companyName,
            //         'inquiryStatus'=>$request->inquiryStatus,
            //         'equiryFeedBack'=>$request->equiryFeedBack,
            //         'subject'=>'SPACE BOOKING REQUEST FEEDBACK',
            //     ];
            //     Mail::send('mails.SpaceRequestFeedBack', $email_data, function ($message) use ($email_data) {
            //         $message->to($email_data['email'],$email_data['name'])
            //         ->bcc('remixkabandwa@gmail.com')
            //         ->subject($email_data['subject'])
            //         ->priority(1);
            // });
            # ***************** END FEEDBACK ************* #

            return back()->with('success', 'Request Attended successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong') . $th->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
