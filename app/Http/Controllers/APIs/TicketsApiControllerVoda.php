<?php

namespace App\Http\Controllers\APIs;

use App\Traits\APITraits;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\settings\events;
use App\Models\setting\ApiRequest;
use App\Traits\IntergrationTraits;
use App\Http\Controllers\Controller;
use App\Models\Tickets\OnlineTicket;
use App\Models\Notifications\BulkSms;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\TicketCollection;
use App\Models\Tickets\VipTicketPackage;
use App\Models\Tickets\VipTicketBookings;
use Illuminate\Support\Facades\Validator;
use App\Traits\TsrnSupplier;

class TicketsApiControllerVoda extends Controller
{
    use APITraits;
    use IntergrationTraits;
    use TsrnSupplier;

    // public function serverHits(){

    // }

    public function getAllServices(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $token = $request->token;
            $spCode = $request->spCode;
            $status = $this->AuthenticateRequest($spCode, $token);

            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 401);
            }

            $services = VipTicketPackage::orderBy('package_cost', 'ASC')
                // ->where('package_cost', '!=', 4000)
                ->get([
                    'vip_package_id as package_id',
                    'package_name',
                    'package_discriptions as package_description',
                    'package_description_sw',
                    'package_cost',
                    'currency'
                ]);
            return response()->json([
                'status' => 1,
                'services' => $services,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('global.100'),
            ], 500);
        }
    }


    public function postCustomerRequests(Request $request)
    {
        try {
            $token = $request->token;
            $spCode = $request->spCode;
            $status = $this->AuthenticateRequest($spCode, $token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }
            #----- END AUTHENTICATION ----------#
            $validator = Validator::make($request->all(), [
                'fullName' => 'required',
                'phoneNumber' => 'required|digits:12|starts_with:255',
                'tickets' => 'required|array',
                'tickets.*.ticketQuantity' => 'required|numeric',
                'tickets.*.ticketId' => 'required|exists:vip_ticket_packages,vip_package_id',
                'dateOfVisit' => 'required|date_format:d-m-Y',
                'spReference'=>'required|unique:vip_ticket_bookings,spReference'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }

            #--------------- ADD REQUEST -------------#
            $requestNo = random_int(10000000000, 99999999999) . '-' . date('Ymdhsi');
            foreach ($request['tickets'] as $key => $value) {
                $quantity = $value['ticketQuantity'];
                $name = $request->fullName;
                $email = 'noreply@tantrade.go.tz';
                $phone = $request->phoneNumber;

                $eventId = events::where('event_status', 1)->first()->event_id;

                $ticketType = $value['ticketId'];
                if ($quantity > 0) {
                    OnlineTicket::create([
                        'requestId' => Str::uuid(),
                        'packageId' => $ticketType,
                        'requestedBy' => $name,
                        'email' => $email,
                        'phoneNumber' => $phone,
                        'requestNo' => $requestNo,
                        'event' => $eventId,
                        'quantity' => $quantity,
                        'requestStatus' => 1,
                        'visitDate' => $request->dateOfVisit,
                        'plateNumber' => $request->plateNumber,
                    ]);
                }
                #--------------- END ADD REQUEST ---------#  
            }

            #*************** GENERATE BILL *****************#
            $eventId = events::where('event_status', 1)->first()->event_id;

            $ticket = OnlineTicket::leftJoin('vip_ticket_packages', 'online_tickets.packageId', '=', 'vip_ticket_packages.vip_package_id')
                ->where('requestNo', $requestNo)->first(); // Get data from reqested tickets

            $requests = OnlineTicket::leftJoin('vip_ticket_packages', 'online_tickets.packageId', '=', 'vip_ticket_packages.vip_package_id')
                ->where('requestNo', $requestNo)->get();

            #*********CREATE DESCRIPTION ********#
            $description = array();
            $ticketAmount = 0;
            foreach ($requests as $value) {
                $data = $value->package_discriptions . ' (' . $value->quantity . ' @ ' . number_format($value->package_cost) . ' TZS)';
                array_push($description, $data);
                $ticketAmount = $ticketAmount + ($value->quantity * $value->package_cost);
            }
            #********** END CREATE DESCRIPTION ********#
            $plateNumber = $ticket->plateNumber;
            $ticketTitle = 'SabaSaba Online Ticket';
            #************* END CAR PARKING *****#

            $customerId = Str::uuid();
            $firstName = $ticket->requestedBy;
            $phone_number = $ticket->phoneNumber;
            $email = 'noreply@tantrade.go.tz';

            $exist = VipTicketBookings::where('phone_number', $phone_number)->first();
            #-- VIP or Normal Ticket
            $ticketType = 1;
            if ($ticket->package_cost == 1500000) {
                $ticketType = 2;
            }
            #------ END CHECKING-----#

            $invoiceNo = strtoupper(Str::random(16) . date('d')); // C1
            VipTicketBookings::create([
                'ticket_booking_id' => $customerId,
                'customer_name' => $firstName,
                'email_address' => $email,
                'phone_number' => $phone_number,
                'payment_deadline' => date('2025-07-13'),
                'booked_package' => $ticket->packageId,
                'ticket_amount' => $ticketAmount,
                'amount_paid' => $ticketAmount,
                'currency' => $ticket->currency,
                'gfs_code_mapping' => $ticket->gfs_code,
                'ticket_status' => 1,
                'ticketDescription' => implode(',', $description),
                'TicketTitle' => $ticketTitle,
                'ticketType' => $ticketType,
                'plateNumber' => $plateNumber,
                'requestId' => $ticket->requestNo,
                'bookingEvent' => $eventId,
                'serviceCode' => $spCode,
                'sp_invoice_no' => $invoiceNo, // ticket No C2
                'spReference'=>$request->spReference,
            ]);

            ##### CHANGES STATUS FROM HERE ###### 
            if (config('global.APP_VASSION') == 1) {
                $this->generateTickets($customerId); // C3
                $tickets = rtrim(config('app.url'), '/') . '/myTicket/' . $customerId;
                $ticketNumber = ProducedTicket::where('bookingId', $customerId)
                    ->get(['ticketNo'])
                    ->pluck('ticketNo')               
                    ->toArray();
                $this->sendTicketMsg($customerId);
                return response()->json([
                    'status' => 1,
                    'bookingId' => $customerId,
                    'tickets' => $tickets,
                    'ticketNumber' => implode(', ', $ticketNumber),
                ]);
            }
            ####### END CHANGES ########
            else {
                $company_id = $customerId;
                if (!$exist && config('global.APP_STATUS') == 1) {
                    $status = $this->customer_registration_api($company_id, $firstName, $phone_number, $email); // register user as customer in billing.
                } else {
                    $newBooking = VipTicketBookings::where('ticket_booking_id', $company_id)
                        ->first();
                    $newBooking->update([
                        'sp_customer_no' => $exist->sp_customer_no,
                    ]);

                    $this->OrganizeInvoice($company_id, $spCode);

                    return response()->json([
                        'status' => 1,
                        'bookingId' => $customerId,
                        'message' => 'Control number requested successfully!'
                    ]);
                }
            }
        } catch (\Throwable $th) {
            OnlineTicket::where('requestNo', $requestNo)->delete(); // delete request when failed
            return response()->json([
                'status' => 0,
                'message' => 'ERROR: ERROR OCCURED',
            ]);
        }
    }
    #******************* 




    public function checkControlNumber(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
                'collectionId' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $token = $request->token;
            $spCode = $request->spCode;
            $collectionId = $request->collectionId;
            $status = $this->AuthenticateRequest($spCode, $token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }

            $collection = TicketCollection::find($collectionId);
            if ($collection) {
                if (empty($collection->controlNumber)) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Control Number still fetching.',
                    ]);
                } else {
                    $collection = TicketCollection::where('collectionId', $collectionId)
                        ->get(['collectionId', 'controlNumber', 'amountExpected']);
                    return response()->json([
                        'status' => 1,
                        'collection' => $collection,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Collection Id is Incorrect',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('global.100'),
            ], 500);
        }
    }


    public function getTickets(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
                'spReference' => 'required|exists:vip_ticket_bookings,spReference'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $token = $request->token;
            $spCode = $request->spCode;
            $spReference = $request->spReference;
            $status = $this->AuthenticateRequest($spCode, $token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }

            $booking = VipTicketBookings::where('spReference',$spReference)->first();
            if ($booking) {
                $tickets = ProducedTicket::where('bookingId', $booking->ticket_booking_id)->get(['ticketNo']);

                $ticketNo = [];
                foreach ($tickets as  $value) {
                    $ticketNo[] = $value->ticketNo;
                }
                #********* GENERATE TICKET **********#

                #********** AND GENERATE TICKET ******#
                $tickets = rtrim(config('app.url'), '/') . '/myTicket/' . $booking->ticket_booking_id;
                return response()->json([
                    'status' => 1,
                    'bookingId' => $booking->ticket_booking_id,
                    'tickets' => $tickets,
                    'ticketNumber' =>  implode(', ', $ticketNo),
                ]);
            }
            return response()->json([
                'status' => 0,
                'message' => 'We do not have this request in our Database',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('global.100') . $th->getMessage(),
            ], 500);
        }
    }

    public function postPayments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
                'bookingId' => 'required',
                'amountPaid' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $token = $request->token;
            $spCode = $request->spCode;
            $bookingId = $request->bookingId;
            $status = $this->AuthenticateRequest($spCode, $token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 401);
            }

            $booking = VipTicketBookings::find($bookingId);
            if ($booking) {
                if ($request->amountPaid >= $booking->ticket_amount) {
                    $booking->update([
                        'amount_paid' => $request->amountPaid,
                    ]);
                    return response()->json([
                        'status' => 1,
                        'message' => 'Payment Posted Successfully'

                    ]);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'PAYMENT MUST BE EQUAL OR GREATER THAN ' . number_format($booking->ticket_amount) . ' TZS',
                    ]);
                }
            }
            return response()->json([
                'status' => 0,
                'message' => 'BOOKING DOES NOT EXIST',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => config('global.100') . $th->getMessage(),
            ], 500);
        }
    }

    public function generateTickets($bookingId)
    {
        $booking = VipTicketBookings::find($bookingId);
        if ($booking) {
            $ticket = VipTicketBookings::find($bookingId);
            $exists = ProducedTicket::where('bookingId', $bookingId)->first();
            $tickets = OnlineTicket::leftJoin('vip_ticket_packages', 'online_tickets.packageId', '=', 'vip_ticket_packages.vip_package_id')
                ->where('requestNo', $ticket->requestId)
                ->get();
            #********* GENERATE TICKET **********#
            if (!$exists) {
                foreach ($tickets as  $value) {
                    $i = 1;
                    while ($i <= $value->quantity) {
                        ProducedTicket::create([
                            'ticketId' => Str::uuid(),
                            'ticketAmount' => $value->package_cost,
                            'bookingId' => $ticket->ticket_booking_id,
                            'ticketNo' => $this->generateTicketNo(),
                            'ticketName' => $value->package_discriptions,
                            'ticketStatus' => 1,
                            'customerName' => $ticket->customer_name,
                            'packageId' => $value->packageId,
                            'visitDate' => $value->visitDate,
                            'plateNumber' => $value->plateNumber,
                        ]);
                        $i++;
                    }
                }
            }
            #********** AND GENERATE TICKET ******#

        }
    }

    public function payCollection(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
                'collectionDate' => 'required|date_format:Y-m-d',
                'amountCollected' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $token = $request->token;
            $spCode = $request->spCode;
            $collectionDate = $request->collectionDate;
            $VCollection = $request->amountCollected;
            $status = $this->AuthenticateRequest($spCode, $token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }
            $TCollection = ProducedTicket::leftJoin('vip_ticket_bookings', 'produced_tickets.bookingId', '=', 'vip_ticket_bookings.ticket_booking_id')
                ->where('serviceCode', $spCode)->where('ticketDate', 'LIKE', '%' . $collectionDate . '%')->sum('ticketAmount');

            if ($TCollection == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => 'No Ticket sold for this Day',
                ]);
            }
            $customerId = ApiRequest::where('request_name', $spCode)->first()->client_id;
            $exists = TicketCollection::where('paymentDate', 'LIKE', '%' . $collectionDate . '%')->first();
            if (!$exists) {
                $collectionId = Str::uuid();
                TicketCollection::create([
                    'collectionId' => $collectionId,
                    'amountCollected' => $VCollection,
                    'customerId' => $customerId,
                    'amountExpected' => $TCollection,
                    'paymentDate' => $collectionDate,
                    'collectionStatus' => 0,
                ]);
            } else {
                $collectionId = $exists->collectionId;
                $exists->update([
                    'amountCollected' => $VCollection,
                    'amountExpected' => $TCollection,
                    'paymentDate' => $collectionDate,
                    'collectionStatus' => 0,
                ]);
            }
            if ($TCollection == $VCollection) {
                ##---------  Request Control Number   --------- ####
                $ticket = TicketCollection::leftJoin('companies', 'companyID', '=', 'ticket_collections.customerId')
                    ->where('collectionId', $collectionId)->first();
                if (!empty($ticket->controlNumber)) {
                    $collection = TicketCollection::where('collectionId', $collectionId)
                        ->get(['collectionId', 'controlNumber', 'amountExpected']);
                    return response()->json([
                        'status' => 1,
                        'collection' => $collection,
                    ]);
                }
                $descriptions = 'Online Sabasaba Ticket Collected Amount on ' . date('d-m-Y');
                $items[] = ['item_code' => '142203710007', 'amount' => $ticket->amountExpected];
                $data = array(
                    'customer_no' => $ticket->sp_customer_no,
                    'amount' => $ticket->amountExpected,
                    'currency' => 'TZS',
                    'reference' => $collectionId,
                    'withholding_tax' => 0,
                    'taxable' => 0,
                    'payment_mode' => 1,
                    'expire_date' => date('d-m-Y', strtotime('2025-01-01')),
                    'items' => $items,
                    'description' => $descriptions
                );

                $this->RequestInvoiceToBilling($collectionId, $data); // requesting invoice to billing 
                #---------  END Request Control Number -------- ####
                return response()->json([
                    'status' => 1,
                    'collectionId' => $collectionId,
                    'message' => 'Control Number requested successfully!'
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Amount collected mismatch, Requred Amount is: ' . number_format($TCollection) . ' TZS',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong!' . $th->getMessage(),
            ]);
        }
    }

    public function OrganizeInvoice($collectionId)
    {
        $ticket = TicketCollection::leftJoin('companies', 'companyID', '=', 'ticket_collections.customerId')
            ->where('collectionId', $collectionId)->first();

        $descriptions = 'Online Sabasaba Ticket Collected Amount on ' . date('d-m-Y');

        $items[] = ['item_code' => '142203710007', 'amount' => $ticket->amountExpected];

        $data = array(
            'customer_no' => $ticket->sp_customer_no,
            'amount' => $ticket->amountExpected,
            'currency' => 'TZS',
            'reference' => $collectionId,
            'withholding_tax' => 0,
            'taxable' => 0,
            'payment_mode' => 1,
            'expire_date' => date('d-m-Y', strtotime('2025-01-01')),
            'items' => $items,
            'description' => $descriptions
        );
        $this->RequestInvoiceToBilling($collectionId, $data); // requesting invoice to billing 
    }

    public function reconcilTransaction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'spCode' => 'required',
                'bookingId' => 'required',
                'amountPaid' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => implode(',', $validator->getMessageBag()->all()),
                ]);
            }
            $token = $request->token;
            $spCode = $request->spCode;
            $bookingId = $request->bookingId;
            $status = $this->AuthenticateRequest($spCode, $token);
            if ($status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => config('global.102')
                ], 404);
            }
            $exists = ProducedTicket::where('bookingId', $bookingId)->get();
        } catch (\Throwable $th) {
        }
    }
}
