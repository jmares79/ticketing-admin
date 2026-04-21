<?php
declare(strict_types=1);

use App\Actions\CreateTicketsAction;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Create tickets action tests', function () {
    it('Creates tickets for a user', function () {
        $response = CreateTicketsAction::run();

        expect($response)->toBeInstanceOf(Ticket::class)
            ->and($response->status)->toBe(TicketStatus::Open)
            ->and($response->user_id)->not->toBeNull()
            ->and($response->subject)->not->toBeEmpty()
            ->and($response->content)->not->toBeEmpty()
            ->and($response->user_id)->not->toBeNull();

        $this->assertDatabaseCount('tickets', 1);
    });

    it('Creates up to max users', function () {
        User::factory()->count(10)->create();

        $userCountBefore = User::count();

        CreateTicketsAction::run();

        expect(User::count())->toBe($userCountBefore);
    });
});

