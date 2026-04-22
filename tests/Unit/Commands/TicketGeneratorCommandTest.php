<?php
declare(strict_types=1);

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Ticket generator command tests', function () {
    it('Generates tickets in batches with a default count', function () {
        $this->artisan('tickets:generate')->assertSuccessful();

        expect(Ticket::count())->toBe(5);
    });

    it('Generates tickets in batches with a specific count', function () {
        $this->artisan('tickets:generate --count=20')->assertSuccessful();

        expect(Ticket::count())->toBe(20);
    });
});