<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\StatsService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class StatsController extends Controller
{
    #[OA\Get(
        path: '/stats',
        operationId: 'getTicketStats',
        summary: 'Get system-wide ticket statistics',
        tags: ['Stats'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ticket statistics',
                content: new OA\JsonContent(ref: '#/components/schemas/StatsResponse')
            ),
        ]
    )]
    public function __invoke(Request $request, StatsService $service)
    {
        return response()->json($service->getTicketStats());
    }
}
