<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\ProcessTicketsAction;
use Illuminate\Console\Command;

class TicketProcessorCommand extends Command
{
    protected $signature   = 'tickets:process {--batch=5 : Number of tickets to close per run}';
    protected $description = 'Process open tickets and mark them as closed';

    public function handle(): int
    {
        $batch = (int) $this->option('batch');

        $this->info("Ticket processing command started. Processing up to {$batch} open ticket(s)...");

        $processed = ProcessTicketsAction::run($batch);

        $this->info("Ticket processing command finished. {$processed} ticket(s) closed.");

        return self::SUCCESS;
    }
}
