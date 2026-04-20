<?php
declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StatsResponse',
    type: 'object',
    required: ['total_tickets', 'open_tickets', 'top_user', 'last_processed_at'],
    properties: [
        new OA\Property(property: 'total_tickets', type: 'integer', example: 100),
        new OA\Property(property: 'open_tickets', type: 'integer', example: 65),
        new OA\Property(
            property: 'top_user',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
            ]
        ),
        new OA\Property(property: 'last_processed_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class StatsResponseSchema
{
}
