<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;

class StatsService
{
    public function getTicketStats(): array
    {
        $userTopTickets = User::withCount('tickets')
            ->orderByDesc('tickets_count')
            ->first(['id', 'name', 'email']);

        $lastClosed = Ticket::closed()
            ->latest('updated_at')
            ->value('updated_at');

        return [
            'total_tickets'       => Ticket::count(),
            'open_tickets'        => Ticket::open()->count(),
            'top_user'            => $userTopTickets ? [
                'name'  => $userTopTickets->name,
                'email' => $userTopTickets->email,
            ] : null,
            'last_processed_at'   => $lastClosed,
        ];
    }
}