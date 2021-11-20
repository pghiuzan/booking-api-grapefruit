<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Services\Hashing\HasherInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;

class ApiKeyAuthorization
{
    private const API_KEY_HEADER = 'Api-Key';

    public function __construct(private HasherInterface $hasher)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader(self::API_KEY_HEADER)) {
            throw new UnauthorizedException('No API key provided', Response::HTTP_UNAUTHORIZED);
        }

        $key = $request->header(self::API_KEY_HEADER);
        $apiKey = ApiKey::where('key', $this->hasher->hash($key))->first();

        if (!$apiKey) {
            throw new UnauthorizedException('Invalid API key provided', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
