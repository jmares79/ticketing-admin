<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserTicketController extends Controller
{
    public function index(Request $request, User $user)
    {
        $tickets = $user->tickets()
            ->with('user')
            ->latest()
            ->paginate(25);

        return TicketResource::collection($tickets);
    }
}
