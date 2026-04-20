<?php
declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PaginationLinks',
    type: 'object',
    required: ['first', 'last', 'prev', 'next'],
    properties: [
        new OA\Property(property: 'first', type: 'string', nullable: true, example: 'http://localhost:8000/api/v1/tickets/open?page=1'),
        new OA\Property(property: 'last', type: 'string', nullable: true, example: 'http://localhost:8000/api/v1/tickets/open?page=3'),
        new OA\Property(property: 'prev', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'next', type: 'string', nullable: true, example: 'http://localhost:8000/api/v1/tickets/open?page=2'),
    ]
)]
class PaginationLinksSchema
{
}
