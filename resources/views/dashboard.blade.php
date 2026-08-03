<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>

            <a
                href="{{ route('user-guide.download') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                </svg>
                {{ __('Download User Guide') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">On Leave Today</h3>

                @if ($onLeaveToday->isEmpty())
                    <p class="text-gray-500">No one is on leave today.</p>
                @else
                    <ul class="divide-y divide-blue-50">
                        @foreach ($onLeaveToday as $user)
                            <li class="py-2 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-blue-600 shrink-0"></span>
                                <span class="text-gray-900">{{ $user->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ now()->format('F Y') }}</h3>

                <div class="grid grid-cols-7 gap-px bg-blue-50 text-xs font-medium text-gray-400 uppercase">
                    @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                        <div class="bg-white px-2 py-2 text-center">{{ $dayName }}</div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7 gap-px bg-blue-50">
                    @foreach ($calendarDays as $day)
                        <div class="bg-white min-h-[3.5rem] p-2 {{ $day['inMonth'] ? '' : 'bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-sm {{ $day['inMonth'] ? 'text-gray-700' : 'text-gray-300' }} {{ $day['isToday'] ? 'inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white font-semibold' : '' }}">
                                    {{ $day['date']->day }}
                                </span>

                                @if ($day['users']->isNotEmpty())
                                    <span
                                        class="inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1 rounded-full bg-blue-100 text-blue-800 text-xs font-medium"
                                        title="{{ $day['users']->pluck('name')->join(', ') }}"
                                    >
                                        {{ $day['users']->count() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <x-bar-chart title="Leave by Type" :bars="$leaveByType" />
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <x-bar-chart title="Leave by Status" :bars="$leaveByStatus" />
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Leave</h3>

                @if ($upcomingLeave->isEmpty())
                    <p class="text-gray-500">No upcoming leave scheduled.</p>
                @else
                    <ul class="divide-y divide-blue-50">
                        @foreach ($upcomingLeave as $leaveRequest)
                            <li class="py-2 flex items-center justify-between gap-2">
                                <span class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-blue-600 shrink-0"></span>
                                    <span class="text-gray-900">{{ $leaveRequest->user->name }}</span>
                                </span>
                                <span class="text-sm text-gray-500">{{ $leaveRequest->start_date->format('M j') }} &ndash; {{ $leaveRequest->end_date->format('M j') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
