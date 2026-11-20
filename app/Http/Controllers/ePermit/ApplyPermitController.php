<?php

namespace App\Http\Controllers\ePermit;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use App\Models\ePermit\ApplicationInvoice;
use App\Models\ePermit\ApplicationReviewer;
use App\Models\ePermit\ePermitApplication;
use App\Models\ePermit\PromotionEventType;
use App\Models\ePermit\TradePromotionEvent;
use App\Models\exhibition\Product;
use App\Models\sectors;
use App\Models\setting\Country;
use App\Models\setting\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Traits\ThirdPartIntergrationTraits;
use App\Traits\IntergrationTraits;
use App\Traits\TsrnSupplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Throwable;

class ApplyPermitController extends Controller
{
    use ThirdPartIntergrationTraits;
    use IntergrationTraits;
    use TsrnSupplier;
    public function __construct()
    {

        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->user()->cannot('Manage MyApplications', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }

            $applications = ePermitApplication::orderBy('applicationDate', 'DESC')
                ->leftJoin('companies', 'e_permit_applications.companyId', '=', 'companies.companyID')
                ->leftJoin('promotion_event_types', 'e_permit_applications.typeOfPromotionEvent', '=', 'promotion_event_types.eventTypeId')
                ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                ->where('appliedBy', Auth::user()->user_uuid)
                ->get();

            $proEvents = PromotionEventType::get([
                'eventTypeId',
                'promotionEventTypeName'
            ]);
            $myCompany = CompanyAdmin::leftJoin('companies', 'company_admins.companyID', '=', 'companies.companyID')
                ->where('adminId', Auth::user()->user_uuid)->get(['company_admins.companyID', 'companyName']);
            return view('ePermit.PermitList', compact('applications', 'myCompany', 'proEvents'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
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
            if ($request->user()->cannot('Manage MyApplications', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $proEvents = PromotionEventType::get([
                'eventTypeId',
                'promotionEventTypeName'
            ]);
            $myCompany = CompanyAdmin::leftJoin('companies', 'company_admins.companyID', '=', 'companies.companyID')
                ->where('company_admins.adminId', Auth::user()->user_uuid)
                ->get(['company_admins.companyId', 'companyName']);

            if (Auth::user()->account_type == 1) {
                $myCompany = companies::orderBy('companyName', 'ASC')
                    ->where('status', 1)->get(['companyID', 'companyName']);
            }
            return view('ePermit.CreateApplication', compact('proEvents', 'myCompany'));
        } catch (Throwable $th) {
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
            if ($request->user()->cannot('Manage MyApplications', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validator = Validator::make($request->all(), [
                'typeOfPromotionEvent' => 'required',
                'typeOfApplication' => 'required'
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }

            $applicationCount = ePermitApplication::where('companyId', $request->companyId)->where('applicationStatus', '<', 6)->count();

            if ($applicationCount <= 3) {
                $permitId = Str::uuid();
                ePermitApplication::create([
                    'applicationId' => $permitId,
                    'companyId' => $request->companyId,
                    'appliedBy' => Auth::user()->user_uuid,
                    'applicationStatus' => 1,
                    'applicationComment' => Null,
                    'typeOfPromotionEvent' => $request->typeOfPromotionEvent,
                    'typeOfApplication' => $request->typeOfApplication,
                ]);
                TradePromotionEvent::create([
                    'tradeEventId' => Str::uuid(),
                    'tradeEventType' => $request->typeOfPromotionEvent,
                    'eventApplicationId' => $permitId,
                ]);
                return redirect()->route('apply_permit.show',['apply_permit'=>$permitId])->with('success', config('Messages.200'));
            } else {
                return redirect()->route('apply_permit.index')->with('success', 'You have Three application pending, wait until have been approved and paid!');
            }
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
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
            if ($request->user()->cannot('Manage MyApplications', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $products = Product::get();
            $sectors = sectors::get(['sectorID', 'sectorName']);
            $application = ePermitApplication::leftJoin('companies', 'e_permit_applications.companyId', '=', 'companies.companyID')
                ->leftJoin('promotion_event_types', 'e_permit_applications.typeOfPromotionEvent', '=', 'promotion_event_types.eventTypeId')
                ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
                ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
                ->where('applicationId', $id)->get();
            $sectors = sectors::get(['sectorID', 'sectorName']);
            $countries = Country::orderByRaw("CASE WHEN country_name = 'Tanzania' THEN 0 ELSE 1 END")->get(['country_name', 'country_short_code']);
            $invoices = ApplicationInvoice::where('referenceApplication', $id)->get();

            $submitForReview = TradePromotionEvent::selectRaw('
    (CASE WHEN eventTitle IS NULL THEN 1 ELSE 0 END) +
    (CASE WHEN eventTheme IS NULL THEN 1 ELSE 0 END) +
    (CASE WHEN eventObjectives IS NULL THEN 1 ELSE 0 END) +
    (CASE WHEN startFrom IS NULL THEN 1 ELSE 0 END) +
    (CASE WHEN endDate IS NULL THEN 1 ELSE 0 END) +
    (CASE WHEN venueAddress IS NULL THEN 1 ELSE 0 END) +
    (CASE WHEN venueOwner IS NULL THEN 1 ELSE 0 END) +
    (CASE WHEN typeOfBuilding IS NULL THEN 1 ELSE 0 END) +
    (CASE WHEN venueOwnerAddress IS NULL THEN 1 ELSE 0 END)  
    AS nullValues
')->where('eventApplicationId', $id)->first();
            if (!$submitForReview) {
                TradePromotionEvent::create([
                    'tradeEventId' => Str::uuid(),
                    'tradeEventType' => $request->typeOfPromotionEvent,
                    'eventApplicationId' => $id,
                ]);
                $submitForReview = TradePromotionEvent::where('eventApplicationId', $id)->first();
            }
            $asignedReviewer = ApplicationReviewer::leftJoin('users', 'application_reviewers.asignedUser', '=', 'users.user_uuid')
                ->where('applicationReference', $id)->get();
            $rate = ExchangeRate::where('currencyCode', 'USD')->first()->SELLING;

            return view('ePermit.ApplyePermit', compact('application', 'products', 'sectors', 'countries', 'invoices', 'submitForReview', 'rate', 'asignedReviewer'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
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
            if ($request->user()->cannot('Manage MyApplications', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $permit = ApplicationInvoice::leftJoin('e_permit_applications', 'application_invoices.referenceApplication', '=', 'e_permit_applications.applicationId')
                ->leftJoin('trade_promotion_events', 'e_permit_applications.applicationId', '=', 'trade_promotion_events.eventApplicationId')
                ->leftJoin('companies', 'application_invoices.customerId', '=', 'companies.companyID')
                ->leftJoin('promotion_event_types', 'trade_promotion_events.tradeEventType', '=', 'promotion_event_types.eventTypeId')
                ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
                ->where('applicationId', $id)->get();
            $signature = ePermitApplication::leftJoin('users', 'e_permit_applications.lastComment', '=', 'users.user_uuid')->first();
            $pdf = Pdf::loadView('ePermit.PermitPDF.DownloadablePermit2024', compact('permit', 'signature'));
            $pdf->setPaper('A4', 'potrait');
            $pdf->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]
                ])
            );
            $pdf->render();
            return $pdf->stream($id . '.pdf');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
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
            if ($request->user()->cannot('Manage MyApplications', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            if ($request->source == 1) {
                $MOFAttachment = Null;
                $application = ePermitApplication::find($id);
                $event = TradePromotionEvent::where('eventApplicationId', $id)->first();
                if ($event) {
                    $MOFAttachment = $event->MOFAttachment;
                    if ($request->MOFAttachment) {
                        $file = 'MOFAttachment_' . date('sdmsdY') . '_' . $request->MOFAttachment->extension();
                        $filePath = public_path(str_replace(env('APP_URL') . '/public', '', $event->MOFAttachment));
                        File::delete($filePath);
                        $request->MOFAttachment->move(public_path('ePermitFiles/uploadedFiles'), $file);
                        $MOFAttachment = env('APP_URL') . '/public/ePermitFiles/uploadedFiles/' . $file;
                    }
                    TradePromotionEvent::where('eventApplicationId', $id)->first()
                        ->update([
                            'eventTitle' => $request->eventTitle,
                            'eventTheme' => $request->eventTheme,
                            'eventObjectives' => $request->eventObjectives,
                            'productExhibited' => $request->eventSector,
                            'startFrom' => $request->startFrom,
                            'endDate' => $request->endDate,
                            'localParticipants' => $request->localParticipants,
                            'foreignParticipants' => $request->foreignParticipants,
                            'MOFAttachment' => $MOFAttachment,
                            'entryFee' => $request->entryFee,
                            'estimateExpenditure' => $request->estimateExpenditure,
                            'eventWebsite' => $request->eventWebsite,
                            'venueAddress' => $request->venueAddress,
                            'venueCoordinates' => $request->venueCoordinates,
                            'typeOfBuilding' => $request->typeOfBuilding,
                            'venueOwner' => $request->venueOwner,
                            'venueOwnerAddress' => $request->venueOwnerAddress,
                            'venueSize' => $request->venueSize
                        ]);

                    if ($application->reviewComment != Null) {
                        $application->update([
                            'applicationStatus' => 1,
                            'allowedToEdit' => 1,
                        ]);
                        return back()->with('success', config('Messages.200'));
                    }
                } else {
                    if ($request->MOFAttachment) {
                        $file = 'MOFAttachment_' . date('sdmsdY') . '_' . $request->MOFAttachment->extension();
                        $request->MOFAttachment->move(public_path('ePermitFiles/uploadedFiles'), $file);
                        $MOFAttachment = env('APP_URL') . '/public/ePermitFiles/uploadedFiles/' . $file;
                    }
                    TradePromotionEvent::create([
                        'tradeEventId' => Str::uuid(),
                        'eventApplicationId' => $application->applicationId,
                        'tradeEventType' => $application->typeOfPromotionEvent,
                        'eventTitle' => $request->eventTitle,
                        'eventTheme' => $request->eventTheme,
                        'eventObjectives' => $request->eventObjectives,
                        'productExhibited' => $request->eventSector,
                        'startFrom' => $request->startFrom,
                        'endDate' => $request->endDate,
                        'localParticipants' => $request->localParticipants,
                        'foreignParticipants' => $request->foreignParticipants,
                        'MOFAttachment' => $MOFAttachment,
                        'entryFee' => $request->entryFee,
                        'estimateExpenditure' => $request->estimateExpenditure,
                        'eventWebsite' => $request->eventWebsite,
                        'venueAddress' => $request->venueAddress,
                        'venueCoordinates' => $request->venueCoordinates,
                        'typeOfBuilding' => $request->typeOfBuilding,
                        'venueOwner' => $request->venueOwner,
                        'venueOwnerAddress' => $request->venueOwnerAddress,
                        'venueSize' => $request->venueSize
                    ]);
                }
                return back()->with('success', config('Messages.200'));
            } elseif ($request->source == 2) {
                ePermitApplication::find($id)
                    ->update([
                        'allowedToEdit' => 0,
                        'applicationStatus' => 2,
                    ]);
                return back()->with('success', config('Messages.200'));
            }
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
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
            if ($request->user()->cannot('Delete MyApplications', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $application = ePermitApplication::where('applicationId', $id)->where('applicationStatus', '<', 6)->first();
            if ($application) {
                $evetDetails = TradePromotionEvent::where('eventApplicationId', $id)->first();
                if ($evetDetails) {
                    $evetDetails->delete();
                }
                $reviewer = ApplicationReviewer::where('applicationReference', $id)->first();
                if ($reviewer) {
                    $reviewer->delete();
                }
                $invoice = ApplicationInvoice::where('referenceApplication', $id)->first();
                if ($invoice) {
                    return back()->with('success', 'This application have control Number please Cancel Invoice First');
                }
                $application->delete();
                return back()->with('success', config('Messages.200'));
            } else {
                return back()->with('fail', 'This Application can not be deleted!');
            }
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }

    public function requestControlNumber(Request $request)
    {
        try {
            $invoice = ApplicationInvoice::leftJoin('companies', 'application_invoices.customerId', '=', 'companies.companyID')
                ->where('invoiceNo', $request->invoiceNo)->first();

            if ($invoice->spInvoiceNo != Null) {
                return back()->with('success', 'Please wait we are generating Control Number this may take one to two minutes');
            }
            if ($request->currency == 'TZS' && $invoice->currency == 'USD') {
                $rate = ExchangeRate::where('currencyCode', 'USD')->first()->SELLING;
                $newAMount = $invoice->invoiceAmount * $rate;
                $invoice->update([
                    'invoiceAmount' => $newAMount,
                    'currency' => $request->currency
                ]);
            }

            $items[] = ['item_code' => '142202570005', 'amount' => $invoice->invoiceAmount];
            $data = array(
                'customer_no' => $invoice->sp_customer_no,
                'amount' => $invoice->invoiceAmount,
                'currency' => $invoice->currency,
                'reference' => $invoice->invoiceNo,
                'withholding_tax' => 0,
                'taxable' => 0,
                'payment_mode' => $invoice->paymentMode,
                'expire_date' => '31-12-9999',
                'items' => $items,
                'description' => $invoice->invoiceDescription,
            );
            $id = $invoice->invoiceNo;
            $this->RequestInvoiceToBilling($id, $data);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
