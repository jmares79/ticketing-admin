<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function openTickets()
    {
        $tickets = Ticket::with('user')
            ->open()
            ->oldest()
            ->paginate(config('pagination.per_page'));

        return TicketResource::collection($tickets);
    }

    public function closedTickets()
    {
        $tickets = Ticket::with('user')
            ->closed()
            ->latest()
            ->paginate(config('pagination.per_page'));

        return TicketResource::collection($tickets);
    }
}
