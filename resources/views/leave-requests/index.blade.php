<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Leave Requests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 flex items-center justify-between">
                <div class="flex gap-10">
                    <div>
                        <p class="text-sm text-gray-500">Remaining balance</p>
                        <p class="text-2xl font-semibold">{{ $balance }} days</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Leave package</p>
                        <p class="text-2xl font-semibold">${{ number_format($packageAmount) }}</p>
                    </div>
                </div>
                <a href="{{ route('leave-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm">
                    New Request
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Start</th>
                            <th class="px-6 py-3">End</th>
                            <th class="px-6 py-3">Reason</th>
                            <th class="px-6 py-3">Level</th>
                            <th class="px-6 py-3">Package</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($leaveRequests as $leaveRequest)
                            <tr>
                                <td class="px-6 py-4 capitalize">{{ $leaveRequest->type }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->start_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->end_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->reason }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->level ?? '—' }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->package_amount ? '$'.number_format($leaveRequest->package_amount) : '—' }}</td>
                                <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $leaveRequest->status) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('leave-requests.show', $leaveRequest) }}" class="text-indigo-700">View</a>
                                        <x-document-indicator :leave-request="$leaveRequest" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500" colspan="8">No leave requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
