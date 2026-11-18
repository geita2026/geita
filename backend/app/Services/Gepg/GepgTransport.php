<?php

namespace App\Services\Gepg;

/** How GeMEMIS reaches GePG: over HTTPS (live) or in-process (simulator). */
interface GepgTransport
{
    /** @return array{status: int, body: string} */
    public function post(string $path, string $xml): array;

    /** Deliver anything GePG would send back later (the simulator's callbacks). */
    public function flush(): void;
}
