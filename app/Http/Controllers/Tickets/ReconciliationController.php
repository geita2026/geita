<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\TicketCollection;
use App\Models\Tickets\VipTicketBookings;
use Illuminate\Http\Request;
use App\Traits\IntergrationTraits;
use Spatie\Permission\Models\Role;

class ReconciliationController extends Controller
{
    use IntergrationTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices=TicketCollection::orderBy('paymentDate','DESC')->leftJoin('companies','ticket_collections.customerId','=','companies.companyID')
        ->get();

        // $vCollections=TicketCollection::sum('amountExpected');

        $vCollections=VipTicketBookings::leftJoin('produced_tickets','vip_ticket_bookings.ticket_booking_id','=','produced_tickets.bookingId')
        ->where('bookingEvent',session('liveEvent'))
        ->where('serviceCode','VODACOM')
        ->sum('ticketAmount');
        
        $vCollections=ProducedTicket::leftJoin('vip_ticket_bookings','vip_ticket_bookings.ticket_booking_id','=','produced_tickets.bookingId')
        ->where('bookingEvent',session('liveEvent'))
        ->where('control_number',Null)->where('invoice_status',1)
        ->sum('ticketAmount');

        $tCollections=ProducedTicket::leftJoin('vip_ticket_bookings','vip_ticket_bookings.ticket_booking_id','=','produced_tickets.bookingId')
        ->where('bookingEvent',session('liveEvent'))
        ->where('control_number','!=',Null)->where('invoice_status',1)
        ->sum('ticketAmount');

        return view('Tickets.Reconciliation',compact('invoices','vCollections','tCollections'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        try {
            if($request->user()->cannot('Delete Reconciliation',Role::class))
            {
                return back()->with('fail',config('Messages.401'));
            }
        $collectionId=$id;
        $ticket=TicketCollection::leftJoin('companies','companyID','=','ticket_collections.customerId')
        ->where('collectionId',$collectionId)->first();

        $descriptions = 'Online Sabasaba Ticket Collected Amount on ' . date('d-m-Y');

        $items[] = ['item_code' => '142203710007', 'amount' => $ticket->amountExpected];

        $data = array(
            'customer_no' => $ticket->sp_customer_no, 'amount' => $ticket->amountExpected, 'currency' => 'TZS',
            'reference' => $collectionId, 'withholding_tax' => 0, 'taxable' => 0, 'payment_mode' => 1,
            'expire_date' => date('d-m-Y', strtotime('2030-01-01')), 'items' => $items, 'description' => $descriptions
        );
        $this->RequestInvoiceToBilling($collectionId, $data); // requesting invoice to billing 
        return back()->with('success','Control number requested successfully!');
        } catch (\Throwable $th) {
           return back()->with('fail','Something went wrong!'.$th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
            if($request->user()->cannot('Delete Reconciliation',Role::class))
            {
                return back()->with('fail','You do not have permission to perform this Action');
            }
            $exists=TicketCollection::find($id);
            if ($exists && empty($exists->controlNumber)) {
               $exists->delete();
               return back()->with('success','Recociliation Deleted successfully!');
            }
            else {
                return back()->with('fail','Reconciliation Does not exist');
            }
        } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!');
        }
    }
}
