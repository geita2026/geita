<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_sponsorship_packages;
use App\Models\setting\ExchangeRate;
use App\Models\settings\events;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SponsorShipReportController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth');
    }
    public function loadSponsorshipData(Request $request){
        try {
            if ($request->user()->cannot('Manage SponsorshipReports', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $events=events::orderBy('event_created_at','DESC')->get(['event_id','event_name']);
            $event=$request->event;
            $data=booked_sponsorship_packages::leftJoin('advice_notes','booked_sponsorship_packages.advice_note_id','=','advice_notes.advice_note_id')
            ->leftJoin('companies','booked_sponsorship_packages.customer_id','=','companies.companyID')
            ->leftJoin('sponsorship_packages','booked_sponsorship_packages.package_id','=','sponsorship_packages.package_id')
            ->where('exhibition_year',$event)
            ->where('advice_note_status','APPROVED')
            ->get([
                'companies.companyName',
                'package_name',
                'control_number',
                'total_amount',
                'amount_paid',
                'balance',
                'currency',
                'advice_note_description',
                'advice_note_deadline'
            ]);
            session(['sponsorshipReport'=>$data]);
            return view('reports.SponsorshipReports',compact('data','events'));
        } catch (\Throwable $th) {
            return back()->with('fail',config('global.200'));
        }
    }


    public function ExportSponsorshipReport(Request $request){
        $data = session('sponsorshipReport');
        $rate = ExchangeRate::where('currencyCode', 'USD')->first()->SELLING;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'TANZANIA TRADE DEVELOPMENT AUTHORITY
        SPONSORSHIP REPORT - ' . date('M d, Y') . '. 1 USD = ' . $rate . ' TZS');

        $sheet->setCellValue('A2', 'COMPANY NAME');
        $sheet->setCellValue('B2', 'PACKAGE NAME');
        $sheet->setCellValue('c2', 'CONTROL NUMBER');
        $sheet->setCellValue('D2', 'INVOICE AMOUNT (TZS)');
        $sheet->setCellValue('E2', 'AMOUNT PAID (TZS)');
        $sheet->setCellValue('F2', 'BENEFITS');
        $sheet->setCellValue('G2', 'DEADLINE');
        $i = 3;
        foreach ($data as   $value) {
            $deadline = date('d-m-Y', strtotime($value->advice_note_deadline));
            $sheet->setCellValue('A' . $i, $value->companyName);
            $sheet->setCellValue('B' . $i, $value->package_name);
            $sheet->setCellValue('C' . $i, $value->control_number);
            if ($value->currency == 'USD') {
                $sheet->setCellValue('D' . $i, $value->total_amount * $rate);
                $sheet->setCellValue('E' . $i, $value->amount_paid * $rate);
            } else {
                $sheet->setCellValue('D' . $i, $value->total_amount);
                $sheet->setCellValue('E' . $i, $value->amount_paid);
            }
            $sheet->setCellValue('F' . $i, $value->advice_note_description);
            $sheet->setCellValue('G' . $i, $value->advice_note_deadline);
            if ($value->total_amount <= $value->amount_paid) {
                $sheet->getStyle('A' . $i . ':G' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('75e897');
            } elseif (date('Ymd') > date('Ymd', strtotime($deadline))) {
                $sheet->getStyle('A' . $i . ':G' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('f6f63a');
            }

            
            $i++;
        }
        $lastCell = $i + 1;
        $sheet->getStyle('A' . $lastCell . ':G' . $lastCell)->getFont()->setBold(true);
        $sheet->getStyle('A' . $lastCell . ':G' . $lastCell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c2c0ef');
        $sheet->getStyle('A' . $lastCell . ':G' . $lastCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('D' . $lastCell, '=SUM(D3:D' . $i . ')');
        $sheet->getStyle('D3:D' . $lastCell)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->setCellValue('E' . $lastCell, '=SUM(E3:E' . $i . ')');
        $sheet->getStyle('E3:E' . $lastCell)->getNumberFormat()->setFormatCode('#,##0.00');

        // Set some data to be displayed in Excel


        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('e1faf9');
        $sheet->getStyle('A1:G' . $i + 1)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getRowDimension(1)->setRowHeight(50);
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('images/nembo.png')); // Path to the image
        $drawing->setHeight(50); // Set image height
        $drawing->setCoordinates('A1'); // Place the image in cell D1
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $sheet->getColumnDimension('G')->setWidth(50);
        $sheet->getRowDimension(1)->setRowHeight(50);
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('images/Logo.png')); // Path to the image
        $drawing->setHeight(50); // Set image height
        $drawing->setCoordinates('G1'); // Place the image in cell D1
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:G1')->getFont()->setSize(15);

        $writer = new Xlsx($spreadsheet);
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="SponsorshipReport_"' . date('d_m_Y') . '.xlsx',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
