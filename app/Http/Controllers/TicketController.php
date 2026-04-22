<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use OpenApi\Attributes as OA;

class TicketController extends Controller
{
    #[OA\Get(
        path: '/tickets/open',
        operationId: 'getOpenTickets',
        summary: 'Get paginated open tickets',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Pagination page number',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, default: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of open tickets',
                content: new OA\JsonContent(ref: '#/components/schemas/TicketPaginationResponse')
            ),
        ]
    )]
    public function openTickets()
    {
        $tickets = Ticket::with('user')
            ->open()
            ->latest()
            ->paginate(config('pagination.per_page'));

        return TicketResource::collection($tickets);
    }

    #[OA\Get(
        path: '/tickets/closed',
        operationId: 'getClosedTickets',
        summary: 'Get paginated closed tickets',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                description: 'Pagination page number',
                schema: new OA\Schema(type: 'integer', minimum: 1, default: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of closed tickets',
                content: new OA\JsonContent(ref: '#/components/schemas/TicketPaginationResponse')
            ),
        ]
    )]
    public function closedTickets()
    {
        $tickets = Ticket::with('user')
            ->closed()
            ->latest()
            ->paginate(config('pagination.per_page'));

        return TicketResource::collection($tickets);
    }
}
