<?php

namespace App\Traits;

use App\Models\companies;

trait TsrnSupplier
{

    public function getTsrn()
    {
        $TSRN = 'T-' . substr(date('Y'), -3) . '-' . random_int(100, 999) . '-' . random_int(100, 999);
        $exist = companies::where('registrationID', $TSRN)->first();
        if (!$exist) {
            return $TSRN;
        }
    }
}
