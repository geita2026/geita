<?php

namespace App\Services;

use App\Exceptions\DomainException;
use App\Models\Allocation;
use App\Models\Application;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * FR-023..028 and BR-001, 002, 005, 006, 007.
 *
 * Double allocation is impossible by construction: every live allocation
 * carries active_space_id = space_id under a unique index, so the second
 * insert for the same space fails in the database even if two officers press
 * "Allocate" in the same millisecond.
 */
class AllocationService
{
    private const ALLOCATABLE = ['approved', 'pending_payment', 'confirmed'];

    public function __construct(private BillingService $billing) {}

    public function allocate(Application $application, Space $space, User $actor, string $source = 'officer', bool $exception = false): Allocation
    {
        return DB::transaction(function () use ($application, $space, $actor, $source, $exception) {
            $pending = $source === 'self';
            $this->guardApplication($application, $actor, $exception);
            $space = Space::whereKey($space->id)->lockForUpdate()->firstOrFail();
            $this->guardSpace($space, $application, $pending);

            $allocation = $this->open($application, $space, $actor, $source, $pending ? 'pending' : 'allocated');
            $space->update(['status' => $pending ? 'pending_approval' : 'allocated']);

            if ($pending) {
                Audit::log('space.selected', "{$application->exhibitor->organization_name} selected space {$space->number}", $allocation);
                Notifier::roles(['space_officer'], 'allocation', 'Space selection awaiting confirmation',
                    "{$application->exhibitor->organization_name} selected space {$space->number} ({$application->reference}).", '/allocations?status=pending');
            } else {
                $this->finalise($allocation, $application);
            }

            return $allocation;
        });
    }

    /** Officer confirms an exhibitor's self-selection. */
    public function confirm(Allocation $allocation, User $actor): Allocation
    {
        return DB::transaction(function () use ($allocation, $actor) {
            if ($allocation->status !== 'pending') {
                throw new DomainException('Only a pending selection can be confirmed.');
            }
            $allocation->update(['status' => 'allocated', 'allocated_by' => $actor->id, 'allocated_at' => now()]);
            $allocation->space->update(['status' => 'allocated']);
            $this->finalise($allocation, $allocation->application);

            return $allocation;
        });
    }

    /** FR-025: first available space that fits the application's category and size preference. */
    public function autoAllocate(Application $application, User $actor): Allocation
    {
        $query = Space::where('exhibition_id', $application->exhibition_id)->where('status', 'available');

        if ($application->space_category_id) {
            $query->where('space_category_id', $application->space_category_id);
        }
        if ($application->preferred_size && preg_match('/([\d.]+)\D+([\d.]+)/', $application->preferred_size, $m)) {
            $query->orderByRaw('ABS(area_sqm - ?)', [(float) $m[1] * (float) $m[2]]);
        }
        $space = $query->orderBy('number')->first();

        if (! $space) {
            throw new DomainException('No available space matches this application\'s category. Allocate manually or change the preference.', 'BR-005');
        }

        return $this->allocate($application, $space, $actor, 'auto');
    }

    /** Batch FR-025: approved applications by priority, then by submission time. */
    public function autoAllocateExhibition(int $exhibitionId, User $actor): array
    {
        $done = [];
        $skipped = [];
        $applications = Application::where('exhibition_id', $exhibitionId)->whereIn('status', ['approved', 'pending_payment'])
            ->withCount('activeAllocations')->orderBy('priority')->orderBy('submitted_at')->get();

        foreach ($applications as $application) {
            for ($i = $application->active_allocations_count; $i < $application->spaces_requested; $i++) {
                try {
                    $done[] = $this->autoAllocate($application->fresh(), $actor)->reference;
                } catch (DomainException $e) {
                    $skipped[] = ['application' => $application->reference, 'reason' => $e->getMessage()];
                    break;
                }
            }
        }

        return ['allocated' => $done, 'skipped' => $skipped];
    }

    /** FR-026: move the exhibitor to another space; a reason is mandatory. */
    public function reallocate(Allocation $allocation, Space $target, User $actor, string $reason): Allocation
    {
        return DB::transaction(function () use ($allocation, $target, $actor, $reason) {
            $this->guardChange($allocation, $actor);
            $application = $allocation->application;
            $target = Space::whereKey($target->id)->lockForUpdate()->firstOrFail();
            $this->guardSpace($target, $application, false, checkQuota: false);

            $from = $allocation->space;
            $this->close($allocation, 'reallocated', $actor, $reason);
            $new = $this->open($application, $target, $actor, 'officer', 'allocated', $allocation, $reason);
            $target->update(['status' => 'allocated']);
            $invoice = $this->billing->rebillAllocation($allocation, $new);
            if ($application->status === 'confirmed' && $invoice && $invoice->status !== 'paid') {
                $application->update(['status' => 'pending_payment', 'confirmed_at' => null]);
            }

            Audit::log('allocation.reallocated', "Reallocated {$application->exhibitor->organization_name} from {$from->number} to {$target->number}: $reason",
                $new, ['space' => $from->number], ['space' => $target->number, 'reason' => $reason]);
            Notifier::exhibitor($application->exhibitor, 'allocation', 'Your space has changed',
                "You have been moved from space {$from->number} to {$target->number}. Reason: $reason", "/applications/{$application->id}");

            return $new;
        });
    }

    /** FR-027: hand the same space to another exhibitor's approved application. */
    public function transfer(Allocation $allocation, Application $recipient, User $actor, string $reason): Allocation
    {
        return DB::transaction(function () use ($allocation, $recipient, $actor, $reason) {
            if (! $actor->allowed('allocations.authorize')) {
                throw new DomainException('Transferring a space between exhibitors requires authorisation by an Exhibition Administrator.', 'BR-006');
            }
            if (! $allocation->isLive()) {
                throw new DomainException('This allocation is no longer active.');
            }
            if ($recipient->exhibition_id !== $allocation->exhibition_id) {
                throw new DomainException('The receiving application belongs to a different exhibition.');
            }
            if ($recipient->exhibitor_id === $allocation->exhibitor_id) {
                throw new DomainException('The space already belongs to this exhibitor.');
            }
            $this->guardApplication($recipient, $actor, false);

            $space = $allocation->space;
            $from = $allocation->exhibitor;
            $old = $allocation->application;
            $this->close($allocation, 'transferred', $actor, $reason, freeSpace: false);
            $this->billing->unbillAllocation($allocation);
            $new = $this->open($recipient, $space, $actor, 'officer', 'allocated', $allocation, $reason);
            $space->update(['status' => 'allocated']);
            $this->finalise($new, $recipient, announce: false);
            $this->settleAfterLoss($old);

            Audit::log('allocation.transferred', "Space {$space->number} transferred from {$from->organization_name} to {$recipient->exhibitor->organization_name}: $reason",
                $new, ['exhibitor' => $from->organization_name], ['exhibitor' => $recipient->exhibitor->organization_name, 'reason' => $reason]);
            Notifier::exhibitor($from, 'allocation', 'Space transferred', "Space {$space->number} has been transferred to another exhibitor. Reason: $reason");
            Notifier::exhibitor($recipient->exhibitor, 'allocation', 'Space allocated', "Space {$space->number} has been transferred to you.", "/applications/{$recipient->id}");

            return $new;
        });
    }

    /** FR-028: cancel with date, reason and user. */
    public function cancel(Allocation $allocation, User $actor, string $reason): Allocation
    {
        return DB::transaction(function () use ($allocation, $actor, $reason) {
            // An exhibitor may withdraw a selection the officer has not confirmed yet.
            if (! ($allocation->status === 'pending' && $actor->exhibitor_id === $allocation->exhibitor_id)) {
                $this->guardChange($allocation, $actor);
            }
            $space = $allocation->space;
            $this->close($allocation, 'cancelled', $actor, $reason);
            $this->billing->unbillAllocation($allocation);
            $this->settleAfterLoss($allocation->application);

            Audit::log('allocation.cancelled', "Allocation of space {$space->number} cancelled: $reason", $allocation,
                ['status' => 'allocated'], ['status' => 'cancelled', 'reason' => $reason]);
            Notifier::exhibitor($allocation->exhibitor, 'allocation', 'Allocation cancelled', "The allocation of space {$space->number} was cancelled. Reason: $reason",
                "/applications/{$allocation->application_id}");

            return $allocation;
        });
    }

    /* ---------------------------------------------------------------- guards */

    private function guardApplication(Application $application, User $actor, bool $exception): void
    {
        if (in_array($application->status, self::ALLOCATABLE, true)) {
            return;
        }
        // BR-002: "…unless management authorizes an exception."
        if ($exception && $application->status === 'submitted' && $actor->allowed('allocations.authorize')) {
            return;
        }
        throw new DomainException("Application {$application->reference} is {$application->status}; only approved applications can be allocated space.", 'BR-002');
    }

    private function guardSpace(Space $space, Application $application, bool $selfService, bool $checkQuota = true): void
    {
        if ($space->exhibition_id !== $application->exhibition_id) {
            throw new DomainException('This space belongs to a different exhibition.');
        }
        $free = $selfService ? ['available'] : ['available', 'reserved'];
        if (! in_array($space->status, $free, true)) {
            throw new DomainException("Space {$space->number} is ".str_replace('_', ' ', $space->status).' and cannot be allocated.', 'BR-005');
        }
        if ($checkQuota && $application->activeAllocations()->count() >= $application->spaces_requested) {
            throw new DomainException("This application asked for {$application->spaces_requested} space(s) and already holds that many.");
        }
    }

    /** BR-006: a confirmed (paid) allocation changes only with authorisation. */
    private function guardChange(Allocation $allocation, User $actor): void
    {
        if (! $allocation->isLive()) {
            throw new DomainException('This allocation is no longer active.');
        }
        if ($allocation->application->status === 'confirmed' && ! $actor->allowed('allocations.authorize')) {
            throw new DomainException('This participation is confirmed. Changes need authorisation by an Exhibition Administrator.', 'BR-006');
        }
    }

    /* --------------------------------------------------------------- plumbing */

    private function open(Application $application, Space $space, User $actor, string $source, string $status, ?Allocation $previous = null, ?string $reason = null): Allocation
    {
        try {
            $allocation = Allocation::create([
                'reference' => Ref::temp(),
                'application_id' => $application->id,
                'exhibition_id' => $application->exhibition_id,
                'exhibitor_id' => $application->exhibitor_id,
                'space_id' => $space->id,
                'active_space_id' => $space->id,
                'status' => $status,
                'source' => $source,
                'allocated_by' => $actor->id,
                'allocated_at' => now(),
                'previous_allocation_id' => $previous?->id,
                'change_reason' => $reason,
            ]);
        } catch (UniqueConstraintViolationException) {
            throw new DomainException("Space {$space->number} has just been taken by another exhibitor. Choose a different space.", 'BR-001');
        }
        Ref::assign($allocation, 'reference', 'ALC', $application->exhibition->code);

        return $allocation;
    }

    private function close(Allocation $allocation, string $status, User $actor, string $reason, bool $freeSpace = true): void
    {
        $allocation->update([
            'status' => $status, 'active_space_id' => null, 'ended_by' => $actor->id, 'ended_at' => now(),
            'change_reason' => $reason, 'occupancy_status' => $status === 'cancelled' ? 'cancelled' : 'vacant',
        ]);
        if ($freeSpace) {
            $allocation->space->update(['status' => 'available']);
        }
    }

    private function finalise(Allocation $allocation, Application $application, bool $announce = true): void
    {
        $invoice = $this->billing->billAllocation($allocation);

        if ($application->status === 'approved' || $application->status === 'submitted') {
            $application->update(['status' => 'pending_payment']);
        } elseif ($application->status === 'confirmed' && $invoice->status !== 'paid') {
            $application->update(['status' => 'pending_payment', 'confirmed_at' => null]);
        }

        if ($announce) {
            $space = $allocation->space;
            Audit::log('allocation.created', "Space {$space->number} allocated to {$application->exhibitor->organization_name}", $allocation,
                null, ['space' => $space->number, 'source' => $allocation->source]);
            Notifier::exhibitor($application->exhibitor, 'allocation', 'Space allocated',
                "Space {$space->number} ({$space->size_label}) has been allocated to you for {$application->exhibition->name}.", "/applications/{$application->id}");
        }
    }

    /** An application that lost its last space goes back to "approved". */
    private function settleAfterLoss(Application $application): void
    {
        if ($application->activeAllocations()->count() === 0 && in_array($application->status, ['pending_payment', 'confirmed'], true)) {
            $application->update(['status' => 'approved', 'confirmed_at' => null]);
        }
    }
}
