<?php
declare(strict_types=1);

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Retrieve tickets', function() {
    it('Returns all open/unprocessed tickets', function () {
        //Tickets must belong to a user
        $user = User::factory()->create();
        Ticket::factory()->create(['user_id' => $user->id]);

        $this->getJson(route('tickets.open'))
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

    it('Does not return closed or non open/unprocessed tickets', function () {
        $user = User::factory()->create();

        Ticket::factory()->count(3)->create(['user_id' => $user->id]);
        Ticket::factory()->count(2)->closed()->create(['user_id' => $user->id]);

        $this->getJson(route('tickets.open'))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    });

    it('Returns oldest tickets first', function () {
        $user = User::factory()->create();

        $oldest = Ticket::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2),
        ]);

        $newest = Ticket::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        $response = $this->getJson(route('tickets.open'))->assertOk();

        $ids = collect($response->json('data'))->pluck('id');

        expect($ids->first())->toBe($newest->id)->and($ids->last())->toBe($oldest->id);
    });

    // Default is 25 or according to config
    it('Returns tickets paginated', function () {
        $total = 30;
        $user = User::factory()->create();
        Ticket::factory()->count($total)->create(['user_id' => $user->id]);

        $this->getJson(route('tickets.open'))
            ->assertOk()
            ->assertJsonCount(25, 'data')
            ->assertJsonPath('meta.per_page', 25)
            ->assertJsonPath('meta.total', $total)
            ->assertJsonPath('meta.last_page', 2);
    });

    it('Returns empty when no open tickets', function () {
        $user = User::factory()->create();
        Ticket::factory()->count(3)->closed()->create(['user_id' => $user->id]);

        $this->getJson(route('tickets.open'))
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.total', 0);
    });
});

describe('Retrieve open tickets', function() {

    it('Returns all closed tickets', function () {
        $user = User::factory()->create();
        Ticket::factory()->closed()->create(['user_id' => $user->id]);

        $this->getJson(route('tickets.closed'))
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

    it('Does not return non closed tickets', function () {
        $closedTickets = 5;
        $user = User::factory()->create();

        Ticket::factory()->count($closedTickets)->closed()->create(['user_id' => $user->id]);
        Ticket::factory()->count(3)->create(['user_id' => $user->id]);

        $this->getJson(route('tickets.closed'))
            ->assertOk()
            ->assertJsonCount($closedTickets, 'data');
    });

    it('Returns tickets paginated', function () {
        $total = 30;
        $user = User::factory()->create();
        Ticket::factory()->count($total)->closed()->create(['user_id' => $user->id]);

        $this->getJson(route('tickets.closed'))
            ->assertOk()
            ->assertJsonCount(25, 'data')
            ->assertJsonPath('meta.per_page', 25)
            ->assertJsonPath('meta.total', $total)
            ->assertJsonPath('meta.last_page', 2);
    });

    it('Returns empty when no closed tickets', function () {
        $user = User::factory()->create();
        Ticket::factory()->count(30)->create(['user_id' => $user->id]);

        $this->getJson(route('tickets.closed'))
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.total', 0);
    });
});
