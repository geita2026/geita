<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Exhibitor;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * FR-035..039 / FR-060. Every notice is stored in-system and then pushed to
 * email (Laravel mailer) and SMS (driver in config/gememis.php; `log` writes
 * the message to the application log until a gateway is contracted).
 */
class Notifier
{
    public static function user(User $user, string $kind, string $title, string $body, ?string $href = null): void
    {
        $channels = ['in_app'];

        try {
            Mail::raw($body."\n\n".($href ? rtrim(config('gememis.frontend_url'), '/').$href : ''), function ($m) use ($user, $title) {
                $m->to($user->email)->subject('[GeMEMIS] '.$title);
            });
            $channels[] = 'email';
        } catch (Throwable $e) {
            Log::warning('Email notification failed', ['user' => $user->id, 'error' => $e->getMessage()]);
        }

        if ($user->phone) {
            static::sms($user->phone, $title.': '.$body);
            $channels[] = 'sms';
        }

        AppNotification::create([
            'user_id' => $user->id, 'kind' => $kind, 'title' => $title, 'body' => $body, 'href' => $href, 'channels' => $channels,
        ]);
    }

    public static function exhibitor(Exhibitor $exhibitor, string $kind, string $title, string $body, ?string $href = null): void
    {
        foreach ($exhibitor->users()->where('is_active', true)->get() as $user) {
            static::user($user, $kind, $title, $body, $href);
        }
    }

    /** @param string[] $roles */
    public static function roles(array $roles, string $kind, string $title, string $body, ?string $href = null): void
    {
        foreach (User::whereIn('role', $roles)->where('is_active', true)->get() as $user) {
            static::user($user, $kind, $title, $body, $href);
        }
    }

    public static function sms(string $phone, string $message): void
    {
        // Driver seam: swap for the contracted SMS gateway's HTTP call.
        Log::channel(config('logging.default'))->info('SMS', [
            'to' => $phone, 'sender' => config('gememis.sms.sender'), 'message' => mb_strimwidth($message, 0, 300, '…'),
        ]);
    }
}
