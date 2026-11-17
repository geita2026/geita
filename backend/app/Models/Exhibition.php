<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['registration_open'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'registration_opens_on' => 'date:Y-m-d',
            'registration_closes_on' => 'date:Y-m-d',
            'payment_deadline' => 'date:Y-m-d',
            'categories' => 'array',
            'required_documents' => 'array',
            'allow_self_selection' => 'boolean',
            'vat_rate' => 'float',
            'published_at' => 'datetime',
        ];
    }

    public function halls()
    {
        return $this->hasMany(Hall::class)->orderBy('sort')->orderBy('code');
    }

    public function spaceCategories()
    {
        return $this->hasMany(SpaceCategory::class)->orderBy('name');
    }

    public function spaces()
    {
        return $this->hasMany(Space::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /** BR-010: open only while published and inside the registration window. */
    public function getRegistrationOpenAttribute(): bool
    {
        $today = now()->startOfDay();

        return $this->status === 'published'
            && $this->registration_opens_on <= $today
            && $this->registration_closes_on >= $today;
    }
}
