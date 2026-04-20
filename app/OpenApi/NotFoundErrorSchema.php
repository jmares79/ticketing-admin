<?php
declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NotFoundError',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'No query results for model [App\\Models\\User] 100'),
    ]
)]
class NotFoundErrorSchema
{
}
