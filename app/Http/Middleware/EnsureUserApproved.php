<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->status === 'approved') {
            return $next($request);
        }

        abort(403, 'Akun Anda masih menunggu approval staff gereja.');
    }
}
