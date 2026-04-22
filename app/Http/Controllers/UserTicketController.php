<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserTicketController extends Controller
{
    #[OA\Get(
        path: '/users/{user}/tickets',
        operationId: 'getUserTickets',
        summary: 'Get paginated tickets for a specific user',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'user',
                in: 'path',
                required: true,
                description: 'User ID',
                schema: new OA\Schema(type: 'integer', minimum: 1)
            ),
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
                description: 'Paginated list of user tickets',
                content: new OA\JsonContent(ref: '#/components/schemas/TicketPaginationResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'User not found',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
        ]
    )]
    public function index(Request $request, User $user)
    {
        $tickets = $user->tickets()
            ->with('user')
            ->latest()
            ->paginate(25);

        return TicketResource::collection($tickets);
    }
}
