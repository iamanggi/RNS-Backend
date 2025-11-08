<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // Hanya izinkan admin dan owner
        if (!in_array($user->role, ['admin', 'owner'])) {
            return response()->json([
                'success' => false,
                'message' => 'Access restricted to admin or owner only'
            ], 403);
        }

        return $next($request);
    }
}
