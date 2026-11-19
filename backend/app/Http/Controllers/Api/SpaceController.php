<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Exhibition;
use App\Models\Hall;
use App\Models\Space;
use App\Models\SpaceCategory;
use App\Services\Audit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpaceController extends Controller
{
    /** FR-022, FR-056/057: list with filters. Exhibitors never see who holds a space. */
    public function index(Request $request, Exhibition $exhibition)
    {
        $staff = $this->actor()->isStaff();
        $query = $exhibition->spaces()->with(['hall:id,code,name', 'category:id,code,name,color']);

        if ($staff) {
            $query->with(['activeAllocation' => fn ($q) => $q->select('id', 'reference', 'space_id', 'active_space_id', 'exhibitor_id', 'application_id', 'status', 'occupancy_status')
                ->with('exhibitor:id,organization_name')]);
        }
        foreach (['hall_id', 'space_category_id', 'status'] as $filter) {
            if ($value = $request->query($filter)) {
                $query->whereIn($filter, explode(',', $value));
            }
        }
        if ($q = $request->query('q')) {
            $query->where(fn ($w) => $w->where('number', 'like', "%$q%")->orWhere('location_note', 'like', "%$q%"));
        }
        if ($min = $request->query('min_area')) {
            $query->where('area_sqm', '>=', (float) $min);
        }
        if ($max = $request->query('max_price')) {
            $query->where('price', '<=', (float) $max);
        }

        $spaces = $query->orderBy('hall_id')->orderBy('map_row')->orderBy('map_col')->orderBy('number')->get();

        // The exhibitor's own holdings are marked so the map can show "yours".
        if (! $staff && $this->actor()->exhibitor_id) {
            $mine = Allocation::where('exhibitor_id', $this->actor()->exhibitor_id)->whereNotNull('active_space_id')->pluck('status', 'space_id');
            $spaces->each(fn ($s) => $s->setAttribute('mine', $mine[$s->id] ?? null));
        }

        return $spaces;
    }

    private function rules(Exhibition $exhibition, ?Space $space = null): array
    {
        return [
            'hall_id' => ['required', Rule::exists('halls', 'id')->where('exhibition_id', $exhibition->id)],
            'space_category_id' => ['required', Rule::exists('space_categories', 'id')->where('exhibition_id', $exhibition->id)],
            'number' => ['required', 'string', 'max:20', Rule::unique('spaces')->where('exhibition_id', $exhibition->id)->ignore($space?->id)],
            'width_m' => 'required|numeric|min:0.5|max:500',
            'length_m' => 'required|numeric|min:0.5|max:500',
            'price' => 'nullable|numeric|min:0',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:60',
            'location_note' => 'nullable|string|max:255',
            'special_conditions' => 'nullable|string|max:255',
            'map_col' => 'nullable|integer|min:1|max:60',
            'map_row' => 'nullable|integer|min:1|max:200',
            'map_w' => 'nullable|integer|min:1|max:12',
            'map_h' => 'nullable|integer|min:1|max:12',
        ];
    }

    private function priced(array $data): array
    {
        $data['area_sqm'] = round($data['width_m'] * $data['length_m'], 2);
        // FR-008: the category's rate prices the space unless a specific price is given.
        if (! isset($data['price']) || $data['price'] === null || $data['price'] === '') {
            $data['price'] = round($data['area_sqm'] * SpaceCategory::find($data['space_category_id'])->price_per_sqm, 2);
        }

        return $data;
    }

    /** FR-005, FR-007 */
    public function store(Request $request, Exhibition $exhibition)
    {
        $this->permit('spaces.manage');
        $space = $exhibition->spaces()->create($this->priced($request->validate($this->rules($exhibition))));
        Audit::log('space.created', "Space {$space->number} created in {$exhibition->code}", $space, reference: $space->number);

        return response()->json($space->refresh()->load('hall', 'category'), 201);
    }

    /** Lays out a block of identical spaces on the hall grid in one go. */
    public function generate(Request $request, Exhibition $exhibition)
    {
        $this->permit('spaces.manage');
        $data = $request->validate([
            'hall_id' => ['required', Rule::exists('halls', 'id')->where('exhibition_id', $exhibition->id)],
            'space_category_id' => ['required', Rule::exists('space_categories', 'id')->where('exhibition_id', $exhibition->id)],
            'prefix' => 'required|string|max:8',
            'count' => 'required|integer|min:1|max:200',
            'start_at' => 'nullable|integer|min:1',
            'width_m' => 'required|numeric|min:0.5|max:500',
            'length_m' => 'required|numeric|min:0.5|max:500',
            'facilities' => 'nullable|array',
        ]);
        $hall = Hall::find($data['hall_id']);
        $start = $data['start_at'] ?? 1;
        $numbers = collect(range($start, $start + $data['count'] - 1))->map(fn ($n) => strtoupper($data['prefix']).str_pad((string) $n, 2, '0', STR_PAD_LEFT));
        $clash = $exhibition->spaces()->whereIn('number', $numbers)->pluck('number');
        abort_if($clash->isNotEmpty(), 422, 'These space numbers already exist: '.$clash->take(5)->implode(', '));

        // Continue below whatever is already drawn in the hall, leaving an aisle every second row.
        $row = (int) $hall->spaces()->max('map_row') + ($hall->spaces()->exists() ? 2 : 1);
        $col = 1;
        $created = 0;
        foreach ($numbers as $number) {
            if ($col > $hall->grid_cols) {
                $col = 1;
                $row += ($created / $hall->grid_cols) % 2 === 1 ? 2 : 1;
            }
            $exhibition->spaces()->create($this->priced([
                'hall_id' => $hall->id, 'space_category_id' => $data['space_category_id'], 'number' => $number,
                'width_m' => $data['width_m'], 'length_m' => $data['length_m'], 'facilities' => $data['facilities'] ?? [],
                'map_col' => $col++, 'map_row' => $row,
            ]));
            $created++;
        }
        Audit::log('space.generated', "$created spaces generated in {$hall->name} ({$numbers->first()}–{$numbers->last()})", $hall);

        return response()->json(['created' => $created], 201);
    }

    public function update(Request $request, Space $space)
    {
        $this->permit('spaces.manage');
        $data = $this->priced($request->validate($this->rules($space->exhibition, $space)));
        // The price an exhibitor was billed must not move under them.
        if ($space->activeAllocation()->exists()) {
            unset($data['price'], $data['width_m'], $data['length_m'], $data['area_sqm'], $data['space_category_id']);
        }
        $space->update($data);
        [$old, $new] = Audit::diff($space);
        if ($new) {
            Audit::log('space.updated', "Space {$space->number} updated", $space, $old, $new, $space->number);
        }

        return $space->load('hall', 'category');
    }

    /** FR-009: manual statuses only; allocation statuses follow from allocations. */
    public function status(Request $request, Space $space)
    {
        $this->permit('spaces.manage');
        $data = $request->validate([
            'status' => ['required', Rule::in(config('gememis.manual_space_statuses'))],
            'reason' => 'nullable|string|max:255',
        ]);
        abort_if($space->activeAllocation()->exists(), 422, "Space {$space->number} is held by an exhibitor. Cancel or reallocate the allocation first.");
        $from = $space->status;
        $space->update(['status' => $data['status']]);
        Audit::log('space.status', "Space {$space->number}: $from → {$data['status']}".(! empty($data['reason']) ? " ({$data['reason']})" : ''),
            $space, ['status' => $from], ['status' => $data['status']], $space->number);

        return $space;
    }

    public function destroy(Space $space)
    {
        $this->permit('spaces.manage');
        abort_if(Allocation::where('space_id', $space->id)->exists(), 422, 'This space has allocation history and cannot be deleted. Mark it cancelled instead.');
        Audit::log('space.deleted', "Space {$space->number} deleted", $space, reference: $space->number);
        $space->delete();

        return response()->noContent();
    }
}
