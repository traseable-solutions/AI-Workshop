@props(['leaveRequest'])

@if ($leaveRequest->documents->isNotEmpty())
    <span class="inline-flex items-center gap-1 text-xs text-gray-500" title="{{ $leaveRequest->documents->pluck('original_name')->join(', ') }}">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 10-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
        </svg>
        {{ $leaveRequest->documents->count() }}
    </span>
@endif
