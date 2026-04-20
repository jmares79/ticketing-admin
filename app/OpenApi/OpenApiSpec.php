<?php
declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Ticketing Admin API',
    description: 'REST API for ticket listing and statistics.'
)]
#[OA\Server(
    url: '/api/v1',
    description: 'Version 1'
)]
#[OA\Tag(
    name: 'Tickets',
    description: 'Endpoints for open and closed tickets'
)]
#[OA\Tag(
    name: 'Users',
    description: 'Endpoints for user ticket listings'
)]
#[OA\Tag(
    name: 'Stats',
    description: 'System-wide ticket statistics'
)]
class OpenApiSpec
{
}
