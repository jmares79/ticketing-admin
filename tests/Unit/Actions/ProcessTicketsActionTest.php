<?php
declare(strict_types=1);

use App\Actions\ProcessTicketsAction;
use App\Enums\TicketStatus;
use App\Jobs\ProcessTicketJob;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Process tickets action tests', function () {
    beforeEach(function () {
        Queue::fake();
        $this->user = User::factory()->create();
    });

    it('It dispatch a job per open ticket, setting them as in progress', function () {
        $totalTickets = 3;
        Ticket::factory()->count($totalTickets)->create(['user_id' => $this->user->id]);

        $count = ProcessTicketsAction::run(10);

        // No extra ticket created
        $this->assertDatabaseCount('tickets', $totalTickets);
        expect($count)->toBe($totalTickets)
            ->and(Ticket::inProgress()->count())->toBe($totalTickets);

        Queue::assertPushed(ProcessTicketJob::class, $totalTickets);
    });

    it('It does not dispatch a job per ticket not in open status', function () {
        $totalTickets = 3;
        Ticket::factory()->closed()->count($totalTickets)->create(['user_id' => $this->user->id]);

        ProcessTicketsAction::run(10);

        // No extra ticket created
        $this->assertDatabaseCount('tickets', $totalTickets);

        expect(Ticket::inProgress()->count())->toBe(0);
        Queue::assertNotPushed(ProcessTicketJob::class);
    });

    it('It does not process more tickets than the batch limit', function () {
        $batch = 15;
        Ticket::factory()->count(50)->create(['user_id' => $this->user->id]);

        $count = ProcessTicketsAction::run($batch);

        $this->assertDatabaseCount('tickets', 50);
        expect($count)->toBe($batch)
            ->and(Ticket::inProgress()->count())->toBe($batch);

        Queue::assertPushed(ProcessTicketJob::class, $batch);
    });

    it('Process tickets in chronological order', function () {
       $oldTicket = Ticket::factory()->create(['user_id' => $this->user->id, 'subject' => 'Old Ticket', 'created_at' => now()->subDays(2)]);
       $newTicket = Ticket::factory()->create(['user_id' => $this->user->id, 'subject' => 'New Ticket', 'created_at' => now()]);

        $count = ProcessTicketsAction::run(1);

        Queue::assertPushed(ProcessTicketJob::class, 1);
        expect($count)->toBe(1);

        $this->assertDatabaseHas('tickets', ['id' => $oldTicket->id, 'status' => TicketStatus::InProgress->value]);
        $this->assertDatabaseHas('tickets', ['id' => $newTicket->id, 'status' => TicketStatus::Open->value]);
    });
});