<?php
declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PaginationMetaLink',
    type: 'object',
    required: ['url', 'label', 'active'],
    properties: [
        new OA\Property(property: 'url', type: 'string', nullable: true, example: 'http://localhost:8000/api/v1/tickets/open?page=1'),
        new OA\Property(property: 'label', type: 'string', example: '1'),
        new OA\Property(property: 'active', type: 'boolean', example: true),
    ]
)]
class PaginationMetaLinkSchema
{
}
