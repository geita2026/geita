<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PasswordResetChallengeService
{
    public const CACHE_PREFIX = 'password_reset_challenge:';

    public const TTL_SECONDS = 600;

    public const MAX_ATTEMPTS = 3;

    public const BLOCK_SECONDS = 900;

    public const IDENTIFIER_EMAIL = 'email';

    public const IDENTIFIER_PHONE = 'phone';

    public const CHALLENGE_EMAIL = 'email';

    public const CHALLENGE_PHONE = 'phone';

    public const CHALLENGE_FIRST_NAME = 'first_name';

    public const CHALLENGE_MIDDLE_NAME = 'middle_name';

    public const CHALLENGE_LAST_NAME = 'last_name';

    public const CHALLENGE_USERNAME = 'username';

    public const CHALLENGE_MATH = 'math';

    public const MAX_REGENERATIONS = 5;

    /**
     * @return array{found: bool, message?: string, token?: string, challenges?: array<int, array{key: string, question: string, hint: ?string}>}
     */
    public function initiate(string $identifierType, string $identifierValue): array
    {
        $user = $this->findUserByIdentifier($identifierType, $identifierValue);

        if (! $user) {
            return [
                'found' => false,
                'message' => 'No account was found with the email or phone number you entered. Please check your details and try again.',
            ];
        }

        $token = Str::random(64);
        $challenges = $this->buildThreeRandomChallenges($user, $identifierType);

        $payload = [
            'user_id' => $user->id,
            'identifier_type' => $identifierType,
            'challenges' => $challenges,
            'used_challenge_types' => $this->baseTypesFromChallenges($challenges),
            'regenerate_count' => 0,
            'attempts' => 0,
            'blocked_until' => null,
            'verified' => false,
        ];

        Cache::put(self::CACHE_PREFIX.$token, $payload, self::TTL_SECONDS);

        return [
            'found' => true,
            'token' => $token,
            'challenges' => $this->publicChallenges($challenges),
        ];
    }

    /**
     * @param  array<string, string>  $answers
     * @return array{success: bool, message: string, verified?: bool}
     */
    public function verify(string $token, array $answers): array
    {
        $payload = $this->getPayload($token);

        if (! $payload) {
            return [
                'success' => false,
                'message' => 'This password reset request has expired. Please start again.',
            ];
        }

        if ($this->isBlocked($payload)) {
            return [
                'success' => false,
                'message' => 'Too many failed attempts. Please wait before trying again or start a new request.',
            ];
        }

        $allValid = ! empty($payload['user_id']) && $this->answersMatchChallenges(
            $payload['challenges'],
            $answers
        );

        if ($allValid) {
            $payload['verified'] = true;
            $payload['attempts'] = 0;
            Cache::put(self::CACHE_PREFIX.$token, $payload, self::TTL_SECONDS);

            return [
                'success' => true,
                'message' => 'Verification successful. You may now set a new password.',
                'verified' => true,
            ];
        }

        $payload['attempts'] = ((int) ($payload['attempts'] ?? 0)) + 1;

        if ($payload['attempts'] >= self::MAX_ATTEMPTS) {
            $payload['blocked_until'] = now()->addSeconds(self::BLOCK_SECONDS)->timestamp;
            Cache::put(self::CACHE_PREFIX.$token, $payload, self::BLOCK_SECONDS);

            return [
                'success' => false,
                'message' => 'Too many failed attempts. This reset request is temporarily locked. Please try again later.',
            ];
        }

        Cache::put(self::CACHE_PREFIX.$token, $payload, self::TTL_SECONDS);

        return [
            'success' => false,
            'message' => 'One or more answers are incorrect. Please review all three questions and try again.',
        ];
    }

    public function getPayload(string $token): ?array
    {
        return Cache::get(self::CACHE_PREFIX.$token);
    }

    public function isBlocked(array $payload): bool
    {
        if (empty($payload['blocked_until'])) {
            return false;
        }

        return now()->timestamp < (int) $payload['blocked_until'];
    }

    public function canResetPassword(string $token): bool
    {
        $payload = $this->getPayload($token);

        return $payload
            && ! empty($payload['verified'])
            && ! empty($payload['user_id'])
            && ! $this->isBlocked($payload);
    }

    public function userIdForToken(string $token): ?int
    {
        $payload = $this->getPayload($token);

        return $payload['user_id'] ?? null;
    }

    public function forget(string $token): void
    {
        Cache::forget(self::CACHE_PREFIX.$token);
    }

    /**
     * @return array{success: bool, message: string, challenges?: array<int, array{key: string, question: string, hint: ?string}>}
     */
    public function regenerate(string $token): array
    {
        $payload = $this->getPayload($token);

        if (! $payload || empty($payload['user_id'])) {
            return [
                'success' => false,
                'message' => 'This password reset request has expired. Please start again.',
            ];
        }

        if (! empty($payload['verified'])) {
            return [
                'success' => false,
                'message' => 'You have already been verified. Continue to set your new password.',
            ];
        }

        if ($this->isBlocked($payload)) {
            return [
                'success' => false,
                'message' => 'Too many failed attempts. Please wait before trying again.',
            ];
        }

        $regenerateCount = ((int) ($payload['regenerate_count'] ?? 0)) + 1;

        if ($regenerateCount > self::MAX_REGENERATIONS) {
            return [
                'success' => false,
                'message' => 'You have reached the maximum number of new question sets. Please answer the current questions or start over.',
            ];
        }

        $user = User::find($payload['user_id']);

        if (! $user) {
            return [
                'success' => false,
                'message' => 'Unable to load your account. Please start again.',
            ];
        }

        $excludeTypes = $payload['used_challenge_types'] ?? [];
        $challenges = $this->buildThreeRandomChallenges(
            $user,
            (string) $payload['identifier_type'],
            $excludeTypes
        );

        $payload['challenges'] = $challenges;
        $payload['regenerate_count'] = $regenerateCount;
        $payload['used_challenge_types'] = array_values(array_unique(array_merge(
            $excludeTypes,
            $this->baseTypesFromChallenges($challenges)
        )));
        $payload['attempts'] = 0;

        Cache::put(self::CACHE_PREFIX.$token, $payload, self::TTL_SECONDS);

        return [
            'success' => true,
            'message' => 'New verification questions have been generated.',
            'challenges' => $this->publicChallenges($challenges),
        ];
    }

    /**
     * @return array<int, array{key: string, question: string, hint: ?string}>
     */
    public function publicChallengesFromPayload(array $payload): array
    {
        return $this->publicChallenges($payload['challenges'] ?? []);
    }

    public function regenerationsRemaining(array $payload): int
    {
        $used = (int) ($payload['regenerate_count'] ?? 0);

        return max(0, self::MAX_REGENERATIONS - $used);
    }

    /**
     * @return array{found: bool, message?: string, token?: string, challenges?: array<int, array{key: string, question: string}>}
     */
    public function initiateFromIdentifier(string $identifier): array
    {
        $identifier = trim($identifier);

        if ($identifier === '') {
            return [
                'found' => false,
                'message' => 'Please enter your registered email address or phone number.',
            ];
        }

        return $this->initiate($this->detectIdentifierType($identifier), $identifier);
    }

    public function detectIdentifierType(string $value): string
    {
        $value = trim($value);

        if (str_contains($value, '@') || filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return self::IDENTIFIER_EMAIL;
        }

        return self::IDENTIFIER_PHONE;
    }

    public function findUserByIdentifier(string $type, string $value): ?User
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return match ($type) {
            self::IDENTIFIER_EMAIL => User::query()
                ->whereRaw('LOWER(email) = ?', [$this->normalizeEmail($value)])
                ->first(),
            self::IDENTIFIER_PHONE => $this->findUserByPhone($value),
            default => null,
        };
    }

    protected function findUserByPhone(string $value): ?User
    {
        $digits = $this->normalizePhone($value);

        if ($digits === '') {
            return null;
        }

        return User::query()
            ->whereNotNull('phone_number')
            ->get()
            ->first(fn (User $user) => $this->normalizePhone((string) $user->phone_number) === $digits);
    }

    /**
     * Build exactly 3 questions: at least one math, rest random from account data (excluding identifier field).
     *
     * @return array<int, array{key: string, question: string, answer_hash: string}>
     */
    /**
     * @param  array<int, string>  $excludeBaseTypes
     * @return array<int, array{key: string, question: string, hint: ?string, answer_hash: string}>
     */
    protected function buildThreeRandomChallenges(User $user, string $excludedIdentifierType, array $excludeBaseTypes = []): array
    {
        $pool = $this->buildAccountChallengePool($user, $excludedIdentifierType);
        $pool = array_values(array_filter(
            $pool,
            fn (array $entry) => ! in_array($this->baseTypeFromKey($entry['key']), $excludeBaseTypes, true)
        ));

        if (count($pool) < 2) {
            $pool = $this->buildAccountChallengePool($user, $excludedIdentifierType);
        }

        shuffle($pool);

        $selected = [$this->buildMathChallengeEntry()];

        foreach ($pool as $entry) {
            if (count($selected) >= 3) {
                break;
            }
            if ($this->baseTypeFromKey($entry['key']) === self::CHALLENGE_MATH) {
                continue;
            }
            $selected[] = $entry;
        }

        while (count($selected) < 3) {
            $selected[] = $this->buildMathChallengeEntry();
        }

        shuffle($selected);

        return $selected;
    }

    /**
     * @return array<int, array{key: string, question: string, answer_hash: string}>
     */
    protected function buildAccountChallengePool(User $user, string $excludedIdentifierType): array
    {
        $pool = [];

        if ($excludedIdentifierType !== self::IDENTIFIER_EMAIL && filled($user->email)) {
            $email = (string) $user->email;
            $pool[] = $this->challengeEntry(
                self::CHALLENGE_EMAIL,
                'Enter your registered email address.',
                $this->normalizeEmail($email),
                'Hint: '.$this->maskEmail($email)
            );
        }

        if ($excludedIdentifierType !== self::IDENTIFIER_PHONE && filled($user->phone_number)) {
            $phone = (string) $user->phone_number;
            $pool[] = $this->challengeEntry(
                self::CHALLENGE_PHONE,
                'Enter your registered phone number.',
                $this->normalizePhone($phone),
                'Hint: '.$this->maskPhone($phone)
            );
        }

        if (filled($user->first_name)) {
            $firstName = (string) $user->first_name;
            $pool[] = $this->challengeEntry(
                self::CHALLENGE_FIRST_NAME,
                'Enter your first name.',
                $this->normalizeText($firstName),
                'Hint: '.$this->maskName($firstName)
            );
        }

        if (filled($user->middle_name)) {
            $middleName = (string) $user->middle_name;
            $pool[] = $this->challengeEntry(
                self::CHALLENGE_MIDDLE_NAME,
                'Enter your middle name.',
                $this->normalizeText($middleName),
                'Hint: '.$this->maskName($middleName)
            );
        }

        if (filled($user->last_name)) {
            $lastName = (string) $user->last_name;
            $pool[] = $this->challengeEntry(
                self::CHALLENGE_LAST_NAME,
                'Enter your last name.',
                $this->normalizeText($lastName),
                'Hint: '.$this->maskName($lastName)
            );
        }

        if ($this->userHasUsername($user)) {
            $pool[] = $this->challengeEntry(
                self::CHALLENGE_USERNAME,
                'Enter your registered username.',
                $this->normalizeText((string) $user->user_name)
            );
        }

        return $pool;
    }

    protected function userHasUsername(User $user): bool
    {
        if (! Schema::hasColumn($user->getTable(), 'user_name')) {
            return false;
        }

        return filled($user->user_name ?? null);
    }

    /**
     * @return array{key: string, question: string, hint: ?string, answer_hash: string}
     */
    protected function challengeEntry(string $key, string $question, string $normalizedAnswer, ?string $hint = null): array
    {
        return [
            'key' => $key,
            'question' => $question,
            'hint' => $hint,
            'answer_hash' => $this->hashAnswer($normalizedAnswer),
        ];
    }

    /**
     * @return array{key: string, question: string, answer_hash: string}
     */
    protected function buildMathChallengeEntry(): array
    {
        [$question, $answerHash] = $this->buildMathChallenge();

        return [
            'key' => self::CHALLENGE_MATH.'_'.Str::random(6),
            'question' => $question,
            'hint' => null,
            'answer_hash' => $answerHash,
        ];
    }

    /**
     * @param  array<int, array{key: string, question: string, hint?: ?string, answer_hash: string}>  $challenges
     * @return array<int, array{key: string, question: string, hint: ?string}>
     */
    protected function publicChallenges(array $challenges): array
    {
        return array_map(static fn (array $challenge) => [
            'key' => $challenge['key'],
            'question' => $challenge['question'],
            'hint' => $challenge['hint'] ?? null,
        ], $challenges);
    }

    /**
     * @param  array<int, array{key: string}>  $challenges
     * @return array<int, string>
     */
    protected function baseTypesFromChallenges(array $challenges): array
    {
        return array_values(array_unique(array_map(
            fn (array $challenge) => $this->baseTypeFromKey($challenge['key']),
            $challenges
        )));
    }

    protected function baseTypeFromKey(string $key): string
    {
        if (str_starts_with($key, self::CHALLENGE_MATH)) {
            return self::CHALLENGE_MATH;
        }

        return $key;
    }

    public function maskEmail(string $email): string
    {
        $email = strtolower(trim($email));

        if (! str_contains($email, '@')) {
            return '***@***.***';
        }

        [$local, $domain] = explode('@', $email, 2);
        $length = strlen($local);

        if ($length === 0) {
            $maskedLocal = '***';
        } elseif ($length === 1) {
            $maskedLocal = $local.'*';
        } elseif ($length <= 3) {
            $maskedLocal = substr($local, 0, 1).str_repeat('*', $length - 1);
        } else {
            $visible = min(2, $length - 1);
            $maskedLocal = substr($local, 0, $visible).str_repeat('*', max(3, $length - $visible));
        }

        return $maskedLocal.'@'.$domain;
    }

    public function maskPhone(string $phone): string
    {
        $original = trim($phone);
        $digits = $this->normalizePhone($original);

        if (strlen($digits) < 4) {
            return '*** *** ****';
        }

        $lastFour = substr($digits, -4);
        $compact = strtolower(preg_replace('/\s+/', '', $original) ?? '');

        if (str_starts_with($compact, '+255')) {
            $national = strlen($digits) > 3 ? substr($digits, 3) : '';
            $leading = $national !== '' ? $national[0] : '*';

            return "+255 {$leading}** *** {$lastFour}";
        }

        if (str_starts_with($digits, '255')) {
            $national = substr($digits, 3);
            $leading = $national !== '' ? $national[0] : '*';

            return "255 {$leading}** *** {$lastFour}";
        }

        if (str_starts_with($digits, '0')) {
            $second = strlen($digits) > 1 ? $digits[1] : '*';

            return "{$digits[0]}{$second}** *** {$lastFour}";
        }

        $prefix = substr($digits, 0, min(2, strlen($digits) - 4));

        return "{$prefix}** *** {$lastFour}";
    }

    /**
     * Show first and last letter with masked middle (e.g. John → J**n).
     */
    public function maskName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            return '***';
        }

        $length = mb_strlen($name);

        if ($length === 1) {
            return mb_strtoupper(mb_substr($name, 0, 1)).'*';
        }

        if ($length === 2) {
            return mb_strtoupper(mb_substr($name, 0, 1)).'*'.mb_strtoupper(mb_substr($name, 1, 1));
        }

        $first = mb_strtoupper(mb_substr($name, 0, 1));
        $last = mb_strtoupper(mb_substr($name, -1, 1));

        return $first.str_repeat('*', $length - 2).$last;
    }

    /**
     * @param  array<int, array{key: string, answer_hash: string}>  $challenges
     * @param  array<string, string>  $answers
     */
    protected function answersMatchChallenges(array $challenges, array $answers): bool
    {
        foreach ($challenges as $challenge) {
            $key = $challenge['key'];
            $submitted = (string) ($answers[$key] ?? '');
            $normalized = $this->normalizeChallengeAnswer($key, $submitted);
            $expected = (string) $challenge['answer_hash'];

            if (! hash_equals($expected, $this->hashAnswer($normalized))) {
                return false;
            }
        }

        return true;
    }

    protected function normalizeChallengeAnswer(string $key, string $answer): string
    {
        if (str_starts_with($key, self::CHALLENGE_MATH)) {
            return (string) (int) trim($answer);
        }

        return match ($key) {
            self::CHALLENGE_EMAIL => $this->normalizeEmail($answer),
            self::CHALLENGE_PHONE => $this->normalizePhone($answer),
            default => $this->normalizeText($answer),
        };
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function buildMathChallenge(): array
    {
        $a = random_int(2, 20);
        $b = random_int(2, 12);
        $useSubtraction = (bool) random_int(0, 1);

        if ($useSubtraction && $a < $b) {
            [$a, $b] = [$b, $a];
        }

        if ($useSubtraction) {
            $numericAnswer = $a - $b;
            $question = "What is {$a} - {$b}?";
        } else {
            $numericAnswer = $a + $b;
            $question = "What is {$a} + {$b}?";
        }

        return [$question, $this->hashAnswer((string) $numericAnswer)];
    }

    protected function normalizeText(string $value): string
    {
        return strtolower(trim($value));
    }

    protected function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    protected function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? '';
    }

    protected function hashAnswer(string $answer): string
    {
        return hash('sha256', $answer);
    }
}
