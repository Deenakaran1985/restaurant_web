<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            '/menu/*',
            '/menu/recipe',
            '/menu/category',
            '/menu/*/adjust-recipe',
            '/tables/config',
            '/tables/config/*',
            '/tables/*',
            '/delivery/simulate-order',
            '/hotel/room-service/bill',
            '/inventory/waste-and-spillage/log',
            '/accounts/night-audit/execute',
            '/settings',
            '/it-admin/printer-test',
            '/pos/*',
            '/tables/*',
            '/accounts/*',
            '/crm/*',
            '/kds/*',
            '/kds/*/status/*',
            '/menu/qr/*/order',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
