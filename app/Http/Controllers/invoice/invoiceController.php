<?php

namespace App\Http\Controllers\invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class invoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('VIEW_ANY_INVOICE',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_ANY_INVOICE'));
        }
        //
        return view('invoice.invoices');
    }

}
