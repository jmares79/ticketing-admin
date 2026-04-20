<?php
declare(strict_types=1);

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Returns tickets belonging to a user', function () {
    it('Returns HTTP Not found 404 for a non-existent user', function () {
        $this->getJson('/api/v1/users/100/tickets')->assertNotFound();
    });

    it('Returns tickets for a given user', function () {
        $user = User::factory()->create();
        Ticket::factory()->count(5)->create(['user_id' => $user->id]);

        $this->getJson(route('users.tickets', $user))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'subject',
                        'content',
                        'status',
                        'created_at',
                        'updated_at',
                        'user' => ['id', 'name', 'email'],
                    ],
                ],
                'links',
                'meta',
            ]);
    });

    it('Returns only tickets for a specific user', function () {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Ticket::factory()->count(4)->create(['user_id' => $userA->id]);
        Ticket::factory()->count(3)->create(['user_id' => $userB->id]);

        $response = $this->getJson(route('users.tickets', $userA))
            ->assertOk()
            ->assertJsonCount(4, 'data');

        $userIds = collect($response->json('data'))->pluck('user.id')->unique();

        expect($userIds)->toHaveCount(1)->and($userIds->first())->toBe($userA->id);
    });

    it('Returns all ticket statuses for the user', function () {
        $user = User::factory()->create();
        Ticket::factory()->count(2)->for($user)->create(['status' => TicketStatus::Open, 'user_id' => $user->id]);
        Ticket::factory()->count(3)->closed()->create(['user_id' => $user->id]);

        $this->getJson(route('users.tickets', $user))
            ->assertOk()
            ->assertJsonCount(5, 'data');
    });

    it('Returns result in paginated format', function () {
        $totalTickets = 30;
        $user = User::factory()->create();
        Ticket::factory()->count($totalTickets)->create(['user_id' => $user->id]);

        $response = $this->getJson(route('users.tickets', $user));

        // Default is 25 or according to config
        $response->assertOk()
            ->assertJsonCount(25, 'data')
            ->assertJsonPath('meta.per_page', 25)
            ->assertJsonPath('meta.total', $totalTickets)
            ->assertJsonPath('meta.last_page', 2);
    });

    it('Returns empty data when user has no tickets', function () {
        $user = User::factory()->create();

        $this->getJson(route('users.tickets', $user))
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.total', 0);
    });
});
