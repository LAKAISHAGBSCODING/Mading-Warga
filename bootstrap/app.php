<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin'    => \App\Http\Middleware\IsAdmin::class,
        'approved' => \App\Http\Middleware\EnsureUserIsApproved::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi berakhir, silakan coba lagi.'], 419);
            }

            return redirect()->back()->withInput()->with('error', 'Sesi berakhir, silakan coba lagi.');
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e, Request $request) {
            \Illuminate\Support\Facades\Log::error('Database Query Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if (config('app.debug')) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terjadi gangguan koneksi database. Silakan coba beberapa saat lagi.'], 500);
            }

            return response()->view('errors.500', [], 500);
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($e instanceof \Illuminate\Validation\ValidationException ||
                $e instanceof \Illuminate\Auth\AuthenticationException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                return null;
            }

            \Illuminate\Support\Facades\Log::error('Server Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if (config('app.debug')) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan sistem server.'], 500);
            }

            return response()->view('errors.500', [], 500);
        });
    })->create();
