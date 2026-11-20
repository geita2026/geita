<?php

namespace App\Http\Controllers\Exhibition;

use App\Http\Controllers\Controller;
use App\Models\Companies\CompanyAdmin;
use App\Models\exhibition\exhibitors;
use App\Models\exhibition\JudgingCategory;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ExhibitorJudgingController extends Controller
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
            if ($request->user()->cannot('Confirm Judging', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }

            $sectors = JudgingCategory::where('categoryStatus', 1)->get();
            $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                ->leftJoin('judging_categories', 'exhibitors.judgingCategory', '=', 'judging_categories.categoryId')
                ->where('exhibitor_event', session('liveEvent'))
                ->whereIn('exhibitor_id', CompanyAdmin::where('adminId', Auth::user()->user_uuid)->get(['companyId']))
                ->orderBy('companyName','ASC')->paginate(50);
            if (Auth::user()->account_type == 1) {
                $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                    ->leftJoin('judging_categories', 'exhibitors.judgingCategory', '=', 'judging_categories.categoryId')
                    ->where('exhibitor_event', session('liveEvent'))
                    ->orderBy('companyName','ASC')
                    ->paginate(50);
            }
            if ($request->search) {
              $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                ->leftJoin('judging_categories', 'exhibitors.judgingCategory', '=', 'judging_categories.categoryId')
                ->where('exhibitor_event', session('liveEvent'))
                ->whereIn('exhibitor_id', CompanyAdmin::where('adminId', Auth::user()->user_uuid)->get(['companyId']))
                ->orderBy('categoryName','ASC')->paginate(50);
            }
            return view('Exhibition.ExhibitorJudging', compact('exhibitors', 'sectors'));
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
        if ($request->user()->cannot('Confirm Judging', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        // Source of truth = the DITF Judging app. SAMS pulls categories from it.
        $url = env('JUDGING_CATEGORY_API', 'https://www.tantrade.go.tz/award/api/judgingCategoryApi');
        try {
            $response = Http::timeout(10)->get($url);
            $sectors = $response['categories'] ?? [];
        } catch (\Throwable $th) {
            return back()->with('fail', 'Could not reach the Judging app to sync categories.');
        }

        $incoming = [];
        foreach ($sectors as $value) {
            $incoming[] = $value['uuid'];
            $exist = JudgingCategory::find($value['uuid']);
            if ($exist) {
                $exist->update([
                    'categoryName' => $value['title'],
                    'description' => '',
                    'categoryStatus' => 1,
                ]);
            } else {
                JudgingCategory::create([
                    'categoryId' => $value['uuid'],
                    'categoryName' => $value['title'],
                    'description' => '',
                    'categoryStatus' => 1,
                ]);
            }
        }

        // Deactivate any category no longer published by the judging app.
        if (!empty($incoming)) {
            JudgingCategory::whereNotIn('categoryId', $incoming)->update(['categoryStatus' => 0]);
        }

        return back()->with('success', 'Judging categories synced from the Judging app.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\exhibition\EventProgram  $eventProgram
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('Confirm Judging', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        try {

            $exist = exhibitors::find($id);
            if ($exist) {
                $exist->update([
                    'judging_location' => $request->judging_location,
                    'judging_status' => 1,
                    'judgingCategory' => $request->judgingCategory,
                    'pStatus' => 1,
                ]);
            } else {
                exhibitors::create([
                    'participation_id' => Str::uuid(),
                    'exhibitor_id' => AUth::user()->myCompany,
                    'exhibitor_event' => session('liveEvent'),
                    'judging_location' => $request->judging_location,
                    'judging_status' => $request->internalJudging,
                    'judgingCategory' => $request->judgingCategory,
                    'pStatus' => $request->popularJudging,
                ]);
            }
            return back()->with('success', 'Confirmed successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

  
}
