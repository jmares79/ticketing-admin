<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\StatsService;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function __invoke(Request $request, StatsService $service)
    {
        return response()->json($service->getTicketStats());
    }
}
