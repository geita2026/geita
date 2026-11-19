<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Application;
use App\Models\Exhibition;
use App\Models\Space;
use App\Services\AllocationService;
use App\Services\Audit;
use App\Services\Notifier;
use Illuminate\Http\Request;

class AllocationController extends Controller
{
    public function __construct(private AllocationService $service) {}

    public function index(Request $request)
    {
        $user = $this->actor();
        $query = Allocation::with(['exhibitor:id,organization_name,phone', 'space:id,number,hall_id,space_category_id,width_m,length_m,price,status',
            'space.hall:id,name,code', 'space.category:id,name,color', 'application:id,reference,status', 'allocator:id,name']);

        if (! $user->isStaff()) {
            $query->where('exhibitor_id', $user->exhibitor_id);
        }
        foreach (['exhibition_id', 'exhibitor_id', 'application_id'] as $filter) {
            if ($value = $request->query($filter)) {
                $query->where($filter, $value);
            }
        }
        if ($status = $request->query('status')) {
            $query->whereIn('status', explode(',', $status));
        }
        if ($occupancy = $request->query('occupancy')) {
            $query->whereIn('occupancy_status', explode(',', $occupancy));
        }
        if ($q = $request->query('q')) {
            $query->where(fn ($w) => $w->where('reference', 'like', "%$q%")
                ->orWhereHas('exhibitor', fn ($e) => $e->where('organization_name', 'like', "%$q%"))
                ->orWhereHas('space', fn ($s) => $s->where('number', 'like', "%$q%")));
        }

        return $query->latest('allocated_at')->paginate($this->perPage());
    }

    /** FR-024 (officer) and FR-023 (exhibitor self-selection, allocated and invoiced at once). */
    public function store(Request $request)
    {
        $user = $this->actor();
        $data = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'space_id' => 'required|exists:spaces,id',
            'exception' => 'nullable|boolean',
        ]);
        $application = Application::findOrFail($data['application_id']);
        $space = Space::findOrFail($data['space_id']);

        if ($user->isExhibitor()) {
            $this->ownOrStaff($application->exhibitor_id);
            abort_unless($application->exhibition->allow_self_selection, 422, 'Space selection by exhibitors is switched off for this exhibition.');
            $allocation = $this->service->allocate($application, $space, $user, 'self');
        } else {
            $this->permit('allocations.manage');
            $allocation = $this->service->allocate($application, $space, $user, 'officer', (bool) ($data['exception'] ?? false));
        }

        return response()->json($allocation->load('space'), 201);
    }

    public function confirm(Request $request, Allocation $allocation)
    {
        $this->permit('allocations.manage');

        return $this->service->confirm($allocation, $this->actor());
    }

    /** FR-025 for one application. */
    public function auto(Request $request, Application $application)
    {
        $this->permit('allocations.manage');

        return response()->json($this->service->autoAllocate($application, $this->actor())->load('space'), 201);
    }

    /** FR-025 for every approved application in an exhibition. */
    public function autoBatch(Request $request, Exhibition $exhibition)
    {
        $this->permit('allocations.manage');

        return $this->service->autoAllocateExhibition($exhibition->id, $this->actor());
    }

    public function reallocate(Request $request, Allocation $allocation)
    {
        $this->permit('allocations.manage');
        $data = $request->validate(['space_id' => 'required|exists:spaces,id', 'reason' => 'required|string|min:5|max:1000']);

        return $this->service->reallocate($allocation, Space::findOrFail($data['space_id']), $this->actor(), $data['reason'])->load('space');
    }

    public function transfer(Request $request, Allocation $allocation)
    {
        $this->permit('allocations.manage');
        $data = $request->validate(['application_id' => 'required|exists:applications,id', 'reason' => 'required|string|min:5|max:1000']);

        return $this->service->transfer($allocation, Application::findOrFail($data['application_id']), $this->actor(), $data['reason'])->load('space');
    }

    public function cancel(Request $request, Allocation $allocation)
    {
        $user = $this->actor();
        if ($user->isExhibitor()) {
            $this->ownOrStaff($allocation->exhibitor_id);
            abort_unless($this->service->withdrawable($allocation), 403, 'This space can no longer be given back online (a payment has been made, or an officer allocated it). Contact the Exhibition Management Unit.');
        } else {
            $this->permit('allocations.manage');
        }
        $data = $request->validate(['reason' => 'required|string|min:5|max:1000']);

        return $this->service->cancel($allocation, $user, $data['reason']);
    }

    /** FR-040..042: check-in and occupancy. */
    public function occupancy(Request $request, Allocation $allocation)
    {
        $this->permit('checkin.manage');
        $data = $request->validate(['occupancy_status' => 'required|in:checked_in,occupied,vacant,not_occupied', 'notes' => 'nullable|string|max:255']);
        abort_unless($allocation->status === 'allocated', 422, 'Only an active allocation can be checked in.');
        abort_unless($allocation->application->status === 'confirmed', 422,
            'Participation is not confirmed yet — payment or documents are outstanding.');

        $from = $allocation->occupancy_status;
        $to = $data['occupancy_status'];
        $fresh = in_array($to, ['checked_in', 'occupied'], true) && ! $allocation->checked_in_at;

        $allocation->update([
            'occupancy_status' => $to,
            'checkin_notes' => $data['notes'] ?? $allocation->checkin_notes,
            'checked_in_at' => $fresh ? now() : $allocation->checked_in_at,
            'checked_in_by' => $fresh ? $this->actor()->id : $allocation->checked_in_by,
            'checkin_reference' => $fresh ? 'CHK-'.str_pad((string) $allocation->id, 6, '0', STR_PAD_LEFT) : $allocation->checkin_reference,
        ]);
        $allocation->space->update(['status' => $to === 'occupied' ? 'occupied' : 'allocated']);

        Audit::log('checkin.'.$to, "Space {$allocation->space->number}: occupancy $from → $to", $allocation, ['occupancy_status' => $from], ['occupancy_status' => $to]);
        if ($fresh) {
            Notifier::exhibitor($allocation->exhibitor, 'checkin', 'Check-in confirmed',
                "Check-in {$allocation->checkin_reference} recorded for space {$allocation->space->number}. Welcome to the exhibition.", "/applications/{$allocation->application_id}");
        }

        return $allocation;
    }
}
