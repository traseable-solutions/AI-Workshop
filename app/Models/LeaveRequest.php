<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'start_date', 'end_date', 'type', 'reason', 'status', 'level', 'package_amount',
    'leave_destination', 'leave_address', 'phone_contact', 'travel_expense_assistance',
    'hod_recommended', 'hod_relief_required', 'hod_comments', 'hod_reviewed_by', 'hod_reviewed_at',
    'ps_reviewed_by', 'ps_reviewed_at',
])]
class LeaveRequest extends Model
{
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'hod_recommended' => 'boolean',
            'hod_relief_required' => 'boolean',
            'hod_reviewed_at' => 'datetime',
            'ps_reviewed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<User, $this> */
    public function hodReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_reviewed_by');
    }

    /** @return BelongsTo<User, $this> */
    public function psReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ps_reviewed_by');
    }

    public function daysRequested(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function totalPackageAmount(): int
    {
        return ($this->package_amount ?? 0) + ($this->travel_expense_assistance ?? 0);
    }

    public function isAwaitingHod(): bool
    {
        return $this->status === 'pending';
    }

    public function isAwaitingPs(): bool
    {
        return $this->status === 'awaiting_ps';
    }
}
