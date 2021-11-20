<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

/**
 * Interface LoginAttemptLimiterInterface
 */
interface LoginAttemptLimiterInterface
{
    public function didReachLimit(Request $request): bool;
    public function registerLoginAttempt(Request $request, bool $successful): void;
    public function getTimeLimit(): int;
    public function getRateLimit(): int;
}
