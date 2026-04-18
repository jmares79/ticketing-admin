<?php
declare(strict_types=1);

namespace App\Enums;

/**
 * Enum to map ticket statuses
 */
enum TicketStatus: int
{
    case Open   = 0;
    case Closed = 1;
}
