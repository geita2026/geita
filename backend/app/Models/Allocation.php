<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    /** Statuses in which the allocation still holds its space. */
    public const LIVE = ['pending', 'allocated'];

    protected $guarded = ['id'];

    protected $hidden = ['active_space_id'];

    protected function casts(): array
    {
        return ['allocated_at' => 'datetime', 'ended_at' => 'datetime', 'checked_in_at' => 'datetime'];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function exhibitor()
    {
        return $this->belongsTo(Exhibitor::class);
    }

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function allocator()
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    public function isLive(): bool
    {
        return in_array($this->status, self::LIVE, true);
    }
}
