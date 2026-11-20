<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\advice_note\particulars;
use App\Models\booking\booked_pavilions;
use App\Models\companies;
use App\Models\setting\ExchangeRate;
use App\Models\settings\events;
use App\Models\space\booths;
use App\Models\space\pavilions;
use App\Models\space\space_categories;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class bookedUnbookedReportController extends Controller
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
    public function index(Request $request)
    {
        if ($request->user()->cannot('view-space-booking-report', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $event = $request->event;
        $pavilionCategories = space_categories::where('category_event', session('liveEvent'))
            ->where('category_name', '!=', 'Local Shared Pavilions')->get();
        $data = [];
        $events = events::orderBy('event_status', 'DESC')->get(['event_id', 'event_name']);
        $spaces = pavilions::whereIn('pavilionID', booths::where('bookingStatus', 'UNRESERVED')->get(['pavilionID']))
            ->where('space_status', 'UNRESERVED')->get(['pavilionID', 'pavilionName']);

        $typeOfPavilions = $request->typeOfPavilions;
        $pavilionSelected = pavilions::find($typeOfPavilions); # if pavilions showup
        $categorySelected = space_categories::find($typeOfPavilions); # If space category showup too

        if ($pavilionSelected) {
            $data = booths::where('booths.pavilionID', $pavilionSelected->pavilionID)
                ->leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
                ->whereIn('booths.boothID', booked_pavilions::where('exhibition_year', $event)->get('pavilionID'))
                ->leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
                ->leftJoin('booked_pavilions', 'booths.boothID', '=', 'booked_pavilions.pavilionID')
                ->leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('exhibitors','booked_pavilions.companyID','=','exhibitors.exhibitor_id')
                ->where('exhibitor_event',session('liveEvent'))
                ->where('advice_note_status', 'APPROVED')
                ->where('event', $event)
                ->get([
                    'companyName',
                    'boothNo as partitionName',
                    'boothSize as spaceSize',
                    'category_name',
                    'control_number',
                    'pavilionName as spaceName',
                    'total_amount',
                    'amount_paid',
                    'balance',
                    'currency',
                    'registrationID',
                    'advice_notes.advice_note_id',
                    'advice_note_deadline',
                    'companyEmail',
                    'companyPhone',
                    'companyLocation',
                    'exhibitedProducts'
                ]);
        } elseif ($categorySelected) {
            $pavilion = pavilions::where('pavilionCategory', $categorySelected->category_id)
                ->whereIn('pavilions.pavilionID', booked_pavilions::where('exhibition_year', $event)->get('pavilionID'))
                ->leftJoin('space_categories','pavilions.pavilionCategory','=','space_categories.category_id')
                ->leftJoin('booths','pavilions.pavilionID','=','booths.pavilionID')
                ->leftJoin('booked_pavilions', 'pavilions.pavilionID', '=', 'booked_pavilions.pavilionID')
                ->leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->leftJoin('exhibitors','booked_pavilions.companyID','=','exhibitors.exhibitor_id')
                ->where('exhibitor_event',session('liveEvent'))
                ->where('advice_note_status', 'APPROVED')
                ->where('event', $event)
                ->where('pavilions.pavilionCategory', $categorySelected->category_id)
                ->orderBy('pavilionName')
                ->get([
                    'companyName',
                    'boothNo as partitionName',
                    'pavilion_size as spaceSize',
                    'category_name',
                    'control_number',
                    'pavilionName as spaceName',
                    'total_amount',
                    'amount_paid',
                    'balance',
                    'currency',
                    'registrationID',
                    'advice_notes.advice_note_id',
                    'advice_note_deadline',
                    'companyEmail',
                    'companyPhone',
                    'companyLocation',
                    'exhibitedProducts'
                ]);

             $withBooths = booths::leftJoin('booth_categories', 'booths.booth_category', '=', 'booth_categories.booth_category_id')
                ->whereIn('booths.boothID', booked_pavilions::where('exhibition_year', $event)->get('pavilionID'))
                ->leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
                ->leftJoin('booked_pavilions', 'booths.boothID', '=', 'booked_pavilions.pavilionID')
                ->leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
               ->leftJoin('exhibitors','booked_pavilions.companyID','=','exhibitors.exhibitor_id')
               ->where('advice_note_status', 'APPROVED')
               ->where('exhibitor_event',session('liveEvent'))
                ->where('event', $event)
                ->where('pavilions.pavilionCategory', $categorySelected->category_id)
                ->orderBy('pavilionName')
                ->orderBy('boothNo')
                ->get([
                    'companyName',
                    'boothNo as partitionName',
                    'boothSize as spaceSize',
                    'category_name',
                    'control_number',
                    'pavilionName as spaceName',
                    'total_amount',
                    'amount_paid',
                    'balance',
                    'currency',
                    'registrationID',
                    'advice_notes.advice_note_id',
                    'advice_note_deadline',
                    'companyEmail',
                    'companyPhone',
                    'companyLocation',
                    'exhibitedProducts'
                ]);

            foreach ($withBooths as  $value) {
                $pavilion[] = [
                    "companyName" => $value->companyName,
                    "partitionName" => $value->partitionName,
                    "spaceSize" => $value->spaceSize,
                    "category_name"=>$value->category_name,
                    "control_number" => $value->control_number,
                    "spaceName" => $value->spaceName,
                    "total_amount" => $value->total_amount,
                    "amount_paid" => $value->amount_paid,
                    "balance" => $value->balance, // will match total_amount later
                    "currency" => $value->currency,
                    "registrationID" => $value->registrationID,
                    "advice_note_id" => $value->advice_note_id,
                    "advice_note_deadline" => $value->advice_note_deadline,
                    'companyEmail'=>$value->companyEmail,
                    'companyPhone'=>$value->companyPhone,
                    'companyLocation'=>$value->companyLocation,
                    'exhibitedProducts'=>$value->exhibitedProducts
                ];
            }
            $data=json_decode($pavilion);
        } 
        session(['data' => $data]);
        return view('reports.space-report', compact('data', 'events', 'spaces', 'pavilionCategories'));
    }

    public function store(Request $request)
    {
        if ($request->user()->cannot('Manage SpaceReports', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $data = session('data');
        $rate = ExchangeRate::where('currencyCode', 'USD')->first()->SELLING;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'TANZANIA TRADE DEVELOPMENT AUTHORITY
        SPACE BOOKING REPORT - ' . date('M d, Y') . '. 1 USD = ' . $rate . ' TZS');

        $sheet->setCellValue('A2', 'SPACE NAME');
        $sheet->setCellValue('B2', 'SPACE CATEGORY');
        $sheet->setCellValue('C2', 'SPACE SIZE');
        $sheet->setCellValue('D2', 'BOOKED BY');
        $sheet->setCellValue('E2', 'COMPANY PHONE');
        $sheet->setCellValue('F2', 'COMPANY EMAIL');
        $sheet->setCellValue('G2', 'EXPECTED AMOUNT (TZS)');
        $sheet->setCellValue('H2', 'AMOUNT PAID (TZS)');
        $sheet->setCellValue('I2', 'CONTROL NUMBER');
        $sheet->setCellValue('J2', 'EXPIRE DATE');
        $sheet->setCellValue('K2', 'COUNTRY');
        $sheet->setCellValue('L2', 'EXHIBITED PRODUCTS');




        $i = 3;
        foreach ($data as   $value) {
            $deadline = date('d-m-Y', strtotime($value->advice_note_deadline));
            $sheet->setCellValue('A' . $i, $value->partitionName . ' ~ ' . $value->spaceName);
            $sheet->setCellValue('B' . $i, $value->category_name);
            $sheet->setCellValue('C' . $i, $value->spaceSize);
            $sheet->setCellValue('D' . $i, $value->companyName);
            $sheet->setCellValue('E' . $i, $value->companyPhone);
            $sheet->setCellValue('F' . $i, $value->companyEmail);
            if ($value->currency == 'USD') {
                $sheet->setCellValue('G' . $i, $value->total_amount * $rate);
                $sheet->setCellValue('H' . $i, $value->amount_paid * $rate);
            } else {
                $sheet->setCellValue('G' . $i, $value->total_amount);
                $sheet->setCellValue('H' . $i, $value->amount_paid);
            }

            $sheet->setCellValue('I' . $i, $value->control_number);
            $sheet->setCellValue('J' . $i, $deadline);
            $sheet->setCellValue('K' . $i, $value->companyLocation);
            $sheet->setCellValue('L' . $i, $value->exhibitedProducts);
            
            if ($value->total_amount <= $value->amount_paid) {
                $sheet->getStyle('A' . $i . ':L' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('75e897');
            } elseif (date('Ymd') > date('Ymd', strtotime($deadline))) {
                $sheet->getStyle('A' . $i . ':L' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('f6f63a');
            }
            // elseif (date('Ymd') <= date('Ymd',strtotime($deadline))) {
            //     $sheet->getStyle('A'.$i.':H'.$i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            //     ->getStartColor()->setARGB('fff404');  
            // }
            $i++;
        }


        $lastCell = $i + 1;
        $sheet->getStyle('A' . $lastCell . ':L' . $lastCell)->getFont()->setBold(true);
        $sheet->getStyle('A' . $lastCell . ':L' . $lastCell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c2c0ef');
        $sheet->getStyle('A' . $lastCell . ':L' . $lastCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('G' . $lastCell, '=SUM(G3:G' . $i . ')');
        $sheet->getStyle('G3:G' . $lastCell)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->setCellValue('H' . $lastCell, '=SUM(H3:H' . $i . ')');
        $sheet->getStyle('H3:H' . $lastCell)->getNumberFormat()->setFormatCode('#,##0.00');

        // Set some data to be displayed in Excel


        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:L1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('e1faf9');
        $sheet->getStyle('A1:L' . $i + 1)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:L2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getRowDimension(1)->setRowHeight(50);
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('images/nembo.png')); // Path to the image
        $drawing->setHeight(50); // Set image height
        $drawing->setCoordinates('A1'); // Place the image in cell D1
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $sheet->getColumnDimension('L')->setWidth(50);
        $sheet->getRowDimension(1)->setRowHeight(50);
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('images/Logo.png')); // Path to the image
        $drawing->setHeight(50); // Set image height
        $drawing->setCoordinates('L1'); // Place the image in cell D1
        $drawing->setWorksheet($spreadsheet->getActiveSheet());




        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:L1')->getFont()->setSize(15);


        $writer = new Xlsx($spreadsheet);
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="SpaceBookingReport_"' . date('d_m_Y') . '.xlsx',
            'Cache-Control' => 'max-age=0',
        ]);
    }




    public function napaRequest(Request $request)
    {
        #*************** REQUEST *************#
        //     $nidaHelper = new EsbHelper();
        //     $esbHelper = new EsbHelper();
        //   return  $payload = [
        //         'Payload' => [
        //             'clientCode'=>'qLNO8af4yLUSZdNcqiJFy3guPNWc7rsyyx7q8ZSR',
        //             'levelId'=>3,
        //             'id'=>0
        //         ],
        //     ];
        //     $RQCode = $request->RQCode;
        //     $QNANSW = $request->QNANSW;

        //     $endPoint = 'xi8EqFSj';
        //     return  $esbData = $esbHelper->napaRequest($endPoint, $payload, "json");

        #************* END **********#
    }


    public function testFunction()
    {
        // try {
        //     $url = config('global.BACKUP_BASE_URL') . date('d_m_y2');
        //     $response = Http::get($url);
        //     $stickers = $response['stickers'];

        //     foreach ($stickers as  $value) {
        //         ### ---- Checking if company Already registered----##
        //         $exist = companies::find($value['company_id']);
        //         if (!$exist) {
        //             companies::create([
        //                 'companyID' => $value['company_id'],
        //                 'companyName' => $value['companyName'],
        //                 'registrationID' => $value['registrationID'],
        //                 'status' => $value['status'],
        //             ]);
        //         }
        //         $event=events::find($value['event_id']);
        //         if (!$event) {
        //             events::create([
        //                 'event_id' =>$value['bookingEvent'],
        //                 'event_name' =>'48th Dar es Salaam International Trade Fair',
        //                 'event_location' => 'Dar Es Salaam',
        //                 'event_start_date' =>'2024-06-28',
        //                 'event_end_date' =>'2024-07-13' ,
        //                 'event_descriptions' => 'welcome',
        //                 'event_status' => '1',
        //             ]);  
        //         }

        //         $sticker=CarSticker::find($value['sticker_id']);
        //         ##### ---- End checking Compay Registration ---- #######
        //         if (!$sticker) {
        //             CarSticker::create([
        //                 'sticker_id' => $value['sticker_id'],
        //                 'company_id' => $value['company_id'],
        //                  'event_id' => $value['event_id'],
        //                 //  'printed_by' => $value['printed_by'],
        //                 'reference_sticker' => $value['reference_sticker'],
        //                 'plate_number' => $value['plate_number'],
        //                 'design_group' => $value['design_group'],
        //             ]);
        //         }
        //     }
        //     $id = $value['sticker_id'];

        //     #--------------- END BACK UP ------ #
        //     $response = Http::get(config('global.BACKUP_BASE_URL') . date('d_m_y2') . '/' . $id);
        //     if ($response['status'] == 1) {
        //         return back()->with('success', $response['message']);
        //     }
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'status' => 0,
        //         'message' => 'Something went wrong!'.$th->getMessage(),
        //     ]);
        // }
    }


    // $clientId = 128;
    //     $levelId = 4;
    //     $id = 88621;
    //     return   $this->getAllAddress($clientId, $levelId, $id);
    //     return $this->getAddressServices($clientId, $levelId, $id); #services at Level 8
}
