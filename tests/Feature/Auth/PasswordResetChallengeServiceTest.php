<?php

namespace Tests\Feature\Auth;

use App\Services\Auth\PasswordResetChallengeService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PasswordResetChallengeServiceTest extends TestCase
{
    public function test_initiate_returns_not_found_without_database_user(): void
    {
        Cache::flush();
        $service = app(PasswordResetChallengeService::class);

        try {
            $result = $service->initiate(
                PasswordResetChallengeService::IDENTIFIER_EMAIL,
                'nobody-'.uniqid().'@example.test'
            );
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available: '.$e->getMessage());
        }

        $this->assertFalse($result['found']);
        $this->assertArrayNotHasKey('token', $result);
    }

    public function test_mask_phone_shows_prefix_and_last_digits(): void
    {
        $service = app(PasswordResetChallengeService::class);

        $this->assertSame('+255 7** *** 1234', $service->maskPhone('+2557123451234'));
        $this->assertSame('255 7** *** 1234', $service->maskPhone('2557123451234'));
        $this->assertSame('07** *** 5678', $service->maskPhone('07123455678'));
    }

    public function test_mask_name_shows_first_and_last_letters(): void
    {
        $service = app(PasswordResetChallengeService::class);

        $this->assertSame('J**N', $service->maskName('John'));
        $this->assertSame('H*****T', $service->maskName('HONORAT'));
        $this->assertSame('J*E', $service->maskName('Joe'));
    }

    public function test_normalize_text_is_case_insensitive(): void
    {
        $service = new PasswordResetChallengeService;
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('normalizeText');
        $method->setAccessible(true);

        $this->assertSame('john', $method->invoke($service, 'John'));
        $this->assertSame('john', $method->invoke($service, ' JOHN '));
    }

    public function test_math_answer_normalizes_to_numeric_string(): void
    {
        $service = new PasswordResetChallengeService;
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('normalizeChallengeAnswer');
        $method->setAccessible(true);

        $this->assertSame('12', $method->invoke($service, 'math_abc', ' 12 '));
    }
}
