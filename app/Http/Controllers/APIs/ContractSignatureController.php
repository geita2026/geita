<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContractSignatureController extends Controller
{
    /**
     * Get company information by registration ID
     * Endpoint: GET /api/contract/get-company?registrationID=xxx
     */
    public function getCompany(Request $request)
    {
        $registrationID = $request->query('registrationID');

        if (!$registrationID) {
            return response()->json([
                'status' => 0,
                'message' => 'Registration ID is required'
            ], 400);
        }

        $company = DB::table('companies')
            ->where('registrationID', $registrationID)
            ->first();

        if (!$company) {
            return response()->json([
                'status' => 0,
                'message' => 'Company not found with this Registration ID'
            ], 404);
        }

        // Find latest related invoice/advice_note (if any) to determine payment status
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;

        $latestInvoice = DB::table('advice_notes')
            ->where('company_id', $company->companyID)
            ->whereNotNull('control_number')
            ->where(function ($q) use ($currentYear, $previousYear) {
                $q->whereYear('updated_at', $currentYear)
                  ->orWhereYear('updated_at', $previousYear);
            })
            ->orderBy('updated_at', 'desc')
            ->first();

        $isPaid = false;
        $invoiceData = null;
        if ($latestInvoice) {
            $balance = is_null($latestInvoice->balance) ? 0 : (float) $latestInvoice->balance;
            $amountPaid = is_null($latestInvoice->amount_paid) ? 0 : (float) $latestInvoice->amount_paid;
            $total = is_null($latestInvoice->total_amount) ? 0 : (float) $latestInvoice->total_amount;
            $isPaid = ($balance == 0) || ($total > 0 && $amountPaid >= $total);
            $invoiceData = [
                'advice_note_id' => $latestInvoice->advice_note_id ?? null,
                'control_number' => $latestInvoice->control_number ?? null,
                'balance' => $balance,
                'amount_paid' => $amountPaid,
                'total_amount' => $total,
            ];
        }

        return response()->json([
            'status' => 1,
            'message' => 'Company found',
            'data' => [
                'company_name' => $company->companyName,
                'address' => $company->companyAddress ?? '',
                'email' => $company->companyEmail ?? '',
                'phone' => $company->companyPhone ?? $company->companyTel ?? '',
                'latest_invoice' => $invoiceData,
                'is_paid' => $isPaid,
            ]
        ]);
    }

    /**
     * Check if signature exists for this registration ID
     * Endpoint: GET /api/contract/check-signature?registrationID=xxx
     */
    public function checkSignature(Request $request)
    {
        $registrationID = $request->query('registrationID');

        if (!$registrationID) {
            return response()->json([
                'status' => 0,
                'message' => 'Registration ID is required'
            ], 400);
        }

        $signatures = DB::table('signed_contracts')
            ->where('registration_id', $registrationID)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($signatures->isEmpty()) {
            return response()->json([
                'status' => 1,
                'has_signature' => false,
                'is_complete' => false,
                'message' => 'No signatures found'
            ]);
        }

        $isComplete = $signatures->count() >= 2;
        $latestSignature = $signatures->first();

        // Also include invoice/payment status for this registration's company (if any)
        $company = DB::table('companies')->where('registrationID', $registrationID)->first();
        $isPaid = false;
        $invoiceData = null;
        if ($company) {
            $currentYear = date('Y');
            $previousYear = $currentYear - 1;
            $latestInvoice = DB::table('advice_notes')
                ->where('company_id', $company->companyID)
                ->whereNotNull('control_number')
                ->where(function ($q) use ($currentYear, $previousYear) {
                    $q->whereYear('updated_at', $currentYear)
                      ->orWhereYear('updated_at', $previousYear);
                })
                ->orderBy('updated_at', 'desc')
                ->first();
            if ($latestInvoice) {
                $balance = is_null($latestInvoice->balance) ? 0 : (float) $latestInvoice->balance;
                $amountPaid = is_null($latestInvoice->amount_paid) ? 0 : (float) $latestInvoice->amount_paid;
                $total = is_null($latestInvoice->total_amount) ? 0 : (float) $latestInvoice->total_amount;
                $isPaid = ($balance == 0) || ($total > 0 && $amountPaid >= $total);
                $invoiceData = [
                    'advice_note_id' => $latestInvoice->advice_note_id ?? null,
                    'control_number' => $latestInvoice->control_number ?? null,
                    'balance' => $balance,
                    'amount_paid' => $amountPaid,
                    'total_amount' => $total,
                ];
            }
        }

        return response()->json([
            'status' => 1,
            'has_signature' => true,
            'is_complete' => $isComplete,
            'is_paid' => $isPaid,
            'latest_invoice' => $invoiceData,
            'data' => [
                'id' => $latestSignature->id,
                'signer_type' => $latestSignature->signer_type,
                'first_name' => $latestSignature->first_name,
                'middle_name' => $latestSignature->middle_name,
                'last_name' => $latestSignature->last_name,
                'qualification' => $latestSignature->qualification,
                'address' => $latestSignature->address,
                'signature_path' => url('/api/contract/signature-image?path=' . urlencode($latestSignature->signature_path)),
                'created_at' => $latestSignature->created_at,
            ],
            'signatures' => $signatures->map(function ($sig) {
                $sig->signature_path = url('/api/contract/signature-image?path=' . urlencode($sig->signature_path));
                return $sig;
            }),
        ]);
    }

    /**
     * Serve a signature image file directly through Laravel
     * Endpoint: GET /api/contract/signature-image?path=signatures/filename.png
     */
    public function signatureImage(Request $request)
    {
        $path = $request->query('path');

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Signature image not found');
        }

        // Prevent directory traversal
        if (str_contains($path, '..')) {
            abort(403);
        }

        return response(Storage::disk('public')->get($path), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Upload signature image + signer data (PDF generated on-the-fly by Flutter app)
     * Endpoint: POST /api/contract/upload
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registration_id' => 'required|string',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'qualification' => 'nullable|string|max:200',
            'address' => 'nullable|string',
            'signer_type' => 'required|in:exhibitor,witness',
            'signature_image' => 'required|file|mimes:png,jpg,jpeg|max:2048',
            'existing_signature_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $signatureFile = $request->file('signature_image');
            $signatureFileName = $request->registration_id . '_' . $request->signer_type . '_' . time() . '.png';
            $signaturePath = $signatureFile->storeAs('signatures', $signatureFileName, 'public');

            $signatureId = DB::table('signed_contracts')->insertGetId([
                'registration_id' => $request->registration_id,
                'signer_type' => $request->signer_type,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'qualification' => $request->qualification,
                'address' => $request->address,
                'signature_path' => $signaturePath,
                'related_signature_id' => $request->existing_signature_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->existing_signature_id) {
                DB::table('signed_contracts')
                    ->where('id', $request->existing_signature_id)
                    ->update([
                        'related_signature_id' => $signatureId,
                        'updated_at' => now(),
                    ]);
            }

            $signatureCount = DB::table('signed_contracts')
                ->where('registration_id', $request->registration_id)
                ->count();

            return response()->json([
                'status' => 1,
                'message' => 'Signature uploaded successfully',
                'signature_id' => $signatureId,
                'both_signatures_complete' => $signatureCount >= 2,
            ]);

        } catch (\Exception $e) {
            \Log::error('Contract upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'registration_id' => $request->registration_id,
            ]);
            return response()->json([
                'status' => 0,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
