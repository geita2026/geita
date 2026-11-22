<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\companyTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class templateController extends Controller
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
        if($request->user()->cannot('DOWNLOAD_COMPANY_UPLOAD_TEMPLATE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DOWNLOAD_COMPANY_UPLOAD_TEMPLATE'));
        }

        //
        return Excel::download(new companyTemplateExport, 'Companies.xlsx');
    }

}
