<?php

namespace App\Http\Controllers\messaging;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Notifications\CreatedEmails;
use App\Models\sectors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;

class messagingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('VIEW_MESSAGING_SUBMENU', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_MESSAGING_SUBMENU'));
        }
        $draft_messages = CreatedEmails::get();
        $sectors = sectors::orderBy('sectorName', 'ASC')->get();

        return view('Messaging.customized_emails', compact('draft_messages', 'sectors'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('DELETE_MESSAGING', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('DELETE_MESSAGING'));
        }
        try {
            $email = CreatedEmails::get()->where('email_status', 0);
            foreach ($email as $value) {
                $emails = CreatedEmails::find($value->emailId);
                File::delete("public/mail_files/" . $value->image);
                $emails->delete();
            }
            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail', config('Messages.500'));
        }
    }


    public function CreateCreatedEmails(Request $request){
        try {
            $exist=CreatedEmails::where('emailTo',$request->emailTo)->where('emailSubject',$request->emailSubject)->first();
            if(!$exist){
            CreatedEmails::create([
                'emailId' => Str::uuid(),
                'createdBy' => $request->createdBy,
                'emailTo' => $request->emailTo,
                'linkLable' => $request->linkLable,
                'receiverName' => $request->receiverName,
                'regardsName' => $request->regardsName,
                'emailSubject' => $request->emailSubject,
                'emailLink' => $request->emailLink,
                'emailContent' => $request->emailContent,
                'emailStatus' =>'PENDING',
                'scheduledAt' => date('Y-m-d h:i:s')
            ]);
        }
        } catch (\Throwable $th) {
        }
    }
}
