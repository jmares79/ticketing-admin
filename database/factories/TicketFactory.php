<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(),
            'content' => fake()->paragraphs(2, true),
            'user_id' => User::factory(),
            'status'  => TicketStatus::Open,
        ];
    }

    public function closed(): static
    {
        return $this->state(['status' => TicketStatus::Closed]);
    }
}
