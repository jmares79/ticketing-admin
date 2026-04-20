<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Job to process a ticket. It simulates any processing task that might be done to a ticket
 */
class ProcessTicketJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(protected readonly Ticket $ticket) {}

    public function handle(): void
    {
        $this->ticket->update(['status' => TicketStatus::Closed, 'content' => 'Ticket processed and closed']);
    }

    public function failed(\Throwable $e): void
    {
        $this->ticket->update(['status' => TicketStatus::Open, 'content' => 'Ticket processing failed']);
    }
}
