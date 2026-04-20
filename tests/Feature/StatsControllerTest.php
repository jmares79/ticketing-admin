<?php
declare(strict_types=1);

use App\Services\StatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Service is mocked to only test controller flow in this file
describe('Statistics controller tests', function () {
    it('Returns statistics', function () {
        $mockedStats = [
            'total_tickets'    => 100,
            'open_tickets'     => 65,
            'top_user'         => [
                'name'  => 'John Doe',
                'email' => 'john@example.com',
            ],
            'last_processed_at' => '2026-04-17',
        ];

        $this->mock(StatsService::class)
            ->shouldReceive('getTicketStats')
            ->once()
            ->andReturn($mockedStats);

        $this->getJson(route('stats'))
            ->assertOk()
            ->assertJsonStructure([
                'total_tickets',
                'open_tickets',
                'top_user' => ['name', 'email'],
                'last_processed_at',
            ])->assertJson($mockedStats);
    });

    it('Returns statistics with null values when no data', function () {
        $mockedStats = [
            'total_tickets'    => 0,
            'open_tickets'     => 0,
            'top_user'         => null,
            'last_processed_at' => null,
        ];

        $this->mock(StatsService::class)
            ->shouldReceive('getTicketStats')
            ->once()
            ->andReturn($mockedStats);

        $this->getJson(route('stats'))
            ->assertOk()
            ->assertJsonPath('top_user', null)
            ->assertJsonPath('last_processed_at', null)
            ->assertJsonPath('total_tickets', 0)
            ->assertJsonPath('open_tickets', 0);
    });
});

