<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\PosTransaction;
use App\Models\Tickets\ProducedTicket;
use Illuminate\Http\Request;

class TicketManagement extends Controller
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

    // Add your methods for ticket management here
    public function eTACDashboard()
    {
        
        $TotalTickets = PosTransaction::sum('ticketAmount'); 
        $TotalSales = PosTransaction::where('ticketStatus', 1)->sum('ticketAmount'); 
        $totalOnlineTickets =ProducedTicket::sum('ticketAmount');
        return view('Dashboards.eTac',compact(
            'TotalSales',
            'totalOnlineTickets'
        ));
    }
}
