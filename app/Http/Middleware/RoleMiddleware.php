<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * How it works:
     * 1. The route is protected with middleware like: middleware('role:student')
     * 2. This middleware reads the required role from the $role parameter.
     * 3. It checks if the logged-in user's role matches.
     * 4. If yes → let the request through to the controller.
     * 5. If no  → return HTTP 403 Forbidden immediately.
     *
     * The $role parameter comes from how you apply it in routes/web.php:
     *   ->middleware('role:student')   → $role = 'student'
     *   ->middleware('role:registrar') → $role = 'registrar'
     *   ->middleware('role:cashier')   → $role = 'cashier'
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // auth()->user() returns the currently logged-in user.
        // We check their role column against the required role for this route.
        if (auth()->user()->role !== $role) {
            // Instead of a hard 403, redirect the user to their own dashboard
            // with a friendly message so they understand what happened.
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. You do not have permission to view that page.');
        }

        // Role matches — allow the request to continue to the controller.
        return $next($request);
    }
}
