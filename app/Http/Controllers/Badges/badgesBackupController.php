<?php

namespace App\Http\Controllers\Badges;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\settings\events;
use App\Models\space\additional_services;
use App\Models\Stickers\CarSticker;
use App\Models\Tickets\ProducedTicket;
use App\Models\Tickets\VipTicketBookings;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class badgesBackupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
        $url=config('global.BACKUP_BASE_URL').date('d_m_y');
        $response=Http::get($url);
        $badges=$response['badges'];
        $totaltBadges=$response['totaltBadges'];   
        return view('Badges.BadgeBackup',compact('totaltBadges','badges'));
        }
        catch (\Throwable $th) {
            return back()->with('fail', 'Soory, Backup Failed for this image!');
        } 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $url = config('global.BACKUP_BASE_URL') . date('d_m_y2');
            $response = Http::get($url);
            $stickers = $response['stickers'];
            $services = $response['services'];

            foreach ($stickers as  $value) {
                ### ---- Checking if company Already registered----##
                $exist = companies::find($value['company_id']);
                if (!$exist) {
                    companies::create([
                        'companyID' => $value['company_id'],
                        'companyName' => $value['companyName'],
                        'registrationID' => $value['registrationID'],
                        'status' => $value['status'],
                    ]);
                }
                $event = events::find($value['event_id']);
                if (!$event) {
                    events::create([
                        'event_id' => $value['bookingEvent'],
                        'event_name' => '48th Dar es Salaam International Trade Fair',
                        'event_location' => 'Dar Es Salaam',
                        'event_start_date' => '2024-06-28',
                        'event_end_date' => '2024-07-13',
                        'event_descriptions' => 'welcome',
                        'event_status' => '1',
                    ]);
                }

                foreach ($services as $serv) {
                    $serviceId = additional_services::find($serv['serviceID']);
                    if (!$serviceId) {
                        additional_services::create([
                            'serviceID' => $serv['serviceID'],
                            'userID' => '1ad83f3b-0991-48aa-a472-dc175d3745ac',
                            'serviceName' => $serv['serviceName'],
                            'serviceCost' => $serv['serviceCost'],
                            'serviceDescription' => $serv['serviceDescription'],
                            'type_of_currency' => $serv['type_of_currency'],
                            'service_priolity' => $serv['service_priolity'],
                            'service_type' => $serv['service_type'],
                            'service_event' => $serv['service_event'],
                            'gfs_code' => $serv['gfs_code'],
                            'is_printable' => $serv['is_printable'],
                            'self_booking' => $serv['self_booking'],
                            'relateToSpaceSize' => $serv['relateToSpaceSize']
                        ]);
                    }
                }
                $sticker = CarSticker::find($value['sticker_id']);
                ##### ---- End checking Compay Registration ---- #######
                if (!$sticker) {
                    CarSticker::create([
                        'sticker_id' => $value['sticker_id'],
                        'company_id' => $value['company_id'],
                        'event_id' => $value['event_id'],
                        //  'printed_by' => $value['printed_by'],
                        'reference_sticker' => $value['reference_sticker'],
                        'plate_number' => $value['plate_number'],
                        'design_group' => $value['design_group'],
                        'sticker_status' => 1,
                    ]);
                }
            }
            $id = $value['sticker_id'];
            #--------------- END BACK UP ------ #
            $response = Http::get(config('global.BACKUP_BASE_URL') . date('d_m_y2') . '/' . $id);
            if ($response['status'] == 1) {
                return  response()->json([
                    'status' => 1,
                    'message' => $response['message'],
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong!' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $value
     * @return \Illuminate\Http\Response
     */
    public function store(Request $value)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    { 

        if (config('global.BACKUP')==1) {
            try {
                $url=config('global.BACKUP_BASE_URL').date('d_m_y');
                $response=Http::get($url);
                $badges=$response['badges'];
        
                foreach ($badges as $value) {
                    ### ---- Checking if company Already registered----##
                    $exist=companies::find($value['company_id']);
                    if (!$exist) {
                        companies::create([
                            'companyID'=>$value['company_id'],
                            'companyName'=>$value['companyName'],
                            'registrationID'=>$value['registrationID'],
                            'status'=>$value['status'],
                        ]);
                    }
                    ##### ---- End checking Compay Registration ---- #######
        
                    #------ Getting Image from the json ----#
                    $client = new Client();
                    $imageUrl='https://tradefair.tantrade.go.tz/public/Members/'.$value['member_image'];
                    $response1 = $client->get($imageUrl);
                    $path='/Members/'.basename($imageUrl);
                    Storage::disk('public')->put($path, $response1->getBody());
                    #------- Finish Moving image to storage ------#
        
                    $event=events::where('event_status',1)->first()->event_id;
                    $exist = CompanyMember::find($value['member_id']);
                    if ($value['event']==$event) {
                        $event=$value['event'];
                    }
                    if (!$exist) {
                        CompanyMember::create([
                            'member_id' => $value['member_id'],
                            'company_id' => $value['company_id'],
                            'first_name' => $value['first_name'],
                            'middle_name' => $value['middle_name'],
                            'last_name' => $value['last_name'],
                            'member_group' => $value['member_group'],
                            'member_image' => $value['member_image'],
                            'badge_status' => $value['badge_status'],
                            'event' => $event,
                            'phone_number' => $value['phone_number'],
                            'blocked_by' => $value['blocked_by'],
                            'unblocked_by' => $value['unblocked_by'],
                            'scan_count' => $value['scan_count'],
                        ]);
                    } else {
                        $exist->update([
                            'first_name' => $value['first_name'],
                            'middle_name' => $value['middle_name'],
                            'last_name' => $value['last_name'],
                            'member_group' => $value['member_group'],
                            'member_image' => $value['member_image'],
                            'badge_status' => $value['badge_status'],
                            'event' => $event,
                            'phone_number' => $value['phone_number'],
                            'blocked_by' => $value['blocked_by'],
                            'unblocked_by' => $value['unblocked_by'],
                            'scan_count' => $value['scan_count'],
                        ]);
                    } 
                }
        
                #--------------- END BACK UP ------ #
                    $response = Http::get(config('global.BACKUP_BASE_URL') . date('d_m_y') . '/' . $id);
                    if ($response['status'] == 1) {
                        return back()->with('success', $response['message']);
                    }
                    return back()->with('fail', 'Something went wrong!');
                } catch (\Throwable $th) {
                    return back()->with('fail', 'Soory, Backup Failed for this image!');
                } 
        }
        else{
            return back()->with('fail','Backup not allowed for this badges!');
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
        try {
            $url = config('global.BACKUP_BASE_URL') . date('d_m_y3');
            $response = Http::get($url);
            $tickets = $response['tickets'];
            $balance=$response['pending'];


            foreach ($tickets as  $value) {
                $booking = VipTicketBookings::find($value['bookingId']);
                if (!$booking) {
                    VipTicketBookings::create([
                        'ticket_booking_id' => $value['ticket_booking_id'],
                        'customer_name' => $value['customer_name'],
                        'phone_number' => $value['phone_number'],
                        'control_number' => $value['control_number'],
                        'payment_deadline' => $value['payment_deadline'],
                        'amount_paid' => $value['amount_paid'],
                        'booked_package' => $value['booked_package'],
                        'ticket_amount' => $value['ticket_amount'],
                        'currency' => $value['currency'],
                        'sp_customer_no' => $value['booked_package'],
                        'gfs_code_mapping' => $value['gfs_code_mapping'],
                        'sp_invoice_no' => $value['sp_invoice_no'],
                        'bookingEvent' => $value['bookingEvent']
                    ]);
                }
                $id=$value['ticketId'];
                # add only which does not exists.
                $ticket = ProducedTicket::find($value['ticketId']);
                if (!$ticket) {
                    ProducedTicket::create([
                        'ticketId' => $value['ticketId'],
                        'bookingId' => $value['bookingId'],
                        'ticketNo' => $value['ticketNo'],
                        'ticketStatus' => $value['ticketStatus'],
                        'ticketAmount' => $value['ticketAmount'],
                        'plateNumber' => $value['plateNumber'],
                        'customerName' => $value['customerName'],
                        'visitDate' => $value['visitDate'],
                        'ticketName' => $value['ticketName'],
                    ]);
                    $id = $value['ticketId'];
                    #--------------- END BACK UP ------ #
                    $response = Http::get(config('global.BACKUP_BASE_URL') . date('d_m_y3') . '/' . $id);
                    if ($response['status'] == 1) {
                         $response['message'];
                    }
                } else {
                    $ticket->update([
                        'ticketStatus' => $value['ticketStatus'],
                    ]);
                }
              return  $response = Http::get(config('global.BACKUP_BASE_URL') . date('d_m_y3') . '/' . $id);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong!' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $value
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $value, $id)
    {
        //
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
