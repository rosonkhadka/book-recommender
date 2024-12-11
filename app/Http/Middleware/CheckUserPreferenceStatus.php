<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPreferenceStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user->books->isNotEmpty() || !$user->categories->isNotEmpty()) {
            return response()->json(['error' => 'User preferences are incomplete.'], 403);
        }

        return $next($request);
    }
}
