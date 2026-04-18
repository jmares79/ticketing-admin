<?php

use App\Http\Controllers\StatsController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserTicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('tickets/open', [TicketController::class, 'openTickets'])->name('tickets.open');
    Route::get('tickets/closed', [TicketController::class, 'closedTickets'])->name('tickets.closed');
    Route::get('users/{user}/tickets', [UserTicketController::class, 'index'])->name('users.tickets');
    Route::get('stats', StatsController::class)->name('stats');
});
