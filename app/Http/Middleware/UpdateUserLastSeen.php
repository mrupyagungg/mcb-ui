<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Update hanya jika terakhir dilihat lebih dari 1 menit yang lalu (menghemat resource database)
            if (!$user->last_seen_at || $user->last_seen_at->lt(now()->subMinute())) {
                $user->update(['last_seen_at' => now()]);
            }
        }

        return $next($request);
    }
}