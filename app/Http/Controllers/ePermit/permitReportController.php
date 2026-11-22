<?php

namespace App\Http\Controllers\ePermit;

use App\Http\Controllers\Controller;
use App\Models\ePermit\ePermitApplication;
use App\Models\setting\ExchangeRate;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class permitReportController extends Controller
{
    public function getPermitReports(Request $request){
        try {
            if ($request->user()->cannot('MANAGE_PERMIT_REPORTS', Role::class)) {
                return back()->with('fail', $this->permissionDeniedMessage('MANAGE_PERMIT_REPORTS'));
            }
            $data=ePermitApplication::leftJoin('companies','e_permit_applications.companyId','=','companies.companyID')
            ->leftJoin('application_invoices','e_permit_applications.applicationId','=','application_invoices.referenceApplication')
            ->leftJoin('trade_promotion_events','e_permit_applications.applicationId','=','trade_promotion_events.eventApplicationId')
            ->leftJoin('promotion_event_types','trade_promotion_events.tradeEventType','=','promotion_event_types.eventTypeId')
            ->where('applicationStatus',$request->applicationStatus)
            ->get();
            session(['permitData'=>$data]);
            return view('ePermit.Reports.PermitIssued',compact('data'));
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500'));
        }
    }

    public function ExportPermitReport(Request $request)
    {
        $data = session('permitData');
        $rate = ExchangeRate::where('currencyCode', 'USD')->first()->SELLING;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'TANZANIA TRADE DEVELOPMENT AUTHORITY
        TRADE PROMOTION EVENT PERMIT APPLICATIONS REPORT - ' . date('M d, Y') . '. 1 USD = ' . $rate . ' TZS');

        $sheet->setCellValue('A2', 'COMPANY NAME');
        $sheet->setCellValue('B2', 'EVENT TITLE');
        $sheet->setCellValue('C2', 'EVENT VENUE');
        $sheet->setCellValue('D2', 'EVENT DATE');
        $sheet->setCellValue('E2', 'CONTROL NUMBER');
        $sheet->setCellValue('F2', 'INVOICE AMOUNT (TZS)');
        $sheet->setCellValue('G2', 'AMOUNT PAID (TZS)');
        $i = 3;
        foreach ($data as   $value) {
            $sheet->setCellValue('A' . $i, $value->companyName);
            $sheet->setCellValue('B' . $i, $value->eventTitle.' ('.$value->promotionEventTypeName.')');
            $sheet->setCellValue('C' . $i, $value->venueAddress);
            $sheet->setCellValue('D' . $i, $value->startFrom.' - '.$value->endDate);
            $sheet->setCellValue('E' . $i, $value->controlNumber);

            if ($value->currency == 'USD') {
                $sheet->setCellValue('F' . $i, $value->invoiceAmount * $rate);
                $sheet->setCellValue('G' . $i, $value->amountPaid * $rate);
            } else {
                $sheet->setCellValue('F' . $i, $value->invoiceAmount);
                $sheet->setCellValue('G' . $i, $value->amountPaid);
            }
            if ($value->invoiceAmount <= $value->amountPaid && $value->invoiceAmount>0) {
                $sheet->getStyle('A' . $i . ':G' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('75e897');
            }
            $i++;
        }
        $lastCell = $i + 1;
        $sheet->getStyle('A' . $lastCell . ':G' . $lastCell)->getFont()->setBold(true);
        $sheet->getStyle('A' . $lastCell . ':G' . $lastCell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c2c0ef');
        $sheet->getStyle('A' . $lastCell . ':G' . $lastCell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('F' . $lastCell, '=SUM(F3:F' . $i . ')');
        $sheet->getStyle('F3:F' . $lastCell)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->setCellValue('G' . $lastCell, '=SUM(G3:G' . $i . ')');
        $sheet->getStyle('G3:G' . $lastCell)->getNumberFormat()->setFormatCode('#,##0.00');

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
            'Content-Disposition' => 'attachment;filename="permitsReport_"' . date('d_m_Y') . '.xlsx',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
