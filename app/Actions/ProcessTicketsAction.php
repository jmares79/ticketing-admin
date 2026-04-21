<?php
declare(strict_types=1);

namespace App\Actions;

use App\Enums\TicketStatus;
use App\Jobs\ProcessTicketJob;
use App\Models\Ticket;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Fetch tickets that are open and dispatch a job to process them
 */
class ProcessTicketsAction
{
    use AsAction;

    public function handle(int $batch): int
    {
        $tickets = Ticket::open()
            ->oldest()
            ->limit($batch)
            ->get();

        // Iteration over tickets result collection to send each job individually into the queue
        $tickets->each(function (Ticket $ticket) {
            // In case the processing job does something else than simply update, the progress update should be moved there
            $ticket->update(['status' => TicketStatus::InProgress]);

            // Simulates a real world scenario where each job takes more/less time than other depending on the amount of work
            ProcessTicketJob::dispatch($ticket)->delay(now()->addSeconds(rand(1, 5)));;
        });

        // Return the ticket count fetched, not necessarily all processed
        return $tickets->count();
    }
}
