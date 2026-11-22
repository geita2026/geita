<?php

namespace App\Http\Controllers\reports;
use App\Http\Controllers\Controller;
use App\Models\exhibition\exhibition_products;
use Illuminate\Http\Request;
use App\Models\settings\events;
use Spatie\Permission\Models\Role;

class ProductReportController extends Controller
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
        if($request->user()->cannot('VIEW_EXHIBITION_PRODUCT_REPORT_SUBMENU',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('VIEW_EXHIBITION_PRODUCT_REPORT_SUBMENU'));
        }

        $events=events::orderBy('event_created_at','ASC')->get();
        if($request->event_name)
        {
            $event_id=$request->event_name;
        }
        else
        {

            foreach ($events as $key) {
               $event_id=$key->event_id;
            }
        }


        $eventName=events::get()->where('event_id',$event_id);
        foreach($eventName as $value){ $event_name=$value->event_name; }

        $products=exhibition_products::leftJoin('companies','exhibition_products.exhibitor_id','=','companies.companyID')
        ->where('event',$event_id)->get();
        $events=events::get();
        return view('reports.exhibitor_products',compact('products','event_name','products','events'));
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('DELETE_EXHIBITION_PRODUCT_REPORT',Role::class))
        {
            return back()->with('fail',$this->permissionDeniedMessage('DELETE_EXHIBITION_PRODUCT_REPORT'));
        }
        //
        try {
            $product=exhibition_products::find($id);
            $product->delete();
            return back()->with('success','Products deleted successfully');

        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong'.$th->getMessage());
        }
    }
}
