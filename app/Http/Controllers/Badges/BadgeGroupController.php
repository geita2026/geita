<?php

namespace App\Http\Controllers\Badges;

use App\Http\Controllers\Controller;
use App\Models\ExhibitionBadges\BadgeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class BadgeGroupController extends Controller
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
        if($request->user()->cannot('View Badge Group',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        $badge_groups=BadgeGroup::get();
        return view('Badges.badge_group',compact('badge_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Badge Group',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //

        try {
            BadgeGroup::create([
                'group_id'=>Str::uuid(),
                'group_name'=>$request->group_name,
                'text_color'=>$request->text_color,
                'group_background_color'=>$request->background_color,
            ]);
            return back()->with('success',$request->group_name.' Group saved successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong, ERROR: '.$th->getMessage());
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
        if($request->user()->cannot('Update Badge Group',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }

        try {
            $group=BadgeGroup::find($id);
            $group->update([
                'group_name'=>$request->group_name,
                'text_color'=>$request->text_color,
                'group_background_color'=>$request->background_color,
            ]);
            return back()->with('success',$request->group_name.' Group Updated successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong, ERROR: '.$th->getMessage());
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
        if($request->user()->cannot('Delete Badge Group',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        try {
            $group=BadgeGroup::find($id);
            $group->delete();
            return back()->with('success','Group Deleted successfully!');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong, ERROR: ');
        }
    }
}
