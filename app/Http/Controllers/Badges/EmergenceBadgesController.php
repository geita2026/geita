<?php

namespace App\Http\Controllers\Badges;

use App\Http\Controllers\Controller;
use App\Models\Badges\EmergenceBadge;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class EmergenceBadgesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public  $eventCode='19-27-24';

    public function index(Request $request)
    {
        if ($request->user()->cannot('VIEW_BADGES_LIST', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_BADGES_LIST'));
        }
      
                $companies = EmergenceBadge::where('eventCode', $this->eventCode)->get();

        return view('Badges.EmegencyBadges.CompaniesList', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       try {
        EmergenceBadge::create([
            'companyId'=>Str::uuid(),
            'companyName'=>$request->companyName,
            'companyEmail'=>$request->companyPhone,
            'companyPhone'=>$request->companyPhone,
            'eventCode'=>$this->eventCode,
            'badgeTitle'=>$request->badgeTitle,
           ]);
           return back()->with('success','Exhibitor added successfully!');
       } catch (\Throwable $th) {
        return back()->with('fail','Something went wrong!');
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $badges=EmergenceBadge::where('companyId',$id)->get();
        $pdf=PDF::loadView('Badges.EmegencyBadges.PrintableBadges',compact('badges'))->setPaper('A4','portrait');
        $pdf->render();
        return $pdf->stream("Exhibitor_Badges_".date('d_m'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        try {
            EmergenceBadge::find($id)->update([
                'companyName'=>$request->companyName,
                'companyEmail'=>$request->companyEmail,
                'companyPhone'=>$request->companyPhone,
                'eventCode'=>$this->eventCode,
                'badgeTitle'=>$request->badgeTitle,
               ]);
               return back()->with('success','Exhibitor updated successfully!');
           } catch (\Throwable $th) {
            return back()->with('fail','Something went wrong!');
           }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
