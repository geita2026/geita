<?php

namespace App\Services\Gepg;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Signs what GeMEMIS sends and verifies what GePG sends.
 *
 * The signature is computed over the exact text of the message's content
 * element (e.g. "<gepgBillSubReq>…</gepgBillSubReq>") and carried base64 in
 * <gepgSignature>. Live keys come from config/gepg.php. In simulator mode, with
 * no keys configured, a key pair is generated once for each side under
 * storage/app/private/gepg-simulator, so signing and verification still run
 * for real.
 */
class GepgSigner
{
    private const SIM_DIR = 'gepg-simulator';

    /** Sign as the Service Provider (GeMEMIS). */
    public function sign(string $content): string
    {
        return $this->signWith($content, $this->spPrivateKey());
    }

    /** Verify a message GePG signed. */
    public function verify(string $content, string $signature): bool
    {
        $key = openssl_pkey_get_public($this->gepgPublicKey());
        if (! $key) {
            throw new RuntimeException('The GePG certificate cannot be read.');
        }
        $raw = base64_decode($signature, true);

        return $raw !== false && openssl_verify($content, $raw, $key, $this->algorithm()) === 1;
    }

    /** Used by the simulator to sign as GePG. */
    public function signAsGepg(string $content): string
    {
        return $this->signWith($content, $this->simulatorKey('gepg')['private']);
    }

    /** Public half of GeMEMIS's key — what GePG would verify our messages with. */
    public function spPublicKey(): string
    {
        if ($path = config('gepg.private_key_path')) {
            $details = openssl_pkey_get_details($this->loadPrivate(file_get_contents($path)));

            return $details['key'];
        }

        return $this->simulatorKey('sp')['public'];
    }

    public function usingSimulatorKeys(): bool
    {
        return ! config('gepg.private_key_path') || ! config('gepg.gepg_certificate_path');
    }

    private function signWith(string $content, string $privatePem): string
    {
        if (! openssl_sign($content, $signature, $this->loadPrivate($privatePem), $this->algorithm())) {
            throw new RuntimeException('Signing the GePG message failed: '.openssl_error_string());
        }

        return base64_encode($signature);
    }

    private function loadPrivate(string $pem)
    {
        $key = openssl_pkey_get_private($pem, (string) config('gepg.private_key_passphrase'));
        if (! $key) {
            throw new RuntimeException('The GeMEMIS private key cannot be read (check the path and passphrase).');
        }

        return $key;
    }

    private function spPrivateKey(): string
    {
        if ($path = config('gepg.private_key_path')) {
            return file_get_contents($path) ?: throw new RuntimeException("Cannot read $path.");
        }
        $this->assertSimulator();

        return $this->simulatorKey('sp')['private'];
    }

    private function gepgPublicKey(): string
    {
        if ($path = config('gepg.gepg_certificate_path')) {
            return file_get_contents($path) ?: throw new RuntimeException("Cannot read $path.");
        }
        $this->assertSimulator();

        return $this->simulatorKey('gepg')['public'];
    }

    private function assertSimulator(): void
    {
        if (config('gepg.mode') === 'live') {
            throw new RuntimeException('GePG is in live mode but GEPG_PRIVATE_KEY_PATH / GEPG_CERTIFICATE_PATH are not set.');
        }
    }

    private function algorithm(): int
    {
        return match (strtolower((string) config('gepg.signature_algorithm'))) {
            'sha256' => OPENSSL_ALGO_SHA256,
            default => OPENSSL_ALGO_SHA1,
        };
    }

    /** @return array{private: string, public: string} */
    private function simulatorKey(string $side): array
    {
        $disk = Storage::disk('local');
        $file = self::SIM_DIR."/$side.pem";

        if (! $disk->exists($file)) {
            $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
            openssl_pkey_export($key, $private);
            $disk->put($file, $private);
            $disk->put(self::SIM_DIR."/$side.pub.pem", openssl_pkey_get_details($key)['key']);
        }

        return ['private' => $disk->get($file), 'public' => $disk->get(self::SIM_DIR."/$side.pub.pem")];
    }
}
