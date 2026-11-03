<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStaff
{
    // Cho phép Admin và Biên tập viên
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isStaff()) {
            abort(403, 'Khu vực này chỉ dành cho quản trị viên và biên tập viên.');
        }

        return $next($request);
    }
}
