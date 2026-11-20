<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\settings\events;
use App\Models\Tickets\OnlineTicket;
use App\Models\Tickets\VipTicketBenefit;
use App\Models\Tickets\VipTicketBookings;
use App\Models\Tickets\VipTicketPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Traits\IntergrationTraits;
use App\Traits\VistorsTrait;
use App\Traits\YasMandateTrait;
use App\Models\Collections\YasTransaction;

class OnlineTicketsController extends Controller
{
    use IntergrationTraits;
    use VistorsTrait;
    use YasMandateTrait; // getYasToken(), yasPushBillPay(), makeYasReference()
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            return back();
            $tickets = VipTicketPackage::where('packageStatus', 1)->orderBy('package_cost', 'ASC')->where('package_cost', '!=', 4000)->get();
            $event = events::first();
            return view('FrontEnd.Public_Pages.VipTickets.VipTicketForm', compact('tickets', 'event'));
        } catch (\Throwable $th) {
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
            if (session('phoneNumber') == null) {
                return back()->with('fail', 'Please verify your phone number before booking tickets');
            }

            $validator = Validator::make($request->all(), [
                'tickets' => 'required|array|min:1',
                'tickets.*.type' => 'required|exists:vip_ticket_packages,vip_package_id',
                'tickets.*.quantity' => 'required|integer|min:1',
                'tickets.*.ticketFor' => 'required|string',
            ], [
                'tickets.*.type.required' => 'Please select a ticket type for each row.',
                'tickets.*.type.exists' => 'The selected ticket type is invalid.',
                'tickets.*.ticketFor.required' => 'Please enter a name or plate number for each ticket.',
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput()
                    ->with('fail', $validator->errors()->first());
            }

            $liveEvent = events::where('event_status', 1)->first();
            if (!$liveEvent) {
                return back()->with('fail', 'Ticket sales are not open at the moment. Please try again later.');
            }

            $unpaidTickets = VipTicketBookings::where('phone_number', session('phoneNumber'))
                ->where('ticket_booked_on', 'LIKE', '%' . date('Y') . '%')
                ->where('amount_paid', 0)->first();

            if ($unpaidTickets) {
                session(['unPayedTicket' => $unpaidTickets->ticket_booking_id]);
                return back()->with('fail', 'You have Pending payment of TZS ' . number_format($unpaidTickets->ticket_amount) . ', Control Number: ' . $unpaidTickets->control_number . ' for the previous booking. Please pay before booking new tickets.');
            }
            session(['liveEvent' => $liveEvent->event_id]);

            # Payment deadline = event end date, but never in the past
            # (billing rejects past expire dates with code 112 "Invalid Date/Expire Date").
            $paymentDeadline = $liveEvent->event_end_date;
            if (!$paymentDeadline || strtotime($paymentDeadline) < strtotime('today')) {
                $paymentDeadline = date('Y-m-d', strtotime('+14 days'));
            } else {
                $paymentDeadline = date('Y-m-d', strtotime($paymentDeadline));
            }

            $description = array();
            $ticketAmount = 0;
            $requestNo = Str::uuid();
            foreach ($request['tickets'] as $value) {
                $ticket = VipTicketPackage::find($value['type']);
                OnlineTicket::create([
                    'requestId' => Str::uuid(),
                    'packageId' => $ticket->vip_package_id,
                    'requestedBy' => $value['ticketFor'],
                    'phoneNumber' => session('phoneNumber'),
                    'requestNo' => $requestNo,
                    'event' => session('liveEvent'),
                    'quantity' => $value['quantity'],
                    'requestStatus' => 1,
                    'plateNumber' => $value['ticketFor'],
                ]);
                #********** CREATE DESCRIPTION **********#
                $data = $ticket->package_discriptions . ' (' . $ticket->quantity . ' @ ' . number_format($ticket->package_cost) . ' TZS)';
                array_push($description, $data);
                $ticketAmount = $ticketAmount +  $ticket->package_cost;
                $ticketType = 1; # Normal Ticket
                if ($ticket->package_cost > 10000) {
                    $ticketType = 2; # VIP Ticket
                }
            }
            $customerId = Str::uuid();
            VipTicketBookings::create([
                'ticket_booking_id' => $customerId,
                'customer_name' => 'Online Ticket',
                'email_address' => 'noreply@tantrade.go.tz',
                'phone_number' => session('phoneNumber'),
                'total_amount' => $ticketAmount,
                'payment_deadline' => $paymentDeadline,
                'booked_package' => $ticket->vip_package_id,
                'ticket_amount' => $ticketAmount*$value['quantity'],
                'currency' => 'TZS',
                'gfs_code_mapping' => $ticket->gfs_code,
                'ticket_status' => 1,
                'ticketDescription' => implode(',', $description),
                'TicketTitle' => 'Online Ticket',
                'ticketType' => $ticketType,
                'requestId' => $requestNo,
                'serviceCode' => 'TANTRADE',
                'bookingEvent' => session('liveEvent'),
                'sp_customer_no' => config('global.TICKET_SP_CUSTOMER_NO'),
            ]);
            $this->OrganizeInvoice($customerId);
            return back()->with('success', 'We have received your request, we will send you control number and payment details to your phone number: ' . session('phoneNumber') . ' shortly.');
        } catch (\Throwable $th) {
            \Log::error('Online ticket store() failed', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'phone' => session('phoneNumber'),
            ]);
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function edit(Request $request, $id)
    {
        return back()->with('fail', 'Booking online ticket is now closed');
        try {
            $this->Vistors($ip = $request->ip()); // save new vistor

            if (config('global.ONLINE_TICKET_BOOKING') == 0) {
                return back()->with('fail', 'Online Ticket Booking will be opened on 25th June, 2024.');
            }
            if (!session('ticketRequest')) {
                $validator = Validator::make($request->all(), [
                    'fullName' => 'required',
                    'phoneNumber' => 'required|digits:12|starts_with:255',
                    'visitDate' => 'required|date_format:d-m-Y'
                ]);
                if ($validator->fails()) {
                    return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
                }
            }
            $requestNo = $id;
            $name = $request->fullName;
            $email = $request->emailAddress;
            $phone = $request->phoneNumber;
            $quantity = $request->ticketQuantity;
            $plateNumber = $request->plateNumber;
            session(['ticketRequest' => $id]);
            $ticketRequest = OnlineTicket::where('requestNo', $id)->where('event', session('liveEvent'))->first();
            if ($ticketRequest) {
                $name = $ticketRequest->requestedBy;
                $email = $ticketRequest->email;
                $phone = $ticketRequest->phoneNumber;
                $requestNo = $id;
            }

            $ticketType = $request->ticketType;
            if (empty($request->ticketType)) {
                $ticketType = $request->sabaSabaTicket;
            }
            session(['requestNo' => $requestNo]);

            if ($request->ticketQuantity > 0) {
                OnlineTicket::create([
                    'requestId' => Str::uuid(),
                    'packageId' => $ticketType,
                    'requestedBy' => $name,
                    'email' => $email,
                    'phoneNumber' => $phone,
                    'requestNo' => $requestNo,
                    'event' => session('liveEvent'),
                    'quantity' => $quantity,
                    'requestStatus' => 1,
                    'visitDate' => $request->visitDate,
                    'plateNumber' => $request->plateNumber,
                ]);
                session(['item' => $ticketType]);
            }
            $sabasabaTickets = VipTicketPackage::Where('package_cost', '!=', 3000)->get();
            $packages = VipTicketPackage::orderBy('package_cost', 'ASC')->where('package_cost', '!=', 4000)->get();
            $carParking = VipTicketPackage::where('package_discriptions', 'LIKE', '%car%')->get();
            $benefits = VipTicketBenefit::get();
            $requests = OnlineTicket::leftJoin('vip_ticket_packages', 'online_tickets.packageId', '=', 'vip_ticket_packages.vip_package_id')
                ->where('event', session('liveEvent'))->where('requestNo', $id)->get();
            return view('FrontEnd.Public_Pages.VipTickets.VipTickets', compact('packages', 'benefits', 'requests', 'carParking', 'sabasabaTickets'))->with('success', 'Item added successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'something went wrong!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return back()->with('fail', 'Booking online ticket is now closed');
        $packages = VipTicketPackage::where('vip_package_id', $id)->get();
        $benefits = VipTicketBenefit::get();
        $allBackages = VipTicketPackage::where('vip_package_id', '!=', $id)->get();
        return view('FrontEnd.Public_Pages.VipTickets.BookTickets', compact('packages', 'benefits', 'allBackages'));
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
        return back()->with('fail', 'Booking online ticket is now closed');
        $request->validate([
            'customer_name' => 'required',
            'email_address' => 'required|email',
            'phone_number' => 'required|digits:12|starts_with:255'
        ]);

        try {
            $ticket = VipTicketPackage::find($id); // package contents
            $customerId = Str::uuid();
            $firstName = $request->customer_name;
            $phone_number = $request->phone_number;
            $email = $request->email_address;

            $exist = VipTicketBookings::Orwhere('customer_name', $firstName)->OrWhere('email_address', $email)
                ->OrWhere('phone_number', $phone_number)->first();

            if ($exist) {
                return back()->with('success', $exist->customer_name . ' This customer exist with control Number: ' . $exist->control_number);
            } else {
                // return $customerId.$firstName.$phone_number.$email;
                VipTicketBookings::create([
                    'ticket_booking_id' => $customerId,
                    'customer_name' => $firstName,
                    'email_address' => $email,
                    'phone_number' => $phone_number,
                    'total_amount' => $request->amount,
                    'control_number' => $request->control_number,
                    'payment_deadline' => date('2024-07-13'), // end of event
                    'booked_package' => $id,
                    'ticket_amount' => $ticket->package_cost,
                    'currency' => $ticket->currency,
                    'gfs_code_mapping' => $ticket->gfs_code,
                    'ticket_status' => 1,
                    'visitDate' => $request->visitDate,
                ]);
                $booking_id = $customerId;
                $ticketAmount = Null;
                $this->VipBookingNotifications($booking_id, $ticketAmount);
            }
            return back()->with('success', 'We have sent you verification Link, Kindly click the link to Verify your request to get control number');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong, please try again' . $th->getMessage());
        }
    }

    public function VipBookingNotifications($booking_id, $ticketAmount)
    {
        $ticket = VipTicketBookings::find($booking_id);

        # ******************** EMAIL VERIFICATION ************ #
        $email_data = [
            'fromName' => config('app.name'),
            'email' => $ticket->email_address,
            'name' => $ticket->customer_name,
            'ticketDescription' => $ticket->ticketDescription,
            'ticketAmount' => $ticketAmount,
            'booking_id' => $booking_id,
            'link' => env('APP_URL') . '/contact_verification/' . $booking_id, // link to be clicked
            'subject' => $ticket->TicketTitle,
        ];
        Mail::send('mails.VipTicketsVerification', $email_data, function ($message) use ($email_data) {
            $message->to($email_data['email'], $email_data['name'])
                ->bcc('rkabandwa@gmail.com')
                ->subject($email_data['subject']);
        });
        # ***************** END EMAIL VERIFICATION ************* #

    }

    #********* REMOVE REQUEST**********#
    public function RemoveRequest($id)
    {
        try {
            $requests = OnlineTicket::where('requestNo', $id)->get();
            $i = 0;
            foreach ($requests as  $value) {
                OnlineTicket::find($value->requestId)->delete();
                $i++;
            }
            if ($i > 0) {
                return redirect()->route('online_tickets.index')->with('success', 'Items removed successfully!');
            }
            $request = OnlineTicket::find($id);
            $count = OnlineTicket::where('requestNo', $request->requestNo)->count();
            if ($count == 1) {
                $request->delete();
                Session::forget('ticketRequest');
                Session::forget('item');
                return redirect()->route('online_tickets.index')->with('success', 'Items removed successfully!');
            }
            $request->delete();
            return back()->with('success', 'Item removed');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong!!');
        }
    } 
    #*********** End Remove Request ***********# 

    #****************** ORGANIZE INVOICE **************#
    public function OrganizeInvoice($company_id)
    {
        $invoice = VipTicketBookings::find($company_id);
        $vat = 0;
        $descriptions = $invoice->ticketDescription;
        $items[] = ['item_code' => $invoice->gfs_code_mapping, 'amount' => $invoice->ticket_amount];
        $id = $company_id;
        $data = array(
            'customer_no' => $invoice->sp_customer_no,
            'amount' => $invoice->ticket_amount,
            'currency' => $invoice->currency,
            'reference' => $company_id,
            'withholding_tax' => 0,
            'taxable' => $vat,
            'payment_mode' => 1,
            'expire_date' => date('d-m-Y', strtotime($invoice->payment_deadline)),
            'items' => $items,
            'description' => $descriptions
        );
        $this->RequestInvoiceToBilling($id, $data); // requesting invoice to billing
    }
    #****************** END ORGANIZE INVOICE *********************#


    public function ticketMtandao(Request $request)
    {
        try {
            $tickets = VipTicketPackage::where('packageStatus', 1)->orderBy('package_cost', 'ASC')->get();
            $event = events::first();
            return view('FrontEnd.Public_Pages.VipTickets.OnlineTickets', compact('tickets', 'event'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required|starts_with:255|digits:12'
        ]);
        try {
            $exist = VipTicketBookings::where('phone_number', $request->phoneNumber)->first();
            if ($exist) {
                session(['page' => 'buyTicket']);
                session(['phoneNumber' => $request->phoneNumber]);
                return back()->with('success', $request->phoneNumber . ' Is already verified! please continue');
            }
            $recipients = $request->phoneNumber;
            $code = rand(1000, 9999);
            $sms_content = 'Verification code is: ' . $code;
            session(['verificationCode' => $code]);
            $datetime = date('Y-m-d H:i:s');
            if (config('global.APP_STATUS') == 1) {
                $this->Notification($recipients, $sms_content, $datetime);
            }
            session(['page' => 'verifyPhone']);
            session(['phoneNumber' => $request->phoneNumber]);
            return back()->with('success', 'We sent the verification code to ' . $request->phoneNumber . '. Please check it to proceed.');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'verificationCode' => 'required|digits:4'
        ]);
        try {
            if ($request->verificationCode == session('verificationCode') && $request->phoneNumber == session('phoneNumber')) {
                session(['page' => 'buyTicket']);
                return back()->with('success', 'Phone number verified successfully! please continue');
            }
            return back()->with('fail', 'Verification code is incorrect, please try again');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }


    public function changePhoneNUmber(Request $request)
    {
        session(['page' => null]);
        return back()->with('success', 'You can change phone number or resend code now!');
    }

    /**
     * Pay an online booking via the YAS (Mixx) USSD push instead of a control
     * number. Creates the booking + OnlineTicket rows (tagged MIXX-YAS) and
     * fires the push linked to the booking; the YAS callback then settles the
     * booking and issues the tickets. Returns JSON for the portal to poll.
     *
     * Additive: the control-number / M-Pesa flow in store() is untouched. This
     * only runs when the customer chooses "Pay with Yas".
     */
    public function buyTicketYas(Request $request)
    {
        try {
            if (session('phoneNumber') == null) {
                return response()->json(['status' => 0, 'message' => 'Please verify your phone number first']);
            }
            $phone = session('phoneNumber'); // verified, 255XXXXXXXXX

            // Only YAS/Tigo numbers: whole operator codes 065/067/071/077
            // (=> 255 65/67/71/77) PLUS finer Yas sub-blocks under another
            // operator, e.g. 0705 (=> 255705...). Add new Yas blocks to the
            // 705-style branch as Yas confirms them.
            if (!preg_match('/^255(?:(?:65|67|71|77)\d{7}|705\d{6})$/', (string) $phone)) {
                return response()->json(['status' => 0, 'message' => 'This number is not a Yas (Mixx) number. Please use control number / M-Pesa.']);
            }

            $validator = Validator::make($request->all(), [
                'tickets' => 'required|array|min:1',
                'tickets.*.type' => 'required|exists:vip_ticket_packages,vip_package_id',
                'tickets.*.quantity' => 'required|integer|min:1',
                'tickets.*.ticketFor' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 0, 'message' => $validator->errors()->first()]);
            }

            $liveEvent = events::where('event_status', 1)->first();
            if (!$liveEvent) {
                return response()->json(['status' => 0, 'message' => 'Ticket sales are not open at the moment.']);
            }
            session(['liveEvent' => $liveEvent->event_id]);

            $paymentDeadline = $liveEvent->event_end_date;
            if (!$paymentDeadline || strtotime($paymentDeadline) < strtotime('today')) {
                $paymentDeadline = date('Y-m-d', strtotime('+14 days'));
            } else {
                $paymentDeadline = date('Y-m-d', strtotime($paymentDeadline));
            }

            $description = [];
            $ticketAmount = 0;
            $requestNo = Str::uuid();
            $lastTicket = null;
            $ticketType = 1;
            foreach ($request['tickets'] as $value) {
                $ticket = VipTicketPackage::find($value['type']);
                $lastTicket = $ticket;
                OnlineTicket::create([
                    'requestId' => Str::uuid(),
                    'packageId' => $ticket->vip_package_id,
                    'requestedBy' => $value['ticketFor'],
                    'phoneNumber' => $phone,
                    'requestNo' => $requestNo,
                    'event' => session('liveEvent'),
                    'quantity' => $value['quantity'],
                    'requestStatus' => 1,
                    'plateNumber' => $value['ticketFor'],
                ]);
                $description[] = $ticket->package_discriptions . ' (' . $value['quantity'] . ' @ ' . number_format($ticket->package_cost) . ' TZS)';
                $ticketAmount += $ticket->package_cost * $value['quantity'];
                if ($ticket->package_cost > 10000) {
                    $ticketType = 2;
                }
            }

            $customerId = Str::uuid();
            VipTicketBookings::create([
                'ticket_booking_id' => $customerId,
                'customer_name' => 'Online Ticket',
                'email_address' => 'noreply@tantrade.go.tz',
                'phone_number' => $phone,
                'total_amount' => $ticketAmount,
                'payment_deadline' => $paymentDeadline,
                'booked_package' => $lastTicket->vip_package_id,
                'ticket_amount' => $ticketAmount,
                'currency' => 'TZS',
                'gfs_code_mapping' => $lastTicket->gfs_code,
                'ticket_status' => 1,
                'ticketDescription' => implode(',', $description),
                'TicketTitle' => 'Online Ticket',
                'ticketType' => $ticketType,
                'requestId' => $requestNo,
                'serviceCode' => 'MIXX-YAS',
                'bookingEvent' => session('liveEvent'),
                'sp_customer_no' => config('global.TICKET_SP_CUSTOMER_NO'),
            ]);

            // Fire the YAS push, linked to this booking. The existing callback
            // (YasPaymentController::callback) settles the booking + issues tickets.
            $referenceId = $this->makeYasReference();
            $txn = YasTransaction::create([
                'reference_id'    => $referenceId,
                'booking_id'      => $customerId,
                'customer_msisdn' => $phone,
                'biller_msisdn'   => config('yas.biller_msisdn'),
                'amount'          => $ticketAmount,
                'currency'        => 'TZS',
                'remarks'         => 'DITF online ticket',
                'status'          => YasTransaction::STATUS_PENDING,
            ]);

            $result = $this->yasPushBillPay($phone, (float) $ticketAmount, $referenceId, 'DITF online ticket');

            $txn->update([
                'status'           => $result['ok'] ? YasTransaction::STATUS_PUSHED : YasTransaction::STATUS_FAILED,
                'request_payload'  => ['CustomerMSISDN' => $phone, 'Amount' => $ticketAmount, 'ReferenceID' => $referenceId],
                'response_payload' => $result['body'],
                'response_code'    => $result['code'],
                'error'            => $result['ok'] ? null : ($result['description'] ?? 'Push failed'),
            ]);

            if (!$result['ok']) {
                return response()->json([
                    'status'      => 0,
                    'message'     => $result['description'] ?: 'Failed to send the Yas prompt. Please try again.',
                    'referenceId' => $referenceId,
                ]);
            }

            return response()->json([
                'status'      => 1,
                'message'     => 'Approve the Yas prompt on your phone to receive your ticket.',
                'referenceId' => $referenceId,
                'amount'      => $ticketAmount,
            ]);
        } catch (\Throwable $th) {
            \Log::error('Online Yas buyTicket failed', ['message' => $th->getMessage(), 'line' => $th->getLine()]);
            return response()->json(['status' => 0, 'message' => config('Messages.500')]);
        }
    }

    /**
     * Poll the status of an online YAS payment. Returns the ticket download
     * link once the callback has settled the booking.
     */
    public function yasOnlineStatus(Request $request)
    {
        $txn = YasTransaction::where('reference_id', $request['referenceId'])->first();
        if (!$txn) {
            return response()->json(['status' => 0, 'paymentStatus' => 'UNKNOWN']);
        }
        $download = null;
        if ($txn->status === YasTransaction::STATUS_SUCCESS && $txn->booking_id) {
            $download = url('/myTicket/' . $txn->booking_id);
        }
        return response()->json([
            'status'        => 1,
            'paymentStatus' => $txn->status, // PENDING | PUSHED | SUCCESS | FAILED
            'description'   => $txn->error ?? ($txn->status === YasTransaction::STATUS_SUCCESS ? 'Payment completed' : null),
            'download'      => $download,
        ]);
    }
}
