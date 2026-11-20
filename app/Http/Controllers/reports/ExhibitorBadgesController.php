<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ExhibitorBadgesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }
    public function index(Request $request)
    {

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $i = 0;
        for ($i=0; $i<10; $i++) {
            $ticketNo=random_int(2000,5999).date('is').$i.date('sd').'POS011';
            $sheet->setCellValue('A' . $i, $ticketNo);
            $sheet->setCellValue('B' . $i, 'Adult Entry Ticket');
            $sheet->setCellValue('C' . $i, '3000');
            $sheet->setCellValue('D' . $i, '2024-07-30 14:01:0'.number_format($i/40));
        }
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        $writer = new Xlsx($spreadsheet);
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;ticket="collectionReport_"' . date('d_m_Y') . '.xlsx',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
