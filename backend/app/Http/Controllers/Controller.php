<?php

namespace App\Http\Controllers;

use App\Models\User;

abstract class Controller
{
    protected function actor(): User
    {
        return auth('sanctum')->user() ?? auth()->user();
    }

    /** FR-047: refuse unless the signed-in role holds one of the permissions. */
    protected function permit(string ...$permissions): void
    {
        foreach ($permissions as $permission) {
            if ($this->actor()->allowed($permission)) {
                return;
            }
        }
        abort(403, 'Your role does not allow this action.');
    }

    /** Exhibitor accounts only ever see their own organisation's records. */
    protected function ownOrStaff(?int $exhibitorId): void
    {
        $user = $this->actor();
        abort_unless($user->isStaff() || ($exhibitorId && $user->exhibitor_id === $exhibitorId), 403, 'This record belongs to another exhibitor.');
    }

    protected function perPage(): int
    {
        return min(100, max(5, (int) request('per_page', 20)));
    }
}
