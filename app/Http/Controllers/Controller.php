<?php

namespace App\Http\Controllers;

use App\Support\PermissionNames;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function permissionDeniedMessage(string $ability): string
    {
        $permission = PermissionNames::canonical($ability);

        return "You don't have permission to: {$permission}";
    }
}
