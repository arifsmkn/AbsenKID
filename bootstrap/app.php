<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'    => \App\Http\Middleware\CheckRole::class,
            'admin'   => \App\Http\Middleware\AdminMiddleware::class,
            'peserta' => \App\Http\Middleware\PesertaMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Sesi/token kadaluarsa (419) — kembalikan ke halaman sebelumnya dengan
        // pesan ramah, bukan halaman "Page Expired" kosong.
        // Catatan: Laravel mengonversi TokenMismatchException jadi HttpException(419)
        // generik sebelum callback render custom dicek, jadi type-hint harus ke
        // HttpException lalu cek status code-nya, bukan ke TokenMismatchException langsung.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            return redirect()->back()
                ->withInput($request->except('password'))
                ->withErrors(['session' => 'Sesi sudah kadaluarsa (halaman terbuka terlalu lama). Silakan coba lagi.']);
        });
    })->create();
