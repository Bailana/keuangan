<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $userRole = auth()->user()->role;

        // Superadmin memiliki akses ke semua role
        if ($userRole === 'superadmin') {
            return $next($request);
        }

        // Role hierarchy: superadmin > admin > administrasi > viewer
        $roleHierarchy = [
            'superadmin' => 4,
            'admin' => 3,
            'administrasi' => 2,
            'viewer' => 1,
        ];

        $requiredLevel = $roleHierarchy[$role] ?? 0;
        $userLevel = $roleHierarchy[$userRole] ?? 0;

        if ($userLevel < $requiredLevel) {
            abort(403, 'Anda tidak memiliki akses untuk fitur ini.');
        }

        return $next($request);
    }
}
