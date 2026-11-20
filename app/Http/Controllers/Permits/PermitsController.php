<?php

namespace App\Http\Controllers\Permits;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_notes;
use App\Models\Companies\CompanyAdmin;
use App\Models\exhibition\exhibition_products;
use App\Models\exhibition\exhibitors;
use App\Models\Information\Comment;
use App\Models\Permits\Permit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class PermitsController extends Controller
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
        try {
            if ($request->user()->cannot('manage-iopermit', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            if (Auth::user()->account_type == 1) {
                $permits = Permit::leftJoin('companies', 'permits.company_id', '=', 'companies.companyID')
                    ->orderBy('permit_created_at', 'DESC')->paginate(10);
            } elseif (Auth::user()->account_type == 2) {
                $permits = Permit::leftJoin('companies', 'permits.company_id', '=', 'companies.companyID')
                    ->whereIn('permits.company_id', CompanyAdmin::where('adminId', Auth::user()->user_uuid)->get(['companyId']))
                    ->orderBy('permit_created_at', 'DESC')->paginate(10);
            }
            return view('IOPermits.Permits', compact('permits'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            if ($request->user()->cannot('Manage IOPermits', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $companies = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                ->leftJoin('company_details', 'companies.companyID', '=', 'company_details.companyID')
                ->leftJoin('events', 'exhibitors.exhibitor_event', '=', 'events.event_id')->where('event_status', 1)->get();
            return view('IOPermits.CreatePermit', compact('companies'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
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
            if ($request->user()->cannot('Manage IOPermits', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $exists = exhibitors::where('exhibitor_event', session('liveEvent'))
                ->where('exhibitor_id', $request->company_id)->first();
            if (!$exists) {
                exhibitors::create([
                    'participation_id' => Str::uuid(),
                    'exhibitor_id' => $request->company_id,
                    'exhibitor_event' => session('liveEvent'),
                ]);
            }
            $validator = Validator::make($request->all(), [
                'permitType' => 'required',
                'company_id' => 'required',
                'exhibitedProducts' => 'required'
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            $permit_id = Str::uuid();
            if ($request->permitType=='ENTRANCE_PERMIT') {
                $permitStatus='APPROVED';
               $permitNo = 'TEP-0' . date('y') . '-' . random_int(100, 599) . '-' . random_int(600, 999);
            }
            Permit::create([
                'permit_id' => $permit_id,
                'company_id' => $request->company_id,
                'event_id' => session('liveEvent'),
                'permit_type' => $request->permitType,
                'created_by' => Auth::user()->user_uuid,
                'permitStatus' => $permitStatus ?? 'UNDER_REVIEW',
                'itemList' => $request->exhibitedProducts,
                'permitNumber'=>$permitNo ?? Null,
                'permit_updated_at'=>now(),
            ]);
            return redirect()->route('iopermits.show', ['iopermit' => $permit_id])->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('Manage IOPermits', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $permit = Permit::leftJoin('companies', 'permits.company_id', '=', 'companies.companyID')
                ->where('permit_id', $id)->get();
            $cid = Permit::leftJoin('companies', 'permits.company_id', '=', 'companies.companyID')->where('permit_id', $id)->first(); // get company ID

            $products = exhibition_products::where('exhibitor_id', $cid->company_id)->where(
                'event',
                session('liveEvent')
            )->get();

            $payments = advice_notes::where('company_id', $cid->company_id)->where('advice_note_status', 3)
                ->sum('balance');
            $comments = Comment::orderBy('commentDate', 'DESC')->leftJoin('users', 'comments.commentedBy', '=', 'users.user_uuid')
                ->leftJoin('roles', 'users.role', '=', 'roles.id')->where('referenceId', $id)->get();
            return view('IOPermits.ProcessPermit', compact('permit', 'products', 'payments', 'comments'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->user()->cannot('Manage IoPermits', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $products = exhibition_products::where('exhibition_product_id', $id)
            ->where('event', session('liveEvent'))->get();
        return view('IOPermits.EditExhibitedProducts', compact('products'));
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
        $permit = Permit::find($id);
    if ($permit->permit_type == 'EXIT_PERMIT') {
            if ($request->user()->cannot('approve-permit', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            if ($request->comment) {
                Comment::create([
                    'commentId' => Str::uuid(),
                    'commentedBy' => Auth::user()->user_uuid,
                    'comment' => $request->comment,
                    'referenceId' => $id,
                    'commentStatus' => 'SUBMITED'
                ]);
            }
            $permitNo = Null;
            if ($request->permitStatus == 'APPROVED') {
                $permitNo = 'TEP-0' . date('y') . '-' . random_int(100, 599) . '-' . random_int(600, 999);
            }
            $permit->update([
                'permitStatus' => $request->permitStatus ?? 'UNDER_REVIEW',
                'permitNumber' => $permitNo,
                'approvedBy' => Auth::user()->user_uuid,
            ]);
            return back()->with('success', config('Messages.200'));
        }
    }
    #---------END SAC OFFICER DECISION  -------#

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $permit = Permit::find($id);
            if ($permit) {
                $permit->delete();
            }
            return redirect()->route('iopermits.index')->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function seachPermit(Request $request)
    {
        try {
            $search = $request->get('q');
            if (Auth::user()->account_type == 1) {
                $data = Permit::orderBy('permit_created_at', 'DESC')
                    ->leftJoin('companies', 'permits.company_id', '=', 'companies.companyID')
                    ->where(function ($query) use ($search) {
                        $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                        $query->orWhere('registrationID', 'LIKE', '%' . $search . '%');
                        $query->orWhere('permitStatus', 'LIKE', '%' . $search . '%');
                    })
                    ->where('permit_id', '!=', Null)
                    ->get();
            }
            elseif (Auth::user()->account_type == 2) {
                $data = Permit::orderBy('permit_created_at', 'DESC')
                    ->leftJoin('companies', 'permits.company_id', '=', 'companies.companyID')
                    ->where(function ($query) use ($search) {
                        $query->orWhere('companyName', 'LIKE', '%' . $search . '%');
                        $query->orWhere('registrationID', 'LIKE', '%' . $search . '%');
                        $query->orWhere('permitStatus', 'LIKE', '%' . $search . '%');
                    })
                    ->whereIn('permits.company_id',CompanyAdmin::where('adminId',Auth::user()->user_uuid)->get(['companyId']))
                    ->get();
            }
            return response()->json($data);
        } catch (\Throwable $th) {
        }
    }
}
