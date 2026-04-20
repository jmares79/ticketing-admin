<?php
declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PaginationMeta',
    type: 'object',
    required: ['current_page', 'from', 'last_page', 'links', 'path', 'per_page', 'to', 'total'],
    properties: [
        new OA\Property(property: 'current_page', type: 'integer', example: 1),
        new OA\Property(property: 'from', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'last_page', type: 'integer', example: 3),
        new OA\Property(
            property: 'links',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/PaginationMetaLink')
        ),
        new OA\Property(property: 'path', type: 'string', example: 'http://localhost:8000/api/v1/tickets/open'),
        new OA\Property(property: 'per_page', type: 'integer', example: 25),
        new OA\Property(property: 'to', type: 'integer', nullable: true, example: 25),
        new OA\Property(property: 'total', type: 'integer', example: 63),
    ]
)]
class PaginationMetaSchema
{
}
