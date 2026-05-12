<?php

declare(strict_types=1);

use Hookbox\UiBlade\Http\Controllers\ReplayController;
use Hookbox\UiCore\Http\Controllers\InboxController;
use Hookbox\UiCore\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware((array) config('hookbox-ui.middleware', ['web']))
    ->prefix((string) config('hookbox-ui.route_prefix', 'hookbox'))
    ->as('hookbox-ui.')
    ->group(function (): void {
        Route::get('/messages', InboxController::class)->name('messages.index');
        Route::get('/messages/{message}', MessageController::class)->name('messages.show');
        Route::post('/messages/{message}/replay', ReplayController::class)->name('messages.replay');
    });
