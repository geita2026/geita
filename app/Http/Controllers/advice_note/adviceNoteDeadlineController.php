<?php

namespace App\Http\Controllers\advice_note;

use App\Http\Controllers\Controller;
use App\Models\advice_note\advice_note_deadlines;
use App\Models\advice_note\advice_notes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Traits\IntergrationTraits;
use Spatie\Permission\Models\Role;

class adviceNoteDeadlineController extends Controller
{
    use IntergrationTraits;

    /*
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Create AdviceNote Deadline', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        //
        $today = date('Y-m-d');
        if ($today >= $request->deadline_date) {
            return back()->with('fail', 'Selected date is in expired limit!');
        }
        $request->validate([
            'deadline_date' => 'required',
            'advice_note_id' => 'unique:advice_note_deadlines,advice_note_id'
        ]);

        $deadline = advice_note_deadlines::create([
            'deadline_id' => Str::uuid(),
            'user_id' => Auth::user()->user_uuid,
            'advice_note_id' => $request->advice_note_id,
            'deadline_date' => $request->deadline_date,
        ]);
        if ($deadline) {
            return back()->with('success', 'Advice note Deadline set successfully!');
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
            if ($request->user()->cannot('Extend Deadline', Role::class)) {
                return back()->with('fail', config('Messages.401'));
            }
            $request->validate([
                'reasons' => 'required',
                'expire_date' => 'required',
            ]);
            $invoice = advice_notes::leftJoin('companies', 'advice_notes.company_id', 'companies.companyID')
                ->where('advice_note_id', $id)->first();
            if ($invoice) {
                if ($invoice->advice_note_deadline > $request->expire_date) {
                    return back()->with('fail', config('Messages.500'));
                }
                $new_deadline = date('d-m-Y', strtotime($request->expire_date));
                $data = array(
                    'customer_no' => $invoice->sp_customer_no,
                    'invoice_no' => $invoice->sp_invoice_no,
                    'reference' => $invoice->advice_note_id,
                    'submittedby' => Auth::user()->first_name,
                    'reason' => $request->reasons,
                    'expire_date' => $new_deadline,
                );
            }
            $status = $this->ReuseControlNumber($id, $data);
            if ($status == 1) {
                $invoice->update([
                    'advice_note_deadline' => $new_deadline,
                ]);
                return back()->with('success', config('Messages.200'));
            } else {
                return back()->with('fail', config('Messages.500'));
            }
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
