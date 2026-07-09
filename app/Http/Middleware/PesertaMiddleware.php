<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PesertaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('peserta_npk')) {
            return redirect()->route('peserta.login');
        }
        return $next($request);
    }
}
