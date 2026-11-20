<?php

namespace App\Http\Controllers\Stickers;

use App\Http\Controllers\Controller;
use App\Models\exhibition\exhibitors;
use App\Models\Stickers\CarSticker;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PrintStickerController extends Controller
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
        if($request->user()->cannot('Print Stickers',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $stickers=CarSticker::leftJoin('companies','car_stickers.company_id','=','companies.companyID')
        ->leftJoin('additional_services','car_stickers.reference_sticker','=','additional_services.serviceID')
        ->leftJoin('events','car_stickers.event_id','=','events.event_id')
        ->leftJoin('badge_groups','car_stickers.design_group','=','badge_groups.group_id')
        ->where('car_stickers.event_id',session('liveEvent'))
        ->where('sticker_status',0)->paginate(100);

        $printedStickers=CarSticker::where('sticker_status',1)
        ->where('car_stickers.event_id',session('liveEvent'))->count();
        $UnprintedStickers=CarSticker::where('sticker_status',0)
        ->where('event_id',session('liveEvent'))->count();

        $companies=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
        ->orderBy('companyName','ASC')->where('exhibitor_event',session('liveEvent'))
        ->get(['companyID','companyName']);
        return view('Stickers.PrepareStickers',compact('stickers','companies','printedStickers','UnprintedStickers'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if($request->user()->cannot('Print Stickers',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

       try
       {
        $search_id=Str::uuid();

        $i=0;
        foreach ($request->company_id as $key)
        {
            $sticker=CarSticker::leftJoin('events','car_stickers.event_id','=','events.event_id')
            ->where('event_id',session('liveEvent'))->where('company_id',$key)
            ->where('sticker_status',0)
            ->get();
            foreach ($sticker as $value)
            {
             CarSticker::find($value->sticker_id)->update(['search_id'=>$search_id]);
            }
            $i++;
        }


        $stickers=CarSticker::leftJoin('companies','car_stickers.company_id','=','companies.companyID')
        ->leftJoin('additional_services','car_stickers.reference_sticker','=','additional_services.serviceID')
        ->leftJoin('badge_groups','car_stickers.design_group','=','badge_groups.group_id')
        ->where('search_id',$search_id)->get();

        $printedStickers=CarSticker::where('sticker_status',1)->where('event_id',session('liveEvent'))->count();
        $UnprintedStickers=CarSticker::where('sticker_status',0)->where('event_id',session('liveEvent'))->count();

        $companies=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
        ->orderBy('companyName','ASC')->where('exhibitor_event',session('liveEvent'))
        ->get(['companyID','companyName']);

        return view('Stickers.PrepareStickers',compact('stickers','companies','printedStickers','UnprintedStickers'));

       } catch (\Throwable $th)
       {
       return back()->with('fail','No sticker selected!');
       }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if($request->user()->cannot('Print Stickers',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
    try {
        $print_id=Str::uuid();

        if($request->source==2)
        {
            $i=0;
            foreach ($request->sticker_id as $key)
            {
               CarSticker::find($key)->update(['print_id'=>$print_id,'sticker_status'=>1]);
               $i++;
            }
            if($i==0)
            {
                return back()->with('fail','No sticker selected!');
            }

            $stickers=CarSticker::leftJoin('companies','car_stickers.company_id','=','companies.companyID')
            ->leftJoin('additional_services','car_stickers.reference_sticker','=','additional_services.serviceID')
            ->leftJoin('events','car_stickers.event_id','=','events.event_id')
            ->leftJoin('badge_groups','car_stickers.design_group','=','badge_groups.group_id')
            ->where('print_id',$print_id)->get();

        }
        else
        {
            CarSticker::find($id)->update(['sticker_status'=>1]);
            $stickers=CarSticker::leftJoin('companies','car_stickers.company_id','=','companies.companyID')
            ->leftJoin('additional_services','car_stickers.reference_sticker','=','additional_services.serviceID')
            ->leftJoin('events','car_stickers.event_id','=','events.event_id')
            ->leftJoin('badge_groups','car_stickers.design_group','=','badge_groups.group_id')
            ->where('sticker_id',$id)->get();
        }

        # layout supports: '6' (6-up), '4' (4-up 88x120), '4xl' (4-up 86x127)
        $layout = (string) $request->input('layout', '6');
        if (!in_array($layout, ['6', '4', '4xl'], true)) {
            $layout = '6';
        }
        $perPage = ($layout === '6') ? 6 : 4;

        $payload = $stickers->map(fn($s) => [
            'stickerId'   => $s->sticker_id,
            'plateNumber' => strtoupper(trim($s->plate_number ?? '')),
            'stripColor'  => $s->group_background_color ?? '#000000',
            'textColor'   => $s->text_color ?? '#ffffff',
            'zone'        => $this->stickerZone($s->serviceName ?? null),
        ])->values()->all();

        return view('Stickers.SeasonalStickerPrintSheet', [
            'stickers' => $payload,
            'perPage'  => $perPage,
            'layout'   => $layout,
        ]);
    } catch (\Throwable $th) {
        return redirect()->route('print_stickers.index')->with('fail','No sticker selected!');
    }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->user()->cannot('Print Stickers',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }


    }

    /**
     * Resolve the parking zone label ("INSIDE" / "OUTSIDE") printed on a sticker
     * from the referenced additional service's name. Services named for outside
     * parking (e.g. "Seasonal Outside Car Parking") print OUTSIDE; everything
     * else defaults to INSIDE, matching the legacy artwork.
     */
    private function stickerZone(?string $serviceName): string
    {
        $name = strtolower($serviceName ?? '');

        if (str_contains($name, 'outside') || str_contains($name, 'nje')) {
            return 'OUTSIDE';
        }

        return 'INSIDE';
    }
}
