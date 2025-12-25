<?php

use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service' => 'group.one Centralized License Service',
        'version' => '1.0.0',
        'status' => 'operational',
        'documentation' => url('/api/documentation'),
    ]);
});

Route::get('/health', [HealthController::class, 'check']);
