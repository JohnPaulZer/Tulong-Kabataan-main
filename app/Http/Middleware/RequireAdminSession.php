<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('admin_logged_in')) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('administrator/admin/*') || $request->is('administrator/donations/*')) {
            return response()->json([
                'message' => 'Admin authentication is required.',
            ], 401);
        }

        return redirect()->route('admin.login');
    }
}
