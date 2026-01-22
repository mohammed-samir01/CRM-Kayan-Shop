<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array(auth()->user()?->role, ['admin', 'staff'])) {
            abort(403, 'غير مصرح لك بالدخول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
