<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\sectors;
use Illuminate\Http\Request;
use App\Models\settings\events;
use App\Models\ExhibitionBadges\CompanyMember;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class exhibitorsReports extends Controller
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
        if ($request->user()->cannot('Manage ExhibitorReports', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        $events = events::orderBy('event_status', 'DESC')->orderBy('event_created_at', 'ASC')->get();
        $sectors = sectors::orderBy('sectorName', 'ASC')->get();

        $data = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
            ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
            ->where('exhibitor_event', $request->event)
            ->where('companies.companySector', 'LIKE', '%' . $request->sectorName . '%')
            ->orderBy('companyName', 'ASC')->get([
                'companyName',
                'companyEmail',
                'companyPhone',
                'sectorName'
            ]);

        $data1 = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
            ->leftJoin('sectors', 'companies.companySector', '=', 'sectors.sectorID')
            ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')
            ->where('exhibitor_event', $request->event)
            ->where('companies.companySector', 'LIKE', '%' . $request->sectorName . '%')
            ->orderBy('companyName', 'ASC')
            ->paginate(config('global.DATA_LIMIT'), [
                'companyName',
                'companyEmail',
                'companyPhone',
                'sectorName',
                'participation_id'
            ]);
        session(['exhibitorData' => $data]);

        return view('reports.exhibitor-reports', compact('events', 'sectors', 'data1'));
    }




    public function store(Request $request)
    {
        $data = session('exhibitorData');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'TANZANIA TRADE DEVELOPMENT AUTHORITY
    EXHIBITOR REPORT - ' . date('M d, Y'));

        $sheet->setCellValue('A2', 'COMPANY NAME');
        $sheet->setCellValue('B2', 'COMPANY EMAIL');
        $sheet->setCellValue('C2', 'COMPANY PHONE');
        $sheet->setCellValue('D2', 'COMPANY SECTOR');



        $i = 3;
        foreach ($data as   $value) {

            $sheet->setCellValue('A' . $i, $value->companyName);
            $sheet->setCellValue('B' . $i, $value->companyEmail);
            $sheet->setCellValue('C' . $i, $value->companyPhone);
            $sheet->setCellValue('D' . $i, $value->sectorName);
            $i++;
        }
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
            'Content-Disposition' => 'attachment;filename="exhibitorReport_"' . date('d_m_Y') . '.xlsx',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
