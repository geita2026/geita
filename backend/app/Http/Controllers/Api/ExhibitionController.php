<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use App\Models\Hall;
use App\Models\SpaceCategory;
use App\Services\Audit;
use App\Services\Notifier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExhibitionController extends Controller
{
    private function rules(?Exhibition $exhibition = null): array
    {
        return [
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9-]+$/', Rule::unique('exhibitions', 'code')->ignore($exhibition?->id)],
            'name' => 'required|string|max:255',
            'theme' => 'nullable|string|max:255',
            'objectives' => 'nullable|string|max:5000',
            'venue' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'registration_opens_on' => 'required|date',
            'registration_closes_on' => 'required|date|after_or_equal:registration_opens_on|before_or_equal:start_date',
            'payment_deadline' => 'required|date|before_or_equal:start_date',
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:100',
            'required_documents' => 'nullable|array',
            'required_documents.*' => 'string|max:100',
            'terms' => 'nullable|string|max:20000',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:30',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'allow_self_selection' => 'nullable|boolean',
        ];
    }

    public function index(Request $request)
    {
        $query = Exhibition::query()->withCount(['spaces', 'applications',
            'spaces as available_spaces_count' => fn ($q) => $q->where('status', 'available')]);

        // Exhibitors only see what has been published to them (FR-003).
        if (! $this->actor()->isStaff()) {
            $query->whereNotIn('status', ['draft', 'cancelled']);
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($q = $request->query('q')) {
            $query->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('code', 'like', "%$q%")->orWhere('venue', 'like', "%$q%"));
        }

        return $query->orderByDesc('start_date')->get();
    }

    public function show(Request $request, Exhibition $exhibition)
    {
        abort_if(! $this->actor()->isStaff() && in_array($exhibition->status, ['draft', 'cancelled'], true), 404);

        return $exhibition->load(['halls' => fn ($q) => $q->withCount('spaces'), 'spaceCategories' => fn ($q) => $q->withCount('spaces')])
            ->loadCount(['spaces', 'applications']);
    }

    /** FR-001 */
    public function store(Request $request)
    {
        $this->permit('exhibitions.manage');
        $data = $request->validate($this->rules());
        $data['code'] = strtoupper($data['code']);
        $exhibition = Exhibition::create($data + ['created_by' => $this->actor()->id]);
        Audit::log('exhibition.created', "Exhibition {$exhibition->name} created", $exhibition, null, $data);

        return response()->json($exhibition->refresh(), 201);
    }

    /** FR-002: editable until the exhibition is finalised. */
    public function update(Request $request, Exhibition $exhibition)
    {
        $this->permit('exhibitions.manage');
        abort_if(in_array($exhibition->status, ['completed', 'cancelled'], true), 422, 'A finalised exhibition can no longer be edited.');
        $data = $request->validate($this->rules($exhibition));
        $data['code'] = strtoupper($data['code']);
        $exhibition->update($data);
        [$old, $new] = Audit::diff($exhibition);
        if ($new) {
            Audit::log('exhibition.updated', "Exhibition {$exhibition->name} updated", $exhibition, $old, $new);
        }

        return $exhibition;
    }

    /** FR-003, FR-004 and the rest of the life cycle. */
    public function transition(Request $request, Exhibition $exhibition)
    {
        $this->permit('exhibitions.manage');
        $to = $request->validate(['status' => ['required', Rule::in(config('gememis.exhibition_statuses'))]])['status'];
        $allowed = [
            'draft' => ['published', 'cancelled'],
            'published' => ['registration_closed', 'ongoing', 'cancelled'],
            'registration_closed' => ['published', 'ongoing', 'cancelled'],
            'ongoing' => ['completed'],
        ][$exhibition->status] ?? [];
        abort_unless(in_array($to, $allowed, true), 422, "An exhibition that is {$exhibition->status} cannot become $to.");

        if ($to === 'published') {
            abort_if($exhibition->spaces()->count() === 0, 422, 'Configure at least one space before publishing.');
            // Reopening after the deadline needs a new closing date first (BR-010).
            abort_if($exhibition->registration_closes_on->isPast() && ! $exhibition->registration_closes_on->isToday(), 422,
                'The registration closing date has passed. Extend it before reopening registration.');
        }

        $from = $exhibition->status;
        $exhibition->update(['status' => $to, 'published_at' => $to === 'published' ? ($exhibition->published_at ?? now()) : $exhibition->published_at]);
        Audit::log("exhibition.$to", "Exhibition {$exhibition->name}: $from → $to", $exhibition, ['status' => $from], ['status' => $to]);

        if ($to === 'published' && $from === 'draft') {
            Notifier::roles(['exhibitor'], 'exhibition', 'New exhibition open for registration',
                "{$exhibition->name} at {$exhibition->venue} is open for applications until {$exhibition->registration_closes_on->format('d M Y')}.",
                "/exhibitions/{$exhibition->id}");
        }

        return $exhibition;
    }

    /* ----------------------------------------------------------- halls (FR-005) */

    public function storeHall(Request $request, Exhibition $exhibition)
    {
        $this->permit('spaces.manage');
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('halls')->where('exhibition_id', $exhibition->id)],
            'name' => 'required|string|max:255',
            'kind' => 'required|in:indoor,outdoor,pavilion',
            'description' => 'nullable|string|max:255',
            'grid_cols' => 'nullable|integer|min:2|max:40',
        ]);
        $hall = $exhibition->halls()->create($data);
        Audit::log('hall.created', "Hall {$hall->name} added to {$exhibition->code}", $hall);

        return response()->json($hall->refresh(), 201);
    }

    public function updateHall(Request $request, Hall $hall)
    {
        $this->permit('spaces.manage');
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('halls')->where('exhibition_id', $hall->exhibition_id)->ignore($hall->id)],
            'name' => 'required|string|max:255',
            'kind' => 'required|in:indoor,outdoor,pavilion',
            'description' => 'nullable|string|max:255',
            'grid_cols' => 'nullable|integer|min:2|max:40',
        ]);
        $hall->update($data);
        Audit::log('hall.updated', "Hall {$hall->name} updated", $hall);

        return $hall;
    }

    public function destroyHall(Hall $hall)
    {
        $this->permit('spaces.manage');
        abort_if($hall->spaces()->exists(), 422, 'Remove or move the spaces in this hall first.');
        Audit::log('hall.deleted', "Hall {$hall->name} deleted", $hall);
        $hall->delete();

        return response()->noContent();
    }

    /* ---------------------------------------- space categories (FR-006, FR-008) */

    private function categoryRules(int $exhibitionId, ?int $ignore = null): array
    {
        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('space_categories')->where('exhibition_id', $exhibitionId)->ignore($ignore)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'price_per_sqm' => 'required|numeric|min:0',
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'gfs_code' => ['nullable', 'regex:/^[0-9]{4,20}$/'], // GePG revenue code (FR-059)
        ];
    }

    public function storeCategory(Request $request, Exhibition $exhibition)
    {
        $this->permit('spaces.manage');
        $category = $exhibition->spaceCategories()->create($request->validate($this->categoryRules($exhibition->id)));
        Audit::log('category.created', "Space category {$category->name} added to {$exhibition->code}", $category);

        return response()->json($category->refresh(), 201);
    }

    /** FR-008: a new price re-prices this category's spaces that nobody holds yet. */
    public function updateCategory(Request $request, SpaceCategory $category)
    {
        $this->permit('spaces.manage');
        $old = $category->price_per_sqm;
        $category->update($request->validate($this->categoryRules($category->exhibition_id, $category->id)));

        if ($request->boolean('reprice') && $old != $category->price_per_sqm) {
            $category->spaces()->whereIn('status', ['available', 'blocked', 'maintenance', 'reserved'])
                ->each(fn ($s) => $s->update(['price' => round($s->area_sqm * $category->price_per_sqm, 2)]));
        }
        Audit::log('category.updated', "Space category {$category->name} updated", $category, ['price_per_sqm' => $old], ['price_per_sqm' => $category->price_per_sqm]);

        return $category;
    }

    public function destroyCategory(SpaceCategory $category)
    {
        $this->permit('spaces.manage');
        abort_if($category->spaces()->exists(), 422, 'Spaces still use this category.');
        Audit::log('category.deleted', "Space category {$category->name} deleted", $category);
        $category->delete();

        return response()->noContent();
    }
}
