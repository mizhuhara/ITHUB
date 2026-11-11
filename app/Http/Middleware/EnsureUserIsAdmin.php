<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $adminRoles = ['super_admin', 'admin'];

        // Institusi (school/university/company) hanya boleh akses modul miliknya sendiri.
        if (in_array($user->role, $adminRoles, true)) {
            return $next($request);
        }

        $manageable = [
            'school' => ['/admin/schools', '/admin/articles', '/admin/events', '/admin/editor-upload', '/admin/banners'],
            'university' => ['/admin/univ', '/admin/articles', '/admin/events', '/admin/editor-upload', '/admin/banners'],
            'company' => ['/admin/company', '/admin/jobs', '/admin/applications', '/admin/articles', '/admin/events', '/admin/editor-upload', '/admin/banners'],
        ];

        $path = '/'.$request->path();
        $prefixes = $manageable[$user->role] ?? [];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak. Halaman ini khusus admin.');
    }
}
