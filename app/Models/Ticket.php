<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

class Ticket extends Model
{
    protected $fillable = ['subject', 'content', 'user_id', 'status'];

    protected $casts = [
        'status' => TicketStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpen(Builder $query): void
    {
        $query->where('status', TicketStatus::Open);
    }

    public function scopeClosed(Builder $query): void
    {
        $query->where('status', TicketStatus::Closed);
    }
}
