<?php

namespace App\Http\Controllers;

use App\Models\NationalBrandCompetition;
use App\Models\TnbCriteria;
use App\Models\TnbMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class specialWorkController extends Controller
{
    public function TNBCompetition(Request $request)
    {
        return view('TNBCompetition');
    }
    public function submitArtWork(Request $request)
    {
        if (date('Y-m-d') > '2024-12-23') {
            return back()->with('fail', 'Tanzania National Brand Logo Competetion is now closed! shortlisted Applicants will be contacted, After review process completed!');
        }
        $request->validate([
            'zippedFile' => 'required|file|mimes:zip,rar|max:10240',
            'logoDescription' => 'required|max:500',
            'competitionNumber'=>'required'
        ]);
        try {

            $exists = NationalBrandCompetition::where('status','>=',5)
            ->where('competitionNumber', $request->competitionNumber)->first();
            if (!$exists) {
                return back()->with('fail', 'Registration Number does not exist, please recorrect your number and try Again!');
            }
            if ($exists->status==7) {
                return back()->with('success', 'We have already received your design!');  
            }
            $competitionNumber = $request->competitionNumber.date('_d_m_Y');
            $fileName =$competitionNumber . '.' . $request->zippedFile->extension();
            $zippedFile = env('APP_URL') . '/public/uploadedFiles/' . $fileName;
            $exists->update([
                'zippedFile' => $zippedFile,
                'logoDescription' => $request->logoDescription,
                'status' => 7
            ]);
            $request->zippedFile->move(public_path('uploadedFiles'), $fileName);
            $data = [
                'name' => $exists->fullName,
                'DateReceived' => date('M d, Y'),
                'competitionNumber' => $request->competitionNumber,
                'emailAddress' => $exists->emailAddress,
                'subject' => 'Tanzania National Brand Competition',
            ];
            Mail::send('mails.TNBCMAIL', $data, function ($message) use ($data) {
                $message
                    ->to($data['emailAddress'], $data['name'])
                    ->bcc('madeintanzania@tantrade.go.tz')
                    ->priority(1)
                    ->subject($data['subject']);
            });
            return back()->with('success', 'Your Artwork received successfully!');
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function getAllApplication(Request $request)
    {
        if ($request->source==1) {
            NationalBrandCompetition::where('status','!=',3) 
            ->update([
                'status'=>3,
            ]);
            if (session('showMarks')) {
                $request->session()->forget('showMarks');
            }
            else {
                session(['showMarks'=>1]);
            }
            
            $applications = NationalBrandCompetition::where('status','>=',7)->orderBy('status', 'DESC')->get();
        }
        else {
            $applications = NationalBrandCompetition::where('status','>=',7)->orderBy('status', 'DESC')->get();
        if ($request->competitionNumber) {
            NationalBrandCompetition::where('status',2)->update([
                'status' => 7
            ]); // return all to defaults
            $bunifu = NationalBrandCompetition::find($request->competitionNumber);
            $bunifu->update([
                'status' => 2,
            ]);
            return back()->with('success', config('Messages.200'));
        }
        }
        
        return view('reports.TNBReports', compact('applications'));
    }

    public function SubmitMarks(Request $request)
    {
        $criteria=TnbCriteria::where('criteriaCode',$request->criteria)->first();
        $request->validate([
            'marks'=>"required|numeric|max:$criteria->maxMark",
        ]);
        $exists=TnbMark::where('tnbId',$request->tnbId)
        ->where('judgeNumber',session('judgingNumber'))
        ->where('criteria',$request->criteria)->first();
        if ($exists) {
            return back()->with('fail','Marks already Exist!');
        }
        TnbMark::create([
            'markId'=>Str::uuid(),
            'tnbId'=>$request->tnbId,
            'criteria'=>$request->criteria,
            'judgeNumber'=>session('judgingNumber'),
            'marks'=>$request->marks,
        ]);
        $totalMarks=TnbMark::where('tnbId',$request->tnbId)->sum('marks');
        $averageMarks=number_format($totalMarks/6,2);
         NationalBrandCompetition::find($request->tnbId)
            ->update([
                'averageMarks' => $averageMarks,
                'totalMarks'=>$totalMarks
            ]);
        return back()->with('success','Marks submited successfully!');
    }

    public function TnblcMarking(Request $request, $applicantId)
    {
        if ($applicantId == 'J01' || $applicantId == 'J02' || $applicantId == 'J03' || $applicantId == 'J04' || $applicantId == 'J05'||$applicantId == 'J06') {
            $applicant = NationalBrandCompetition::where('status', 2)->get();
            session(['judgingNumber' => $applicantId]);

            $exists=TnbMark::where('tnbId',NationalBrandCompetition::where('status', 2)->first()->competitionNumber)
        ->where('judgeNumber',session('judgingNumber'))
        ->get(['criteria']);
            
            $creteria=TnbCriteria::orderBy('createdAt','ASC')->whereNotIn('criteriaCode',$exists)
            ->get();
            return view('TNBLCMARKING', compact('applicant','creteria'));
        }
    }


    public function sendNotificationToApplicants(Request $request,$competitionNumber){
        $tnb=NationalBrandCompetition::find($competitionNumber);
        $data = [
            'name' => $tnb->fullName,
            'competitionNumber' => $competitionNumber,
            'emailAddress' => $tnb->emailAddress,
            'subject' => 'Tanzania National Brand Logo Competition',
        ];
        Mail::send('mails.TNBCMAIL', $data, function ($message) use ($data) {
            $message
                ->to($data['emailAddress'], $data['name'])
                ->bcc('madeintanzania@tantrade.go.tz')
                ->bcc('dg@tantrade.go.tz')
                ->priority(1)
                ->subject($data['subject']);
        });
        $tnb->update([
            'status'=>'5',
        ]);
        return back()->with('success',config('Messages.200'));
    }

    public function verifyAttending(Request $request,$competitionNumber){
        $tnb=NationalBrandCompetition::where('status','>=',5)->where('competitionNumber',$competitionNumber)->first();
        if ($tnb) {
           $tnb->update([
            'status'=>6
           ]);
           return redirect()->route('tnblc')->with('success','Thank you for confirming participating in Tanzania National Brand Logo design online Training, get prepared.');
        }

        return redirect()->route('tnblc')->with('fail','Sorry! We appriciate your efforts but this competition Number is not shortlisted for the second round!');
        
    }
}
