<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isLecturer()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Lecturer access required.'], 403);
            }
            return redirect()->route('home')->with('error', 'Unauthorized. Lecturer access required.');
        }

        return $next($request);
    }
}
