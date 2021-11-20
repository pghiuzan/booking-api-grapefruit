<?php

namespace App\Services\Auth;

use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class IpBasedDatabaseDrivenLoginAttemptLimiter implements LoginAttemptLimiterInterface
{
    private function getIp(Request $request): string
    {
        return $request->ip();
    }

    public function didReachLimit(Request $request): bool
    {
        $lastMinuteLoginAttempts = LoginAttempt::where([
            ['ip', '=', $this->getIp($request)],
            ['created_at', '>=', Carbon::now()->subSeconds($this->getTimeLimit())],
            ['successful', '=', false],
        ])->get();

        if ($lastMinuteLoginAttempts->count() >= $this->getRateLimit()) {
            return true;
        }

        return false;
    }

    public function registerLoginAttempt(Request $request, bool $successful): void
    {
        LoginAttempt::create([
            'ip' => $this->getIp($request),
            'successful' => $successful,
        ]);
    }

    public function getTimeLimit(): int
    {
        return config('auth.loginThrottle.timeLimit');
    }

    public function getRateLimit(): int
    {
        return config('auth.loginThrottle.rateLimit');
    }
}
