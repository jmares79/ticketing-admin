<?php
declare(strict_types=1);

use App\Jobs\ProcessTicketJob;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Ticket processor command tests', function () {
    it('Process open tickets in batches', function () {
        Queue::fake();

        $user = User::factory()->create();
        Ticket::factory()->count(10)->create(['user_id' => $user->id]);

        $this->artisan('tickets:process')->assertSuccessful();

        Queue::assertPushed(ProcessTicketJob::class, 5);
    });

    it('Does not Process jobs when no open tickets exist', function () {
        Queue::fake();

        $user = User::factory()->create();
        Ticket::factory()->count(10)->closed()->create(['user_id' => $user->id]);

        $this->artisan('tickets:process')->assertSuccessful();

        Queue::assertNotPushed(ProcessTicketJob::class);
    });
});

