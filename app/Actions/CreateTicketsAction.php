<?php
declare(strict_types=1);

namespace App\Actions;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Creates a ticket to a random user. If the user pool is less than max users, it creates users
 */
class CreateTicketsAction
{
    use AsAction;

    public function handle(): Ticket
    {
        $users = $this->getUsers();

        return Ticket::create([
            'subject' => fake()->sentence(6),
            'content' => fake()->paragraphs(3, true),
            'user_id' => $users->random()->id,
            'status'  => TicketStatus::Open,
        ]);
    }

    private function getUsers(): Collection
    {
        if (User::count() < config('tickets.max_users')) {
            return User::factory()->count(5)->create();
        }

        return User::inRandomOrder()->limit(config('tickets.max_users'))->get();
    }
}
