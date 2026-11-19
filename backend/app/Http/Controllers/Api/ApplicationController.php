<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Exhibition;
use App\Models\Exhibitor;
use App\Services\Audit;
use App\Services\Notifier;
use App\Services\Ref;
use App\Support\ExhibitorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->actor();
        $query = Application::with(['exhibitor:id,organization_name,country,exhibitor_type', 'exhibition:id,code,name', 'category:id,name,color'])
            ->withCount(['activeAllocations', 'documents']);

        if (! $user->isStaff()) {
            $query->where('exhibitor_id', $user->exhibitor_id);
        } else {
            $query->where('status', '!=', 'draft'); // a draft is the exhibitor's own business
        }
        foreach (['exhibition_id', 'exhibitor_id', 'space_category_id'] as $filter) {
            if ($value = $request->query($filter)) {
                $query->where($filter, $value);
            }
        }
        if ($status = $request->query('status')) {
            $query->whereIn('status', explode(',', $status));
        }
        if ($from = $request->query('from')) {
            $query->whereDate('submitted_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('submitted_at', '<=', $to);
        }
        if ($q = $request->query('q')) {
            $query->where(fn ($w) => $w->where('reference', 'like', "%$q%")->orWhere('product_category', 'like', "%$q%")
                ->orWhereHas('exhibitor', fn ($e) => $e->where('organization_name', 'like', "%$q%")));
        }

        return $query->latest('updated_at')->paginate($this->perPage());
    }

    public function show(Application $application)
    {
        $this->ownOrStaff($application->exhibitor_id);

        return $application->load([
            'exhibitor', 'exhibition', 'category', 'documents', 'reviewer:id,name',
            'allocations' => fn ($q) => $q->with('space.hall:id,name,code', 'space.category:id,name,color', 'allocator:id,name')->latest(),
            'invoices' => fn ($q) => $q->latest(),
        ])->setAttribute('missing_documents', $application->missingDocuments());
    }

    private function rules(Exhibition $exhibition): array
    {
        return [
            'space_category_id' => ['nullable', Rule::exists('space_categories', 'id')->where('exhibition_id', $exhibition->id)],
            'preferred_size' => 'nullable|string|max:40',
            'spaces_requested' => 'required|integer|min:1|max:20',
            'product_category' => 'required|string|max:255',
            'products_description' => 'nullable|string|max:3000',
            'special_requirements' => 'nullable|string|max:3000',
        ];
    }

    /** FR-014: saved as a draft first; documents are attached before submission. */
    public function store(Request $request)
    {
        $user = $this->actor();
        $exhibition = Exhibition::findOrFail($request->validate(['exhibition_id' => 'required|integer'])['exhibition_id']);
        // Customer Service may capture an application on an exhibitor's behalf.
        if ($user->isExhibitor()) {
            $exhibitorId = $user->exhibitor_id;
        } else {
            $this->permit('exhibitors.manage');
            $exhibitorId = $request->validate(['exhibitor_id' => 'required|exists:exhibitors,id'])['exhibitor_id'];
        }

        abort_unless($exhibition->registration_open, 422, "Registration for {$exhibition->name} is not open."); // BR-010

        // Who is applying: an individual or a registered business, with their details.
        // Required from the exhibitor; staff capturing on someone's behalf may leave
        // an already-complete profile as it is.
        $exhibitor = Exhibitor::findOrFail($exhibitorId);
        if ($user->isExhibitor() || $request->filled('entity_type') || ! $exhibitor->entity_type) {
            $profile = $request->validate(ExhibitorProfile::rules($request->input('entity_type')));
            ExhibitorProfile::apply($exhibitor, $profile, $user->isExhibitor() ? $user->name : $exhibitor->contact_person);
        }
        abort_if(Application::where('exhibition_id', $exhibition->id)->where('exhibitor_id', $exhibitorId)->whereNotIn('status', ['rejected', 'cancelled'])->exists(),
            422, 'This exhibitor already has an application for this exhibition.');

        $application = Application::create($request->validate($this->rules($exhibition)) + [
            'reference' => Ref::temp(), 'exhibition_id' => $exhibition->id, 'exhibitor_id' => $exhibitorId, 'status' => 'draft',
        ]);
        Ref::assign($application, 'reference', 'APP', $exhibition->code);
        Audit::log('application.created', "Application {$application->reference} drafted", $application);

        return response()->json($application, 201);
    }

    public function update(Request $request, Application $application)
    {
        $this->ownOrStaff($application->exhibitor_id);
        abort_unless(in_array($application->status, ['draft', 'returned'], true), 422, 'Only a draft or returned application can be edited.');
        $application->update($request->validate($this->rules($application->exhibition)));

        return $application;
    }

    public function submit(Request $request, Application $application)
    {
        $this->ownOrStaff($application->exhibitor_id);
        abort_unless(in_array($application->status, ['draft', 'returned'], true), 422, 'This application has already been submitted.');
        abort_unless($application->exhibition->registration_open, 422, 'Registration for this exhibition has closed.');
        if ($missing = $application->missingDocuments()) {
            abort(422, 'Upload the required documents before submitting: '.implode(', ', $missing).'.'); // BR-003
        }

        $application->update(['status' => 'submitted', 'submitted_at' => now(), 'review_notes' => null]);
        Audit::log('application.submitted', "Application {$application->reference} submitted", $application, null, ['status' => 'submitted']);
        Notifier::exhibitor($application->exhibitor, 'application', 'Application received',
            "Application {$application->reference} for {$application->exhibition->name} has been submitted and is awaiting review.", "/applications/{$application->id}");
        Notifier::roles(['registration_officer'], 'application', 'Application to review',
            "{$application->exhibitor->organization_name} submitted {$application->reference}.", "/applications/{$application->id}");

        return $application;
    }

    /** FR-018..021: approve, reject (with reason) or return for correction (with reason). */
    public function review(Request $request, Application $application)
    {
        $this->permit('applications.review');
        $data = $request->validate([
            'decision' => 'required|in:approve,reject,return',
            'notes' => 'required_unless:decision,approve|nullable|string|max:2000',
            'priority' => 'nullable|integer|min:1|max:5',
        ]);
        abort_unless($application->status === 'submitted', 422, "Application {$application->reference} is {$application->status}; only submitted applications can be reviewed.");

        $status = ['approve' => 'approved', 'reject' => 'rejected', 'return' => 'returned'][$data['decision']];
        if ($status === 'approved' && ($missing = $application->missingDocuments())) {
            abort(422, 'Mandatory documents are missing: '.implode(', ', $missing).'. Return the application instead.'); // BR-003
        }

        $application->update([
            'status' => $status, 'reviewed_by' => $this->actor()->id, 'reviewed_at' => now(),
            'review_notes' => $data['notes'] ?? null, 'priority' => $data['priority'] ?? $application->priority,
        ]);
        Audit::log("application.$status", "Application {$application->reference} $status".(! empty($data['notes']) ? ": {$data['notes']}" : ''),
            $application, ['status' => 'submitted'], ['status' => $status]);

        [$title, $body] = match ($status) {
            'approved' => ['Application approved', "Application {$application->reference} is approved. ".($application->exhibition->allow_self_selection
                ? 'Open the space map to choose your space.' : 'A Space Allocation Officer will allocate your space.')],
            'rejected' => ['Application not approved', "Application {$application->reference} was rejected. Reason: {$data['notes']}"],
            'returned' => ['Application returned for correction', "Application {$application->reference} needs correction: {$data['notes']}"],
        };
        Notifier::exhibitor($application->exhibitor, 'application', $title, $body, "/applications/{$application->id}");

        return $application;
    }

    public function cancel(Request $request, Application $application)
    {
        $this->ownOrStaff($application->exhibitor_id);
        $data = $request->validate(['reason' => 'required|string|max:1000']);
        abort_if(in_array($application->status, ['cancelled', 'rejected'], true), 422, 'This application is already closed.');
        abort_if($application->activeAllocations()->exists(), 422, 'Cancel the space allocations on this application first.');

        $from = $application->status;
        $application->update(['status' => 'cancelled', 'review_notes' => $data['reason']]);
        Audit::log('application.cancelled', "Application {$application->reference} cancelled: {$data['reason']}", $application, ['status' => $from], ['status' => 'cancelled']);

        return $application;
    }

    /* ------------------------------------------------------- documents (FR-016) */

    public function uploadDocument(Request $request, Application $application)
    {
        $this->ownOrStaff($application->exhibitor_id);
        abort_if(in_array($application->status, ['rejected', 'cancelled'], true), 422, 'This application is closed.');
        $data = $request->validate([
            'type' => 'required|string|max:100',
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);
        $file = $request->file('file');
        $document = $application->documents()->create([
            'type' => $data['type'], 'original_name' => $file->getClientOriginalName(),
            'path' => $file->store("applications/{$application->id}", 'local'),
            'mime' => $file->getMimeType(), 'size' => $file->getSize(), 'uploaded_by' => $this->actor()->id,
        ]);
        Audit::log('document.uploaded', "Document \"{$data['type']}\" uploaded to {$application->reference}", $application);

        return response()->json($document, 201);
    }

    public function downloadDocument(ApplicationDocument $document)
    {
        $this->ownOrStaff($document->application->exhibitor_id);
        abort_unless(Storage::disk('local')->exists($document->path), 404);

        return Storage::disk('local')->download($document->path, $document->original_name);
    }

    public function destroyDocument(ApplicationDocument $document)
    {
        $application = $document->application;
        $this->ownOrStaff($application->exhibitor_id);
        abort_unless(in_array($application->status, ['draft', 'returned'], true), 422, 'Documents can only be removed from a draft or returned application.');
        Storage::disk('local')->delete($document->path);
        Audit::log('document.deleted', "Document \"{$document->type}\" removed from {$application->reference}", $application);
        $document->delete();

        return response()->noContent();
    }
}
