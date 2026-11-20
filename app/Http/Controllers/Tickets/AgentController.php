<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Imports\TicketImport;
use App\Models\Tickets\PointOfSale;
use App\Models\Tickets\PosAgent;
use App\Models\Tickets\PosList;
use App\Models\Tickets\PosTransaction;
use Illuminate\Http\Request;
use Spatie\Permission\Contracts\Role;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('Manage Agent', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        try {
            $agents = PosAgent::orderBy('agentName', 'ASC')->leftJoin('pos_lists', 'pos_agents.asignedPos', '=', 'pos_lists.posId')
                ->leftJoin('point_of_sales', 'pos_agents.asignedLocation', '=', 'point_of_sales.locationId')
                ->where('agentStatus',1)
                ->paginate(10, [
                    'agentId',
                    'agentName',
                    'mobileNumber',
                    'locationName',
                    'posName',
                    'agentStatus',
                    'userName',
                    'asignedLocation',
                    'asignedEvent',
                    'asignedPos',
                    'asignedLocation'
                ]);
            if ($request->search) {
                $search = $request->search;
                $agents = PosAgent::orderBy('agentName', 'ASC')->leftJoin('pos_lists', 'pos_agents.asignedPos', '=', 'pos_lists.posId')
                    ->leftJoin('point_of_sales', 'pos_agents.asignedLocation', '=', 'point_of_sales.locationId')
                    ->where(function ($query) use ($search) {
                        $query->orWhere('agentName', 'LIKE', '%' . $search . '%');
                        $query->orWhere('userName', 'LIKE', '%' . $search . '%');
                    })->where('agentStatus',1)
                    ->paginate(10, [
                        'agentId',
                        'agentName',
                        'mobileNumber',
                        'locationName',
                        'posName',
                        'agentStatus',
                        'userName',
                        'asignedLocation',
                        'asignedEvent',
                        'asignedPos',
                        'asignedLocation',
                        'lastLogin','firstLogin'
                    ]);
            }

            $locations = PointOfSale::where('locationStatus', 1)->get(['locationId', 'locationName']);
            $pos = PosList::where('posStatus', 1)->get(['posId', 'posName']);
            return view('Tickets.PosAgents', compact('agents', 'locations', 'pos'));
        } catch (\Throwable $th) {
            return back()->with('fail', 'something went wrong!' . $th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // PosTransaction::where('ticketDate','LIKE','%2024-11-21%')->limit(766)->delete();
        $agents = PosAgent::get(['agentName', 'agentId']);
        return view('Tickets.UploadTickets', compact('agents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Manage Agent', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        try {
            PosAgent::create([
                'agentId' => Str::uuid(),
                'agentName' => $request->agentName,
                'userName' => $request->userName,
                'mobileNumber' => $request->mobileNumber,
                'asignedPos' => $request->asignedPos,
                'asignedLocation' => $request->asignedLocation,
                'asignedEvent' => session('liveEvent') ?? config('global.LIVE_EVENT_ID'),
                'agentPassword' => md5($request->mobileNumber),
                'agentToken' => Str::random(40),
                'adminstrativeCompany' => config('global.ADMIN_COMPANY_ID') ?? optional(PosAgent::first())->adminstrativeCompany,
                'agentStatus' => 1,
            ]);
            return back()->with('success', 'Agent added successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong!' . $th->getMessage());
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
        $date = date('Y-m-d');
        // $date='2024-07-07';
        if ($request->transactionDate) {
            $date = $request->transactionDate;
        }
        session(['date' => $date]);
        $agents = PosAgent::leftJoin('pos_lists', 'pos_agents.asignedPos', '=', 'pos_lists.posId')
            ->leftJoin('point_of_sales', 'pos_agents.asignedLocation', '=', 'point_of_sales.locationId')
            ->leftJoin('events', 'pos_agents.asignedEvent', '=', 'events.event_id')
            ->leftJoin('companies', 'pos_agents.adminstrativeCompany', '=', 'companies.companyID')
            ->where('agentId', $id)
            ->get([
                'agentId',
                'agentName',
                'mobileNumber',
                'event_name',
                'locationName',
                'posName',
                'event_name',
                'agentStatus',
                'userName',
                'asignedLocation',
                'asignedEvent',
                'asignedPos',
                'asignedLocation',
                'asignedEvent',
                'adminstrativeCompany',
                'companyName'
            ]);

        $collections = PosTransaction::leftJoin('vip_ticket_packages', 'pos_transactions.serviceId', '=', 'vip_ticket_packages.vip_package_id')
            ->leftJoin('pos_agents', 'pos_transactions.printedBy', '=', 'pos_agents.agentId')
            ->where('printedBy', $id)->where('ticketDate', 'LIKE', '%' . $date . '%')->paginate(10);

        $totalCollections = PosTransaction::where('printedBy', $id)->where('ticketDate', 'LIKE', '%' . $date . '%')->sum('ticketAmount');
        $totalDemaged = PosTransaction::where('printedBy', $id)->where('ticketDate', 'LIKE', '%' . $date . '%')->where('ticketStatus', 0)->sum('ticketAmount');
        $exactlyCollected = PosTransaction::where('printedBy', $id)->where('ticketDate', 'LIKE', '%' . $date . '%')->where('ticketStatus', 1)->sum('ticketAmount');
        session(['agentId' => $id]);
        return view('Tickets.AgentCollections', compact('agents', 'collections', 'totalCollections', 'totalDemaged', 'exactlyCollected'));
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
        if ($request->user()->cannot('Manage Agent', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        try {
            $agent = PosAgent::find($id);
            $agent->update([
                'agentName' => $request->agentName,
                'userName' => $request->userName,
                'mobileNumber' => $request->mobileNumber,
                'asignedPos' => $request->asignedPos,
                'asignedLocation' => $request->asignedLocation,
                'agentStatus' => $request->agentStatus,
                'agentPassword'=>md5($request->mobileNumber)
            ]);
            return back()->with('success', 'Agent Updated successfull!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'something went wrong!');
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
        if ($request->user()->cannot('Manage Agent', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $agent = PosAgent::find($id);
        if (!$agent) {
            return back()->with('fail', 'Agent not found.');
        }

        // An agent referenced by any transaction (as printer or updater) cannot
        // be hard-deleted without losing sales history, so deactivate instead.
        // Deactivated agents (agentStatus = 0) drop off the list, which only
        // shows active agents.
        $hasRecords = PosTransaction::where('printedBy', $id)
            ->orWhere('updatedBy', $id)->exists();

        if ($hasRecords) {
            $agent->update(['agentStatus' => 0]);
            return back()->with('success', 'Agent has recorded sales — deactivated and hidden from the list instead of deleted (history preserved).');
        }

        try {
            $agent->delete();
            return back()->with('success', 'Agent deleted successfully!');
        } catch (\Throwable $th) {
            // Any other constraint: fall back to deactivating rather than failing silently.
            $agent->update(['agentStatus' => 0]);
            return back()->with('success', 'Agent could not be fully deleted, so it was deactivated and hidden from the list instead.');
        }
    }

    public function uploadTransactions(Request $request)
    {
        if ($request->user()->cannot('Upload Ticket', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $request->validate([
            'ticketFile' => 'required|file|mimes:xlsx,csv'
        ]);
        try {
            Excel::import(new TicketImport, $request->file('ticketFile'));
            return back()->with('success', 'Ticket uploaded successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', 'Something went wrong' . $th->getMessage());
        }
    }
}
