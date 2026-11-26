<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\booking\booked_pavilions;
use App\Models\Collections\PaymentDetails;
use App\Models\companies;
use App\Models\setting\ExchangeRate;
use App\Models\settings\events;
use App\Models\space\booths;
use App\Models\space\pavilions;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class invoiceReportController extends Controller
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
        if ($request->user()->cannot('MANAGE_INVOICE_REPORTS', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_INVOICE_REPORTS'));
        }

        $events = events::orderBy('event_created_at', 'DESC')
            ->get(['event_id', 'event_name']);
        $spaces = pavilions::whereIn('pavilionID', booths::get(['pavilionID']))
            ->where('space_status', 'UNRESERVED')->get(['pavilionID', 'pavilionName']);

        $event = $request->event;

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        if ($request->pavilion == 1) {
            $data = companies::leftJoin('advice_notes', 'companies.companyID', '=', 'advice_notes.company_id')
                ->leftJoin('booked_pavilions', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->leftJoin('pavilions', 'booked_pavilions.pavilionID', '=', 'pavilions.pavilionID')
                ->where('control_number', '!=', Null)
                ->where('advice_note_status', '!=', 'CANCELED')
                ->whereIn('booked_pavilions.pavilionID', pavilions::get('pavilionID'))
                ->where('advice_notes.event', $event)
                ->whereBetween('advice_note_created_at', [$fromDate, $toDate])
                ->get();
        } elseif ($request->pavilion == 2) {
            $data = advice_notes::leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->where('advice_notes.event', $event)
                ->where('advice_note_status', '!=', 'CANCELED')
                ->whereBetween('advice_note_created_at', [$fromDate, $toDate])
                ->where('control_number', '!=', Null)->get();
        } elseif ($request->pavilion == 3) {
            # code...
        } else {
            $data = companies::leftJoin('advice_notes', 'companies.companyID', '=', 'advice_notes.company_id')
                ->leftJoin('booked_pavilions', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->leftJoin('booths', 'booked_pavilions.pavilionID', '=', 'booths.boothID')
                ->leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
                ->where('booths.pavilionID', $request->pavilion)
                ->where('control_number', '!=', Null)
                ->where('advice_note_status', '!=', 'CANCELED')
                ->where('advice_notes.event', $event)
                ->whereBetween('advice_note_created_at', [$fromDate, $toDate])
                ->get();
        }


        session(['invoiceData' => $data]);

        return view('reports.invoice_report', compact('data', 'events', 'spaces'));
    }

    public function store(Request $request)
    {
        $data = session('invoiceData');
        $rate = ExchangeRate::where('currencyCode', 'USD')->first()->SELLING;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'TANZANIA TRADE DEVELOPMENT AUTHORITY
        INVOICES REPORT - ' . date('M d, Y') . '. 1 USD = ' . $rate . ' TZS');

        $sheet->setCellValue('A2', 'INVOICE NUMBER');
        $sheet->setCellValue('B2', 'COMPANY NAME');
        $sheet->setCellValue('C2', 'INVOICE AMOUNT (TZS)');
        $sheet->setCellValue('D2', 'AMOUNT PAID (TZS)');
        $sheet->setCellValue('E2', 'EXPIRE DATE');



        $i = 3;
        foreach ($data as   $value) {
            $deadline = date('d-m-Y', strtotime($value->advice_note_deadline));
            $sheet->setCellValue('A' . $i, $value->advice_note_id);
            $sheet->setCellValue('B' . $i, $value->companyName);
            if ($value->currency == 'USD') {
                $sheet->setCellValue('C' . $i, $value->total_amount * $rate);
                $sheet->setCellValue('D' . $i, $value->amount_paid * $rate);
            } else {
                $sheet->setCellValue('C' . $i, $value->total_amount);
                $sheet->setCellValue('D' . $i, $value->amount_paid);
            }
            $sheet->setCellValue('E' . $i, $deadline);
            if ($value->total_amount <= $value->amount_paid) {
                $sheet->getStyle('A' . $i . ':E' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('75e897');
            } elseif (date('Ymd') > date('Ymd', strtotime($deadline))) {
                $sheet->getStyle('A' . $i . ':E' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('f6f63a');
            }
            $i++;
        }
        $lastCell = $i + 1;
        $sheet->getStyle('A' . $lastCell . ':E' . $lastCell)->getFont()->setBold(true);
        $sheet->getStyle('A' . $lastCell . ':E' . $lastCell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c2c0ef');
        $sheet->getStyle('A' . $lastCell . ':E' . $lastCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('C' . $lastCell, '=SUM(C3:C' . $i . ')');
        $sheet->getStyle('C3:C' . $lastCell)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->setCellValue('D' . $lastCell, '=SUM(D3:D' . $i . ')');
        $sheet->getStyle('D3:D' . $lastCell)->getNumberFormat()->setFormatCode('#,##0.00');

        // Set some data to be displayed in Excel


        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('e1faf9');
        $sheet->getStyle('A1:E' . $i + 1)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:E2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getRowDimension(1)->setRowHeight(50);
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('images/nembo.png')); // Path to the image
        $drawing->setHeight(50); // Set image height
        $drawing->setCoordinates('A1'); // Place the image in cell D1
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->getRowDimension(1)->setRowHeight(50);
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('images/Logo.png')); // Path to the image
        $drawing->setHeight(50); // Set image height
        $drawing->setCoordinates('E1'); // Place the image in cell D1
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:E1')->getFont()->setSize(15);

        $writer = new Xlsx($spreadsheet);
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="invoiceReport_"' . date('d_m_Y') . '.xlsx',
            'Cache-Control' => 'max-age=0',
        ]);
    }



    public function collectionReport(Request $request)
    {
        if ($request->user()->cannot('MANAGE_COLLECTION_REPORTS', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_COLLECTION_REPORTS'));
        }
        $events = events::orderBy('event_status', 'DESC')
            ->orderBy('event_created_at', 'DESC')
            ->get(['event_id', 'event_name']);

        $event = $request->event;
        $data = [];
        if ($event) {
            $data = PaymentDetails::leftJoin('advice_notes', 'payment_details.referenceId', 'advice_notes.advice_note_id')
                ->leftJoin('companies', 'advice_notes.company_id', '=', 'companies.companyID')
                ->where('advice_notes.event', $event)
                ->get();
        }


        session(['collectionData' => $data]);
        return view('reports.exhibition_collection_report', compact('data', 'events'));
    }

    public function ExportCollectionReport(Request $request)
    {
        $data = session('collectionData');
        $rate = ExchangeRate::where('currencyCode', 'USD')->first()->SELLING;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'TANZANIA TRADE DEVELOPMENT AUTHORITY
        COLLECTION REPORT UP TO ' . date('M d, Y') . '. 1 USD = ' . $rate . ' TZS');

        $sheet->setCellValue('A2', 'INVOICE NUMBER');
        $sheet->setCellValue('B2', 'COMPANY NAME');
        $sheet->setCellValue('C2', 'INVOICE AMOUNT (TZS)');
        $sheet->setCellValue('D2', 'AMOUNT PAID (TZS)');
        $i = 3;
        foreach ($data as   $value) {
            $deadline = date('d-m-Y', strtotime($value->advice_note_deadline));
            $sheet->setCellValue('A' . $i, $value->advice_note_id);
            $sheet->setCellValue('B' . $i, $value->companyName);
            if ($value->currency == 'USD') {
                $sheet->setCellValue('C' . $i, $value->total_amount * $rate);
                $sheet->setCellValue('D' . $i, $value->amount_paid * $rate);
            } else {
                $sheet->setCellValue('C' . $i, $value->total_amount);
                $sheet->setCellValue('D' . $i, $value->amount_paid);
            }
            if ($value->total_amount <= $value->amount_paid) {
                $sheet->getStyle('A' . $i . ':D' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('75e897');
            } elseif (date('Ymd') > date('Ymd', strtotime($deadline))) {
                $sheet->getStyle('A' . $i . ':D' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('f6f63a');
            }
            $i++;
        }
        $lastCell = $i + 1;
        $sheet->getStyle('A' . $lastCell . ':D' . $lastCell)->getFont()->setBold(true);
        $sheet->getStyle('A' . $lastCell . ':D' . $lastCell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c2c0ef');
        $sheet->getStyle('A' . $lastCell . ':D' . $lastCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('C' . $lastCell, '=SUM(C3:C' . $i . ')');
        $sheet->getStyle('C3:C' . $lastCell)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->setCellValue('D' . $lastCell, '=SUM(D3:D' . $i . ')');
        $sheet->getStyle('D3:D' . $lastCell)->getNumberFormat()->setFormatCode('#,##0.00');

        // Set some data to be displayed in Excel


        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('e1faf9');
        $sheet->getStyle('A1:D' . $i + 1)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getRowDimension(1)->setRowHeight(50);
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('images/nembo.png')); // Path to the image
        $drawing->setHeight(50); // Set image height
        $drawing->setCoordinates('A1'); // Place the image in cell D1
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->getRowDimension(1)->setRowHeight(50);
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('images/Logo.png')); // Path to the image
        $drawing->setHeight(50); // Set image height
        $drawing->setCoordinates('D1'); // Place the image in cell D1
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:D1')->getFont()->setSize(15);

        $writer = new Xlsx($spreadsheet);
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="collectionReport_"' . date('d_m_Y') . '.xlsx',
            'Cache-Control' => 'max-age=0',
        ]);
    }


    public function generalReport(Request $request)
    {
        try {
            $pavilions = pavilions::where('space_status', 'UNRESERVED')
                ->leftJoin('booked_pavilions', 'pavilions.pavilionID', '=', 'booked_pavilions.pavilionID')
                ->leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->where('advice_note_status', '!=', 'CANCELED')
                ->orderBy('spaceSize', 'DESC')->get([
                    'pavilions.pavilionID',
                    'pavilionName as spaceName',
                    'pavilion_size as spaceSize',
                    'total_amount as invoiceAmount',
                    'amount_paid as amountPaid',
                    'currency',
                    'pavilion_size as bookedSpace',
                    'advice_notes.advice_note_id as invoiceId',
                ]);


            $booths = booths::leftJoin('booked_pavilions', 'booths.boothID', '=', 'booked_pavilions.pavilionID')
                ->leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
                ->leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->where('booths.bookingStatus', 'UNRESERVED')
                ->where('advice_note_status', '!=', 'CANCELED')
                ->get([
                    'booths.boothID',
                    'booths.pavilionID',
                    'boothNo as spaceName',
                    'boothSize as spaceSize',
                    'total_amount as invoiceAmount',
                    'amount_paid as amountPaid',
                    'currency',
                ]);

            $pavilionList = pavilions::leftJoin('booths', 'pavilions.pavilionID', '=', 'booths.pavilionID')
                ->leftJoin('booked_pavilions', 'booths.boothID', '=', 'booked_pavilions.pavilionID')
                ->leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->where('advice_note_status', '!=', 'CANCELED')
                ->where('space_status', 'UNRESERVED')
                ->whereIn('pavilions.pavilionID', booths::where('bookingStatus', 'UNRESERVED')->get(['pavilionID']))
                ->get([
                    'pavilions.pavilionID',
                    'pavilionName as spaceName',
                    'pavilion_size as spaceSize',
                    'total_amount as invoiceAmount',
                    'amount_paid as amountPaid',
                    'currency',
                ])->unique('pavilionID');
            foreach ($pavilionList as $item) {
                $pavilionID = $item->pavilionID;
                $pavilionName = $item->spaceName;
                $pavilionSize = $item->spaceSize;
                $invoiceAmount = $item->invoiceAmount;
                $amountPaid = $item->amountPaid;
                $currency = $item->currency;
                $bookedSpace = 0;
                foreach ($booths as  $value) {
                    if ($value->pavilionID == $pavilionID) {
                        $bookedSpace += $value->spaceSize;
                    }
                    
                }
                $booking[] = [
                    'pavilionID' => $item->pavilionID,
                    'spaceName' => $item->spaceName,
                    'spaceSize' => $item->spaceSize,
                    'invoiceAmount' => $item->invoiceAmount,
                    'amountPaid' => $item->amountPaid,
                    'currency' => $item->currency,
                    'bookedSpace' => $bookedSpace,
                    'numberOfPartitions'=> booths::where('pavilionID', $pavilionID)
                        ->where('bookingStatus', 'UNRESERVED')
                        ->count(),
                ];
            }

              $data = $pavilions;
            $events = events::orderBy('event_created_at', 'DESC')->get(['event_id', 'event_name']);
            return view('reports.GeneralReport', compact('pavilions', 'events', 'data'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500').$th->getMessage());
        }
    }
}
