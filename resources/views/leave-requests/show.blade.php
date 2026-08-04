<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Annual Leave Application') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 capitalize">{{ $leaveRequest->type }} Leave</h3>
                    <span class="capitalize px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">
                        {{ str_replace('_', ' ', $leaveRequest->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">{{ $leaveRequest->start_date->format('Y-m-d') }} &rarr; {{ $leaveRequest->end_date->format('Y-m-d') }} ({{ $leaveRequest->daysRequested() }} days)</p>
                @if ($leaveRequest->reason)
                    <p class="text-sm text-gray-700"><span class="text-gray-500">Reason:</span> {{ $leaveRequest->reason }}</p>
                @endif
                <p class="text-xs text-gray-500">Submitted by {{ $leaveRequest->user->name }} on {{ $leaveRequest->created_at->format('Y-m-d') }}.</p>
            </div>

            @if ($leaveRequest->type === 'annual')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Personal Information</h3>
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div><dt class="text-gray-500">Name</dt><dd>{{ $leaveRequest->user->name }}</dd></div>
                        <div><dt class="text-gray-500">TPF</dt><dd>{{ $leaveRequest->user->tpf ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Position</dt><dd>{{ $leaveRequest->user->position ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Substantive Level</dt><dd>{{ $leaveRequest->user->level ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Duty Station</dt><dd>{{ $leaveRequest->user->duty_station ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Leave Destination</dt><dd>{{ $leaveRequest->leave_destination ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Leave Address</dt><dd>{{ $leaveRequest->leave_address ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Phone Contact</dt><dd>{{ $leaveRequest->phone_contact ?? '—' }}</dd></div>
                    </dl>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Annual Leave Assistance Package Calculations</h3>
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div><dt class="text-gray-500">Annual Leave Package Assistance</dt><dd>${{ number_format($leaveRequest->package_amount ?? 0) }}</dd></div>
                        <div><dt class="text-gray-500">Annual Leave Travelling Expense Assistance</dt><dd>${{ number_format($leaveRequest->travel_expense_assistance ?? 0) }}</dd></div>
                        <div class="font-semibold"><dt class="text-gray-500 font-normal">Total Annual Leave Package</dt><dd>${{ number_format($leaveRequest->totalPackageAmount()) }}</dd></div>
                    </dl>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Head of Division / Department Decision</h3>

                @if ($leaveRequest->hod_reviewed_at)
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div><dt class="text-gray-500">Decision</dt><dd class="capitalize">{{ $leaveRequest->status }}</dd></div>
                        <div><dt class="text-gray-500">Leave Relief Required</dt><dd>{{ $leaveRequest->hod_relief_required ? 'Yes' : 'No' }}</dd></div>
                        @if ($leaveRequest->hod_comments)
                            <div class="col-span-2"><dt class="text-gray-500">Comments</dt><dd>{{ $leaveRequest->hod_comments }}</dd></div>
                        @endif
                        <div class="col-span-2 text-xs text-gray-500">
                            {{ $leaveRequest->hodReviewer->name }} on {{ $leaveRequest->hod_reviewed_at->format('Y-m-d') }}
                        </div>
                    </dl>
                @elseif ($leaveRequest->isAwaitingHod() && auth()->id() === $leaveRequest->user->manager_id)
                    <form method="POST" action="{{ route('leave-requests.decide', $leaveRequest) }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label value="Leave Relief Required" />
                            <div class="mt-1 space-x-4">
                                <label><input type="radio" name="hod_relief_required" value="1" required> Yes</label>
                                <label><input type="radio" name="hod_relief_required" value="0" required> No</label>
                            </div>
                            <x-input-error :messages="$errors->get('hod_relief_required')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="hod_comments" value="Comments" />
                            <textarea id="hod_comments" name="hod_comments" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            <x-input-error :messages="$errors->get('hod_comments')" class="mt-2" />
                        </div>

                        <div class="space-x-2">
                            <button type="submit" name="decision" value="approved" class="px-4 py-2 bg-green-700 text-white rounded-md text-sm">Approved</button>
                            <button type="submit" name="decision" value="rejected" class="px-4 py-2 bg-red-700 text-white rounded-md text-sm">Not Approved</button>
                        </div>
                        <x-input-error :messages="$errors->get('decision')" class="mt-2" />
                    </form>
                @else
                    <p class="text-sm text-gray-500">Awaiting HOD decision.</p>
                @endif
            </div>

            @if ($leaveRequest->type === 'annual' && $leaveRequest->status !== 'approved')
                <p class="text-xs text-gray-500">You are not allowed to proceed on leave until you have received a LEAVE CERTIFICATE.</p>
            @endif
        </div>
    </div>
</x-app-layout>
