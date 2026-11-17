<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'role', 'is_active', 'must_change_password', 'exhibitor_id'];

    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['role_label'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'locked_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function exhibitor()
    {
        return $this->belongsTo(Exhibitor::class);
    }

    public function getRoleLabelAttribute(): string
    {
        // Null-safe: relations often load only id and name.
        return (string) (config('gememis.roles')[$this->role ?? ''] ?? $this->role);
    }

    /** FR-047 / BR-009 — the single place a permission is decided. */
    public function allowed(string $permission): bool
    {
        // Indexed, not dot-path: permission names contain dots themselves.
        return in_array($this->role, config('gememis.permissions')[$permission] ?? [], true);
    }

    public function isStaff(): bool
    {
        return $this->allowed('staff');
    }

    public function isExhibitor(): bool
    {
        return $this->role === 'exhibitor';
    }

    /** @return string[] */
    public function permissions(): array
    {
        return array_keys(array_filter(config('gememis.permissions'), fn ($roles) => in_array($this->role, $roles, true)));
    }
}
