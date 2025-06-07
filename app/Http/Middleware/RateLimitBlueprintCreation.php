<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitBlueprintCreation
{
    public function __construct(
        private readonly RateLimiter $limiter
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'blueprint:' . ($request->user()?->id ?? $request->ip());

        if ($this->limiter->tooManyAttempts($key, 60)) {
            $seconds = $this->limiter->availableIn($key);
            
            return response()->json([
                'message' => 'Too many attempts. Please try again later.',
                'retry_after' => $seconds,
            ], 429, [
                'Retry-After' => $seconds,
                'X-RateLimit-Reset' => now()->addSeconds($seconds)->getTimestamp(),
            ]);
        }

        $this->limiter->hit($key);

        $response = $next($request);

        $response->headers->add([
            'X-RateLimit-Limit' => 60,
            'X-RateLimit-Remaining' => $this->limiter->remaining($key, 60),
        ]);

        return $response;
    }
} 