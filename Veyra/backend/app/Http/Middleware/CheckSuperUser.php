<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSuperUser
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->user_type !== 'SUPER_USER') {
            return response()->json([
                'message' => 'Accès réservé aux super utilisateurs.'
            ], 403);
        }

        return $next($request);
    }
}
