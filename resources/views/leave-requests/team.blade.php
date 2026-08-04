<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Team Leave Requests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div id="cancellation-notice" class="hidden bg-amber-100 text-amber-800 px-4 py-3 rounded"></div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                            <th class="px-6 py-3">Employee</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Start</th>
                            <th class="px-6 py-3">End</th>
                            <th class="px-6 py-3">Level</th>
                            <th class="px-6 py-3">Package</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($leaveRequests as $leaveRequest)
                            <tr>
                                <td class="px-6 py-4">{{ $leaveRequest->user->name }}</td>
                                <td class="px-6 py-4 capitalize">{{ $leaveRequest->type }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->start_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->end_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->level ?? '—' }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->package_amount ? '$'.number_format($leaveRequest->package_amount) : '—' }}</td>
                                <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $leaveRequest->status) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('leave-requests.show', $leaveRequest) }}" class="text-indigo-700">
                                            {{ $leaveRequest->isAwaitingHod() ? 'Review' : 'View' }}
                                        </a>
                                        <x-document-indicator :leave-request="$leaveRequest" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500" colspan="8">No team leave requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{--
        No push/email channel is wired up for this app yet (would need a mail
        service or Firebase project the user hasn't set up) — this is a
        client-local stand-in, the same trick used for the employee's own
        status-change toast in the mobile app: diff each request's status
        against what this browser last saw, and only flag cancellations,
        since approve/reject are always the manager's own action.
    --}}
    @php
        $teamNoticeData = $leaveRequests->map(fn ($r) => [
            'id' => $r->id,
            'status' => $r->status,
            'name' => $r->user->name,
            'type' => $r->type,
            'start' => $r->start_date->format('Y-m-d'),
            'end' => $r->end_date->format('Y-m-d'),
        ])->values();
    @endphp
    <script>
        (function () {
            const STORAGE_KEY = 'leave_management_web_team_seen_statuses';
            const requests = @json($teamNoticeData);

            const seen = JSON.parse(localStorage.getItem(STORAGE_KEY) ?? '{}');
            const changes = [];

            requests.forEach((r) => {
                const previous = seen[r.id];
                if (previous && previous !== 'cancelled' && r.status === 'cancelled') {
                    changes.push(`${r.name}'s ${r.type} leave request (${r.start} → ${r.end}) was cancelled.`);
                }
                seen[r.id] = r.status;
            });

            localStorage.setItem(STORAGE_KEY, JSON.stringify(seen));

            if (changes.length > 0) {
                const notice = document.getElementById('cancellation-notice');
                notice.textContent = changes.join(' ');
                notice.classList.remove('hidden');
            }
        })();
    </script>
</x-app-layout>
