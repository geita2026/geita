<?php

namespace App\Services\Gepg;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class LiveTransport implements GepgTransport
{
    public function post(string $path, string $xml): array
    {
        $base = rtrim((string) config('gepg.base_url'), '/');
        if (! $base) {
            throw new RuntimeException('GEPG_BASE_URL is not set.');
        }

        // TLS verification stays on: a bill or payment must never cross a connection that could be intercepted.
        $response = Http::withHeaders(config('gepg.headers') + ['Accept' => 'application/xml'])
            ->timeout(config('gepg.timeout'))
            ->withBody($xml, 'application/xml')
            ->post($base.$path);

        return ['status' => $response->status(), 'body' => (string) $response->body()];
    }

    public function flush(): void {}
}
