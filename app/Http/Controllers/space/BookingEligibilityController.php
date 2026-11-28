<?php

namespace App\Http\Controllers\space;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\sectors;
use App\Models\space\AreaDistribution;
use App\Models\space\booths;
use App\Models\space\pavilions;
use App\Services\SpaceBookingEligibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class BookingEligibilityController extends Controller
{
    public function edit(Request $request, string $type, string $id, SpaceBookingEligibility $eligibility)
    {
        $space = $this->space($request, $type, $id);
        $isBooth = $space instanceof booths;
        $sectorIds = $eligibility->sectorIds($space)->all();
        $companyId = $space->whoCanBook;
        $companyOnly = $companyId && $companyId !== 'ANY';
        $mode = match (true) {
            $companyOnly && $sectorIds !== [] => 'legacy',
            (bool) $companyOnly => 'company',
            $sectorIds !== [] => 'sectors',
            $isBooth && !$companyId => 'inherit',
            default => 'any',
        };
        $selectedCompanyId = old('company_id', $companyOnly ? $companyId : null);
        $company = is_string($selectedCompanyId)
            ? companies::find($selectedCompanyId, ['companyID', 'companyName'])
            : null;
        $sectors = sectors::orderBy('sectorName')->get(['sectorID', 'sectorName']);
        $backUrl = $isBooth ? route('booths.show', $space->pavilionID) : route('pavilions.index');

        return view('space.booking-eligibility', compact(
            'space', 'type', 'isBooth', 'sectorIds', 'mode', 'company', 'sectors', 'backUrl'
        ));
    }

    public function update(Request $request, string $type, string $id)
    {
        $space = $this->space($request, $type, $id);
        $modes = ['any', 'company', 'sectors'];
        if ($space instanceof booths) {
            $modes[] = 'inherit';
        }
        $data = $request->validate([
            'mode' => ['required', Rule::in($modes)],
            'company_id' => ['exclude_unless:mode,company', 'required', 'string', 'exists:companies,companyID'],
            'sector_ids' => ['exclude_unless:mode,sectors', 'required', 'array', 'min:1'],
            'sector_ids.*' => ['exclude_unless:mode,sectors', 'required', 'string', 'distinct', 'exists:sectors,sectorID'],
        ]);

        DB::transaction(function () use ($space, $data) {
            $space = $space->newQuery()->lockForUpdate()->findOrFail($space->getKey());
            $sectorIds = $data['sector_ids'] ?? [];
            AreaDistribution::where('spaceId', $space->getKey())->delete();
            foreach ($sectorIds as $sectorId) {
                AreaDistribution::create([
                    'distributionId' => Str::uuid(),
                    'spaceId' => $space->getKey(),
                    'participants' => $sectorId,
                ]);
            }
            $space->whoCanBook = match ($data['mode']) {
                'company' => $data['company_id'],
                'any' => 'ANY',
                default => null,
            };
            $space->allowedForBooking = sectors::whereIn('sectorID', $sectorIds)
                ->orderBy('sectorName')->pluck('sectorName')->implode(', ');
            $space->save();
        });

        return redirect()->route('space-eligibility.edit', [$type, $id])
            ->with('success', 'Booking eligibility saved.');
    }

    private function space(Request $request, string $type, string $id): booths|pavilions
    {
        abort_if($request->user()->cannot('MANAGE_SPACES', Role::class), 403);
        if ($type === 'booths') {
            abort_if($request->user()->cannot('UPDATE_BOOTH', Role::class), 403);

            return booths::findOrFail($id);
        }

        return pavilions::findOrFail($id);
    }
}
