<?php

namespace App\Http\Controllers\Catalogy;

use App\Http\Controllers\Controller;
use App\Models\booking\booked_pavilions;
use App\Models\Companies\CompanyAdmin;
use App\Models\exhibition\CatalogueSetting;
use App\Models\exhibition\exhibitors;
use App\Models\settings\events;
use App\Models\space\booths;
use App\Models\space\pavilions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;

class CatalogueSettingController extends Controller
{
    /**
     * 
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
            if ($request->user()->cannot('Manage Catalogue', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $catalogueSettings = CatalogueSetting::orderBy('order_number', 'ASC')
                ->leftJoin('events', 'catalogue_settings.event', '=', 'events.event_id')
                ->get();
            $events = events::orderBy('event_created_at', 'DESC')->get();
            return view('Catalogy.CatalogueSettings', compact('catalogueSettings', 'events'));
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
            if ($request->user()->cannot('Manage Catalogue', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validator = Validator::make($request->all(), [
                'leader_name' => 'required',
                'leader_title' => 'required',
                'leader_image' => 'required|mimes:png,jpg,jpeg',
                'appriciation_word' => 'required',
                'order_number' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            $leader_image = 'Catalogue_' . Str::uuid() . '.' . $request->leader_image->extension();
            $request->leader_image->move(public_path('CatalogueImages'), $leader_image);
            CatalogueSetting::create([
                'leader_id' => Str::uuid(),
                'leader_name' => $request->leader_name,
                'leader_title' => $request->leader_title,
                'leader_image' => $leader_image,
                'appriciation_word' => $request->appriciation_word,
                'order_number' => $request->order_number,
                'event' => $request->event,
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
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
        try {
            if ($request->user()->cannot('Manage Catalogue', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validator = Validator::make($request->all(), [
                'leader_name' => 'required',
                'leader_title' => 'required',
                'leader_image' => 'mimes:png,jpg,jpeg',
                'appriciation_word' => 'required',
                'order_number' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            if (!empty($request->leader_image)) {
                File::delete(public_path('CatalogueImages/') . $request->old_image);
                $leader_image = 'Catalogue_' . Str::uuid() . '.' . $request->leader_image->extension();
                $request->leader_image->move(public_path('CatalogueImages'), $leader_image);
            } else {
                $leader_image = $request->old_image;
            }
            CatalogueSetting::find($id)->update([
                'leader_name' => $request->leader_name,
                'leader_title' => $request->leader_title,
                'leader_image' => $leader_image,
                'appriciation_word' => $request->appriciation_word,
                'order_number' => $request->order_number,
                'event' => $request->event,
            ]);
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if ($request->user()->cannot('Delete Catalogue', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $leader = CatalogueSetting::find($id);
            if (File::delete(public_path('CatalogueImages/') . $leader->leader_image)) {
                $leader->delete();
                return back()->with('success', 'Leader deleted successfully!');
            }
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }


    public function confirmParticipation(Request $request)
    {
        try {
            if ($request->user()->cannot('Confirm Participation', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }

            $companyId = $request->companyId;

            $myCompany = CompanyAdmin::leftJoin('companies', 'company_admins.companyId', '=', 'companies.companyID')
                ->where('adminId', Auth::user()->user_uuid)
                ->whereIn('company_admins.companyId', exhibitors::where('exhibitor_event', session('liveEvent'))->get(['exhibitor_id']))
                ->get();

            $exhibitor = exhibitors::where('exhibitor_event', session('liveEvent'))
                ->where('exhibitor_id', $companyId)->first();

            $space = booked_pavilions::leftJoin('booths', 'booked_pavilions.pavilionId', '=', 'booths.boothID')
                ->leftJoin('pavilions', 'booked_pavilions.pavilionId', '=', 'pavilions.pavilionID')
                ->where('booked_pavilions.exhibition_year', session('liveEvent'))
                ->leftJoin('advice_notes', 'booked_pavilions.advice_note_id', '=', 'advice_notes.advice_note_id')
                ->where('advice_note_status', '!=', 'CANCELED')
                ->where('booked_pavilions.exhibition_year',session('liveEvent'))
                ->where('advice_notes.company_id', $companyId)
                ->get(['pavilionName', 'boothID', 'boothNo', 'boothSize', 'pavilion_size']);
            $mySpace = array();
            $combined = Null;
            foreach ($space as $key => $value) {
                if ($value->boothNo != null) {
                    $size = $value->boothSize;
                    $pavilions = booths::leftJoin('pavilions', 'booths.pavilionID', '=', 'pavilions.pavilionID')
                        ->where('boothID', $value->boothID)->first()->pavilionName;
                }
                if ($value->pavilionName != null) {
                    $size = $value->pavilion_size;
                }
                $spaceName = $value->pavilionName ?? $value->boothNo;
                $combined = $pavilions . '(' . $spaceName . ')' . ' ' . $size . ' msq/unit';
                array_push($mySpace, $combined);
            }
            if ($combined == Null) {
            }

            if ($exhibitor) {
                $exhibitor->update([
                    'exhibitionSpace' => implode(', ', $mySpace),
                ]);

                if ($request->source == 1) {
                    $exhibitor->update([
                        'exhibitedProducts' => $request->exhibitedProducts,
                        'makeSales' => $request->makeSales,
                    ]);
                }
            }
            $exhibitors = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                ->where('exhibitor_event', session('liveEvent'))
                ->where('exhibitor_id', $companyId)->get();

            return view('Exhibition.ConfirmParticipation', compact('exhibitors', 'myCompany'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function getParticipationForm(Request $request, $participationId)
    {
        try {
            $exhibitor = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                ->where('participation_id', $participationId)
                ->get();

            $pdf = PDF::loadView('Exhibition.ParticipationForm', compact('exhibitor'));
            $pdf->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]
                ])
            );
            $pdf->render();
            return $pdf->stream('Participation_form_' . date('d_m_Y') . '.pdf');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }


    public function goToUploadForm(Request $request, $participationId)
    {
        try {
            $exhibitor = exhibitors::leftJoin('companies', 'exhibitors.exhibitor_id', '=', 'companies.companyID')
                ->where('participation_id', $participationId)
                ->get();
            return view('Exhibition.UploadForm', compact('exhibitor'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function uploadParticipationForm(Request $request, $participationId)
    {
        try {
            if ($request->user()->cannot('Book Space', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $validator = Validator::make($request->all(), [
                'participationForm' => 'required|mimes:pdf'
            ]);
            if ($validator->fails()) {
                return back()->with('fail', implode(',', $validator->getMessageBag()->all()));
            }
            $exhibitor = exhibitors::find($participationId);
            if ($exhibitor) {
                $pulicPath = env('APP_URL') . '/public/uploadedFiles/';
                $file = str_replace($pulicPath, '', $exhibitor->participationCommitimentFile);
                $filePath = public_path('uploadedFiles/' . $file);
                File::delete($filePath);

                $fileName = 'participation_form_' . $exhibitor->participation_id . '.' . $request->participationForm->extension();
                $request->participationForm->move(public_path('uploadedFiles'), $fileName);
                $fileLink = $pulicPath . $fileName;
                $exhibitor->update([
                    'participationCommitimentFile' => $fileLink
                ]);
                return back()->with('success', config('Messages.200'));
            }
            return back()->with('fail', config('Messages.404'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . $th->getMessage());
        }
    }
}
