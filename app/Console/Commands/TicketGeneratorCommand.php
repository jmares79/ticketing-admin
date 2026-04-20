<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\CreateTicketsAction;
use Illuminate\Console\Command;

/**
 * Generate dummy support tickets, with an optional count of 5 tickets or the passed count if there is one.
 */
class TicketGeneratorCommand extends Command
{
    protected $signature   = 'tickets:generate {--count=5 : Number of tickets to generate}';
    protected $description = 'Generate fake support tickets';

    public function handle(): int
    {
        $count = (int) $this->option('count');

        $this->info("Generating {$count} ticket(s)...");

        collect(range(1, $count))->each(function () {
            $ticket = CreateTicketsAction::run();
            $this->line("Created ticket -- {$ticket->id}: {$ticket->subject}");
        });

        $this->info('Ticket creation finished');

        return self::SUCCESS;
    }
}
