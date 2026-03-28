<?php

declare(strict_types=1);

use App\Events\BeforeStartApiRouteEvent;
use App\Events\BeforeStartWebRouteEvent;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| All NexoPOS routes run here. Tenancy is initialized by full domain.
| e.g. shop1.shop.localhost:8000 → initializes tenant "shop1"
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    BeforeStartWebRouteEvent::dispatch();
    require base_path('routes/web-base.php');
});

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api')->group(function () {
    BeforeStartApiRouteEvent::dispatch();
    require base_path('routes/api-base.php');
});
