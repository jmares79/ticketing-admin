<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

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

    public function scopeInProgress(Builder $query): void
    {
        $query->where('status', TicketStatus::InProgress);
    }
}
