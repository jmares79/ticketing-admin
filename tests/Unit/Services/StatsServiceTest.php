<?php
declare(strict_types=1);

use App\Models\Ticket;
use App\Models\User;
use App\Services\StatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Statistics service tests', function () {
    beforeEach(function () {
        $this->service = new StatsService();
    });

    it('Returns empty data when no tickets exist', function () {
        $stats = $this->service->getTicketStats();

        expect($stats['total_tickets'])->toBe(0)
            ->and($stats['open_tickets'])->toBe(0)
            ->and($stats['top_user'])->toBeNull()
            ->and($stats['last_processed_at'])->toBeNull();
    });

    it('Returns stats data', function () {
        $openTickets = 10;
        $closedTickets = 5;
        $user = User::factory()->create();
        Ticket::factory()->count($openTickets)->create(['user_id' => $user->id]);
        Ticket::factory()->count($closedTickets)->closed()->create(['user_id' => $user->id]);

        $stats = $this->service->getTicketStats();

        expect($stats['total_tickets'])->toBe($openTickets + $closedTickets)
            ->and($stats['open_tickets'])->toBe($openTickets)
            ->and($stats['top_user']['name'])->toBe($user->name)
            ->and($stats['top_user']['email'])->toBe($user->email);
    });

    it('Returns the most recently processed ticket', function () {
        $user = User::factory()->create();

        Ticket::factory()->for($user)->closed()->create([
            'updated_at' => now()->subDays(2),
        ]);

        $latest = Ticket::factory()->closed()->for($user)->create([
            'updated_at' => now(),
        ]);

        $stats = $this->service->getTicketStats();

        expect($stats['last_processed_at']->toDateTimeString())
            ->toBe($latest->updated_at->toDateTimeString());
    });
});