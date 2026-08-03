<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Requests Forwarded for Decision') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                            <th class="px-6 py-3">Employee</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Start</th>
                            <th class="px-6 py-3">End</th>
                            <th class="px-6 py-3">HOD Recommendation</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($leaveRequests as $leaveRequest)
                            <tr>
                                <td class="px-6 py-4">{{ $leaveRequest->user->name }}</td>
                                <td class="px-6 py-4 capitalize">{{ $leaveRequest->type }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->start_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->end_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">{{ $leaveRequest->hod_recommended ? 'Recommended' : 'Not recommended' }}</td>
                                <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $leaveRequest->status) }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('leave-requests.show', $leaveRequest) }}" class="text-indigo-700">
                                        {{ $leaveRequest->isAwaitingPs() ? 'Decide' : 'View' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500" colspan="7">No requests forwarded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
