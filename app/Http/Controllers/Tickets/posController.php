<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\PosList;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class posController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->cannot('Manage Pos',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        try {
            $pos=PosList::get();
        return view('Tickets.Pos',compact('pos'));

        } catch (\Throwable $th) {
            return back()->with('fail','something went wrong!'.$th->getMessage());
        }
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
        if($request->user()->cannot('Manage Pos',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try {
            PosList::create([
                'posId'=>Str::uuid(),
                'posName'=>$request->posName,
                'posDescription'=>$request->posDescription,
                'posStatus'=>$request->posStatus,
            ]);
            return back()->with('success','POS added successfully!');
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
        //
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
        if($request->user()->cannot('Manage Pos',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try {
            PosList::find($id)->update([
                'posName'=>$request->posName,
                'posDescription'=>$request->posDescription,
                'posStatus'=>$request->posStatus,
            ]);
    
            return back()->with('success','Post Updated successfull!');
        } catch (\Throwable $th) {
            return back()->with('fail','something went wrong!');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('Manage Pos',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        try {
            PosList::find($id)->delete();
        return back()->with('success','POS deleted successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail','something went wrong!');
        }
    }
}
