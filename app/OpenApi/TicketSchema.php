<?php
declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Ticket',
    type: 'object',
    required: ['id', 'subject', 'content', 'status', 'created_at', 'updated_at', 'user'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'subject', type: 'string', example: 'Unable to reset password'),
        new OA\Property(property: 'content', type: 'string', example: 'Reset link is not arriving in my inbox.'),
        new OA\Property(property: 'status', type: 'string', enum: ['Open', 'InProgress', 'Closed'], example: 'Open'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
    ]
)]
class TicketSchema
{
}
