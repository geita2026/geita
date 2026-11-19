<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'reviewed_at' => 'datetime', 'confirmed_at' => 'datetime'];
    }

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function exhibitor()
    {
        return $this->belongsTo(Exhibitor::class);
    }

    public function category()
    {
        return $this->belongsTo(SpaceCategory::class, 'space_category_id');
    }

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function allocations()
    {
        return $this->hasMany(Allocation::class);
    }

    public function activeAllocations()
    {
        return $this->hasMany(Allocation::class)->whereIn('status', Allocation::LIVE);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /** Required document types (BR-003) that have not been uploaded yet. */
    public function missingDocuments(): array
    {
        $have = $this->documents()->pluck('type')->all();

        // Individuals and businesses have their own lists (an individual has no business licence).
        $required = $this->exhibitor?->entity_type === 'individual'
            ? ($this->exhibition->required_documents_individual ?? [])
            : ($this->exhibition->required_documents ?? []);

        return array_values(array_diff($required, $have));
    }
}
