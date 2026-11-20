<?php

namespace App\Http\Controllers\Badges;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExhibitionBadges\Badges;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\ExhibitionBadges\BadgeGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PrepareBadgeController extends Controller
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
    {  if($request->user()->cannot('View Badges List',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //

        if(!empty($id))
        {
            $badges=Badges::leftJoin('company_members','badges.member_id','=','company_members.member_id')
            ->leftJoin('companies','badges.organization_id','=','companies.companyID')
            ->leftJoin('badge_groups','company_members.member_group','=','badge_groups.group_id')
            ->where('company_id',$id)->get();
        }
        else
        {
            $badges=Badges::leftJoin('company_members','badges.member_id','=','company_members.member_id')
            ->leftJoin('companies','badges.organization_id','=','companies.companyID')
            ->leftJoin('badge_groups','company_members.member_group','=','badge_groups.group_id')
            ->where('company_id',session('company_id'))
            ->get();
        }

        $members=CompanyMember::limit(40)->get();
        
        $companies=exhibitors::leftJoin('companies','exhibitors.exhibitor_id','=','companies.companyID')
        ->leftJoin('events','exhibitors.exhibitor_event','=','events.event_id')
        ->where('event_status',1)->get(['companyID','companyName']);

        return view('Badges.print_badges',compact('badges','members','companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->cannot('View Badges List',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        if (Auth::user()->account_type==1) {
            $groups=BadgeGroup::orderBy('group_name','ASC')->get();
        }
        else{
            $groups=BadgeGroup::where('group_name','EXHIBITOR')->get();
        }
        $groups=BadgeGroup::orderBy('group_name','ASC')->get();

        $members=CompanyMember::orderBy('badge_status','ASC')->leftJoin('companies','company_members.company_id','=','companies.companyID')
        ->leftJoin('events','company_members.event','=','events.event_id')
        ->leftJoin('badge_groups','company_members.member_group','=','badge_groups.group_id')
        ->where('event',session('liveEvent'))->limit(16)->get();

        $badges=CompanyMember::orderBy('companyName','ASC')->orderBy('member_added_at','DESC')
        ->leftJoin('companies','company_members.company_id','=','companies.companyID')
        ->leftJoin('badge_groups','company_members.member_group','=','badge_groups.group_id')
        ->where('badge_status','UNPRINTED')->where('event',session('liveEvent'))->limit(16)->get(); // get all unprinted badges with 10 PDF limit

        $PrintedBadges=CompanyMember::where('event',session('liveEvent'))->where('badge_status','PRINTED')->count();

        $UnprintedBadges=CompanyMember::where('event',session('liveEvent'))->where('badge_status','UNPRINTED')->count();
          

        $companies=companies::whereIn('companyID',CompanyMember::where('event',session('liveEvent'))->get(['company_id']))->get(['companyID','companyName']);

        $associate=companies::whereIn('companyID',CompanyMember::where('event',session('liveEvent'))->get(['associateCompany']))->get(['companyID','companyName']);

        return view('Badges.badges_list',compact('groups','members','companies','badges','PrintedBadges','UnprintedBadges','associate'));
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if($request->user()->cannot('View Badges List',Role::class))
        {
            return back()->with('fail','You do not have permission to perform this Action');
        }
        //
        $SearchSessionId=Str::uuid();
        $badges=null;
        $limit=16;

        $groups=BadgeGroup::orderBy('group_name','ASC')->get();

        if($request->badge_status!==null && $request->company_id != null)
        {
            foreach ($request->company_id as $key) {
                $members=CompanyMember::where('event',session('liveEvent'))->where('company_id',$key)->where('badge_status',$request->badge_status)->limit(16)->get();
                foreach ($members as $value) {
                   CompanyMember::find($value->member_id)->update(['search_session_id'=>$SearchSessionId]); // set search session ID;
                }
            }

            $badges=CompanyMember::orderBy('badge_status','ASC')->leftJoin('companies','company_members.company_id','=','companies.companyID')
           ->leftJoin('badge_groups','company_members.member_group','=','badge_groups.group_id')
           ->where('search_session_id',$SearchSessionId)->limit(12)->get();
        }
       elseif($request->badge_status!==null){

            $members=CompanyMember::where('event',session('liveEvent'))->where('badge_status',$request->badge_status)->get();
            foreach ($members as $value) {
               CompanyMember::find($value->member_id)->update(['search_session_id'=>$SearchSessionId]); // set search session ID;
            }

           $badges=CompanyMember::orderBy('badge_status','ASC')->leftJoin('companies','company_members.company_id','=','companies.companyID')
           ->leftJoin('badge_groups','company_members.member_group','=','badge_groups.group_id')
           ->where('search_session_id',$SearchSessionId)->limit(12)->get();
        }
        elseif($request->company_id)
        {
            foreach ($request->company_id as $key) {
                $members=CompanyMember::where('event',session('liveEvent'))->where('company_id',$key)->get();
                foreach ($members as $value) {
                   CompanyMember::find($value->member_id)->update(['search_session_id'=>$SearchSessionId]); // set search session ID;
                }
            }

               $badges=CompanyMember::orderBy('badge_status','ASC')->leftJoin('companies','company_members.company_id','=','companies.companyID')
               ->leftJoin('badge_groups','company_members.member_group','=','badge_groups.group_id')
               ->where('search_session_id',$SearchSessionId)->limit(16)->get();
        }

        $members=CompanyMember::orderBy('badge_status','ASC')->leftJoin('companies','company_members.company_id','=','companies.companyID')
        ->leftJoin('events','company_members.event','=','events.event_id')
        ->leftJoin('badge_groups','company_members.member_group','=','badge_groups.group_id')
        ->where('event',session('liveEvent'))->get();

        $companies=companies::whereIn('companyID',CompanyMember::where('event',session('liveEvent'))->get(['company_id']))->get(['companyID','companyName']);

       $associate=companies::whereIn('companyID',CompanyMember::where('event',session('liveEvent'))->get(['associateCompany']))->get(['companyID','companyName']);
        
        session(['company_id'=>$request->company_id]);
        session(['badge_status'=>$request->badge_status]);


        $PrintedBadges=CompanyMember::leftJoin('events','company_members.event','=','events.event_id')
        ->where('event',session('liveEvent'))->where('badge_status','PRINTED')->count();

        $UnprintedBadges=CompanyMember::leftJoin('events','company_members.event','=','events.event_id')
        ->where('event',session('liveEvent'))->where('badge_status','UNPRINTED')->count();

        if($badges==null)
        {
            return redirect()->route('prepare_badges.create')->with('fail','No badge selected');
        }

        
        return view('Badges.badges_list',compact('groups','members','companies','badges','PrintedBadges','UnprintedBadges','associate'));

    }

}
