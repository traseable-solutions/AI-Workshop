<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['leave_request_id', 'path', 'original_name', 'mime_type', 'size'])]
class LeaveRequestDocument extends Model
{
    protected $appends = ['url'];

    /** @return BelongsTo<LeaveRequest, $this> */
    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    protected function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
