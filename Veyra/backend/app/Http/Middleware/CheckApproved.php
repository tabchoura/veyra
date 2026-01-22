<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // نفترض عندك colonne approved = 1
        if ($user->approved !== 1) {
            return response()->json([
                'message' => 'User not approved'
            ], 403);
        }

        return $next($request);
    }
}
