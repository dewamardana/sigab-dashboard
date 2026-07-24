<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header('X-API-KEY');

        if (! $providedKey || $providedKey !== config('services.sigab_api.key')) {
            return response()->json([
                'message' => 'Unauthorized. API key tidak valid atau tidak disertakan.',
            ], 401);
        }

        return $next($request);
    }
}
