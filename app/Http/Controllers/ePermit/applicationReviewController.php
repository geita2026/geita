<?php

namespace App\Http\Controllers\ePermit;

use App\Http\Controllers\Controller;
use App\Models\Companies\CompanyAdmin;
use App\Models\ePermit\ApplicationInvoice;
use App\Models\ePermit\ApplicationReviewer;
use App\Models\ePermit\ePermitApplication;
use App\Models\ePermit\PromotionEventType;
use App\Models\sectors;
use App\Models\setting\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Traits\IntergrationTraits;

class applicationReviewController extends Controller
{
    use IntergrationTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->user()->cannot('REVIEW_APPLICATIONS', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('REVIEW_APPLICATIONS'));
            }
            if ($request->user()->cannot('REVIEW_ALL_APPLICATIONS', Role::class)) {
                if (!$request->applicationStatus) {
                    $applications = ePermitApplication::orderBy('applicationStatus', 'ASC')
                        ->orderBy('applicationDate', 'DESC')
                        ->leftJoin('companies', 'e_permit_applications.companyId', '=', 'companies.companyID')
                        ->leftJoin('promotion_event_types', 'e_permit_applications.typeOfPromotionEvent', '=', 'promotion_event_types.eventTypeId')
                        ->leftJoin('application_reviewers', 'e_permit_applications.applicationId', '=', 'application_reviewers.applicationReference')
                        ->leftJoin('users', 'application_reviewers.asignedUser', '=', 'users.user_uuid')
                        ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                        ->where('applicationStatus', '!=', 0)
                        ->paginate(config('global.DATA_LIMIT'));
                }
                if ($request->applicationStatus) {
                    $applications = ePermitApplication::orderBy('applicationStatus', 'ASC')
                        ->orderBy('applicationDate', 'ASC')
                        ->leftJoin('companies', 'e_permit_applications.companyId', '=', 'companies.companyID')
                        ->leftJoin('promotion_event_types', 'e_permit_applications.typeOfPromotionEvent', '=', 'promotion_event_types.eventTypeId')
                        ->leftJoin('application_reviewers', 'e_permit_applications.applicationId', '=', 'application_reviewers.applicationReference')
                        ->leftJoin('users', 'application_reviewers.asignedUser', '=', 'users.user_uuid')
                        ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                        ->where('applicationStatus', $request->applicationStatus)
                        
                        ->paginate(config('global.DATA_LIMIT'));
                }
                
            }
            else {

                if (!$request->applicationStatus) {
                    $applications = ePermitApplication::orderBy('applicationDate', 'DESC')
                        ->leftJoin('companies', 'e_permit_applications.companyId', '=', 'companies.companyID')
                        ->leftJoin('promotion_event_types', 'e_permit_applications.typeOfPromotionEvent', '=', 'promotion_event_types.eventTypeId')
                        ->leftJoin('application_reviewers', 'e_permit_applications.applicationId', '=', 'application_reviewers.applicationReference')
                        ->leftJoin('users', 'application_reviewers.asignedUser', '=', 'users.user_uuid')
                        ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                        ->paginate(config('global.DATA_LIMIT'));
                }
                if ($request->applicationStatus) {
                    $applications = ePermitApplication::orderBy('applicationDate', 'ASC')
                        ->leftJoin('companies', 'e_permit_applications.companyId', '=', 'companies.companyID')
                        ->leftJoin('promotion_event_types', 'e_permit_applications.typeOfPromotionEvent', '=', 'promotion_event_types.eventTypeId')
                        ->leftJoin('application_reviewers', 'e_permit_applications.applicationId', '=', 'application_reviewers.applicationReference')
                        ->leftJoin('users', 'application_reviewers.asignedUser', '=', 'users.user_uuid')
                        ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                        ->where('applicationStatus', $request->applicationStatus)
                        ->paginate(config('global.DATA_LIMIT'));
                }
            }
            $proEvents = PromotionEventType::get([
                'eventTypeId',
                'promotionEventTypeName'
            ]);
            $myCompany = CompanyAdmin::leftJoin('companies', 'company_admins.companyID', '=', 'companies.companyID')
                ->get(['company_admins.companyID', 'companyName']);

            $users = User::where('account_type', 1)->where('account_status', 1)
                ->leftJoin('roles', 'users.role', '=', 'roles.id')
                ->where('role', '!=', 20)
                ->where('roles.name','SAC Officer')
                ->get(['user_uuid', 'first_name', 'last_name', 'name']);
            return view('ePermit.ApplicationList', compact('applications', 'myCompany', 'proEvents', 'users'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('REVIEW_APPLICATIONS', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('REVIEW_APPLICATIONS'));
        }
        $query = $request->get('q');

        $application = ePermitApplication::leftJoin('companies', 'e_permit_applications.companyId', '=', 'companies.companyID')
            ->leftJoin('promotion_event_types', 'e_permit_applications.typeOfPromotionEvent', '=', 'promotion_event_types.eventTypeId')
            ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
            ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
            ->leftJoin('sectors', 'trade_promotion_events.productExhibited', '=', 'sectors.sectorID')
            ->orWhere('companyName', 'LIKE', '%' . $query . '%')
            ->orWhere('eventTitle', 'LIKE', '%' . $query . '%')
            ->limit(5)->get(['companyName', 'eventTitle', 'applicationId']);
        return response()->json($application);
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
            if ($request->user()->cannot('REVIEW_APPLICATIONS', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('REVIEW_APPLICATIONS'));
            }

            $validator = Validator::make($request->all(), [
                'reviewer' => 'required'
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            $application = ApplicationReviewer::where('applicationReference', $request->applicationReference)
                ->first();
            if ($application) {
                $application->update([
                    'applicationReference' => $request->applicationReference,
                    'asignedUser' => $request->reviewer,
                ]);
                return back()->with('success', config('Messages.200'));
            }
            ApplicationReviewer::create([
                'mappingId' => Str::uuid(),
                'applicationReference' => $request->applicationReference,
                'asignedUser' => $request->reviewer,
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getCode());
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
        try {
            if ($request->user()->cannot('REVIEW_APPLICATIONS', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('REVIEW_APPLICATIONS'));
            }
            $sectors = sectors::get(['sectorID', 'sectorName']);
            $application = ePermitApplication::leftJoin('companies', 'e_permit_applications.companyId', '=', 'companies.companyID')
                ->leftJoin('promotion_event_types', 'e_permit_applications.typeOfPromotionEvent', '=', 'promotion_event_types.eventTypeId')
                ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                ->leftJoin('sectors', 'trade_promotion_events.productExhibited', '=', 'sectors.sectorID')
                ->leftJoin('application_reviewers','e_permit_applications.applicationId','=','application_reviewers.applicationReference')
                ->leftJoin('users','application_reviewers.asignedUser','=','users.user_uuid')
                ->where('applicationId', $id)->get();

            $sectors = sectors::get(['sectorID', 'sectorName']);
            $countries = Country::orderByRaw("CASE WHEN country_name = 'Tanzania' THEN 0 ELSE 1 END")->get(['country_name', 'country_short_code']);
            $invoices = ApplicationInvoice::where('referenceApplication', $id)->get();
            return view('ePermit.ReviewApplication', compact('application', 'invoices'));
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
        try {
            if ($request->user()->cannot('REVIEW_APPLICATIONS', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('REVIEW_APPLICATIONS'));
            }
            $application = ePermitApplication::find($id);

            if ($request->decision == 1) {
                if ($request->user()->cannot('RETURN_APPLICATION', Role::class)) {
                    return back()->with('fail', $this->permissionDeniedMessage('RETURN_APPLICATION'));
                }
                $application->update([
                    'lastComment' => Auth::user()->user_uuid,
                    'reviewComment' => $request->comment,
                    'applicationStatus' => $request->decision,
                    'allowedToEdit' => 1,
                ]);
            } elseif ($request->decision == 2) {
                if ($request->user()->cannot('VERIFY_APPLICATION_REVIEW', Role::class)) {
                    return back()->with('fail', $this->permissionDeniedMessage('VERIFY_APPLICATION_REVIEW'));
                }
                $application->update([
                    'lastComment' => Auth::user()->user_uuid,
                    'reviewComment' => $request->comment,
                    'applicationStatus' => $request->decision
                ]);
            } elseif ($request->decision == 3) {
                if ($request->user()->cannot('REVIEW_STAGE_TWO', Role::class)) {
                    return back()->with('fail', $this->permissionDeniedMessage('REVIEW_STAGE_TWO'));
                }
                $application->update([
                    'lastComment' => Auth::user()->user_uuid,
                    'reviewComment' => $request->comment,
                    'applicationStatus' => $request->decision
                ]);
            } elseif ($request->decision == 4) {
                if ($request->user()->cannot('REVIEW_STAGE_THREE', Role::class)) {
                    return back()->with('fail', $this->permissionDeniedMessage('REVIEW_STAGE_THREE'));
                }
                $application->update([
                    'lastComment' => Auth::user()->user_uuid,
                    'reviewComment' => $request->comment,
                    'applicationStatus' => $request->decision
                ]);
            } elseif ($request->decision == 5) {
                if ($request->user()->cannot('REVIEW_STAGE_FOUR', Role::class)) {
                    return back()->with('fail', $this->permissionDeniedMessage('REVIEW_STAGE_FOUR'));
                }
                #---------- CREATE INVOICE TO CUSTOMER --------#
                if (!ApplicationInvoice::where('referenceApplication', $id)->exists()) {
                    $this->createInvoice($id); // id=applicationId
                }
                #---------- END CREATE INVOICE ---------------#
                $application->update([
                    'lastComment' => Auth::user()->user_uuid,
                    'reviewComment' => $request->comment,
                    'applicationStatus' => $request->decision,
                    'authorizedBy' => Auth::user()->user_uuid,
                ]);
            }
            if (!$request->decision) {
                return back()->with('fail','Please select Decision to make');
            }
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }


    public function createInvoice($applicationId)
    {
        $application = ePermitApplication::leftJoin('companies', 'e_permit_applications.companyId', '=', 'companies.companyID')
            ->leftJoin('promotion_event_types', 'e_permit_applications.typeOfPromotionEvent', '=', 'promotion_event_types.eventTypeId')
            ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
            ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
            ->where('applicationId', $applicationId)->first();

        $invoicNo = 'INV' . '-0' . date('y-') . strtoupper(Str::random(3)) . '-' . random_int(100, 999);

        if ($application->typeOfApplication == 1) {
            $amount = $application->eventTypeAmountTzs;
            $currency = 'TZS';
        }
        if ($application->typeOfApplication == 2 || $application->typeOfApplication == 3) {
            $amount = $application->eventTypeAmountUsd;
            $currency = 'USD';
        }
        $vatAmount = 0;

        $invoiceDescription = 'Being Payment for ' .
            $application->promotionEventTypeName .
            ' Permit, Organized from ' . $application->startFrom .
            ' To ' . $application->endDate . ' At ' . $application->venueAddress;

        $company_id = $application->companyId;
        $firstName = $application->companyName;
        $phone_number = $application->companyPhone;
        $email = $application->companyEmail;
        if ($application->sp_customer_no == Null) {
            $this->customer_registration_api($company_id, $firstName, $phone_number, $email);
        }
        ApplicationInvoice::create([
            'invoiceNo' => $invoicNo,
            'referenceApplication' => $applicationId,
            'invoiceAmount' => $amount,
            'vatAmount' => $vatAmount,
            'currency' => $currency,
            'paymentMode' => 1,
            'invoiceStatus' => 1,
            'invoiceDescription' => $invoiceDescription,
            'customerId' => $application->companyId,
            'createdBy' => Auth::user()->user_uuid,
            'expireDate' =>'31-12-9999',
        ]);
    }


    public function permitList(Request $request)
    {
        try {
            if ($request->user()->cannot('REVIEW_APPLICATIONS', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('REVIEW_APPLICATIONS'));
            }
            $applications = ePermitApplication::orderBy('applicationStatus', 'ASC')
                ->orderBy('applicationDate', 'ASC')
                ->leftJoin('companies', 'e_permit_applications.companyId', '=', 'companies.companyID')
                ->leftJoin('promotion_event_types', 'e_permit_applications.typeOfPromotionEvent', '=', 'promotion_event_types.eventTypeId')
                ->leftJoin('application_reviewers', 'e_permit_applications.applicationId', '=', 'application_reviewers.applicationReference')
                ->leftJoin('users', 'application_reviewers.asignedUser', '=', 'users.user_uuid')
                ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                ->where('permitNo', '!=', Null)->paginate(10);

            $proEvents = PromotionEventType::get([
                'eventTypeId',
                'promotionEventTypeName'
            ]);

            $users = User::where('account_type', 1)->where('account_status', 1)
                ->leftJoin('roles', 'users.role', '=', 'roles.id')
                ->where('role', '!=', 20)
                ->get(['user_uuid', 'first_name', 'last_name', 'name']);
            return view('ePermit.ApplicationList', compact('applications', 'proEvents', 'users'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }
}
