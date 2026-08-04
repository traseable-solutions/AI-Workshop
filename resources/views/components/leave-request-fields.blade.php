@props(['leaveRequest' => null, 'showDocuments' => true])

@php
    $value = fn (string $field) => old($field, $leaveRequest?->$field);
    // start_date/end_date are Carbon instances on the model but plain "Y-m-d" strings
    // when they're old() input from a failed validation redirect — format only the former.
    $dateValue = fn (string $field) => old($field) ?? $leaveRequest?->$field?->format('Y-m-d');
@endphp

<div>
    <x-input-label for="type" value="Type" />
    <select id="type" name="type" x-model="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="annual" @selected($value('type') === 'annual')>Annual</option>
        <option value="sick" @selected($value('type') === 'sick')>Sick</option>
        <option value="unpaid" @selected($value('type') === 'unpaid')>Unpaid</option>
        <option value="compassionate" @selected($value('type') === 'compassionate')>Compassionate</option>
        <option value="maternity" @selected($value('type') === 'maternity')>Maternity</option>
    </select>
    <x-input-error :messages="$errors->get('type')" class="mt-2" />
</div>

<div>
    <x-input-label for="start_date" value="Date Leave Commences" />
    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="$dateValue('start_date')" required />
    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
</div>

<div>
    <x-input-label for="end_date" value="End date" />
    <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="$dateValue('end_date')" required />
    <p class="text-xs text-gray-500 mt-1">Days applied for and the date you resume duties are calculated from your start and end dates (weekends &amp; public holidays included).</p>
    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
</div>

<div>
    <x-input-label for="reason" value="Reason (optional)" />
    <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $value('reason') }}</textarea>
    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
</div>

@if ($showDocuments)
    <div>
        <x-input-label for="documents" value="Supporting Documents (optional)" />
        <input id="documents" name="documents[]" type="file" multiple accept="image/jpeg,image/png,application/pdf" class="mt-1 block w-full text-sm text-gray-700" />
        <p class="text-xs text-gray-500 mt-1">JPEG, PNG or PDF, up to 10MB each — e.g. a medical certificate.</p>
        <x-input-error :messages="$errors->get('documents')" class="mt-2" />
        <x-input-error :messages="$errors->get('documents.0')" class="mt-2" />
    </div>
@endif

<div x-show="type === 'annual'" class="space-y-4 border-t pt-4">
    <h3 class="text-sm font-semibold text-gray-700 uppercase">Personal Information</h3>

    <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 rounded-md p-3">
        <div><span class="text-gray-500">Name:</span> {{ auth()->user()->name }}</div>
        <div><span class="text-gray-500">TPF:</span> {{ auth()->user()->tpf ?? '—' }}</div>
        <div><span class="text-gray-500">Position:</span> {{ auth()->user()->position ?? '—' }}</div>
        <div><span class="text-gray-500">Substantive Level:</span> {{ auth()->user()->level ?? '—' }}</div>
        <div class="col-span-2"><span class="text-gray-500">Duty Station:</span> {{ auth()->user()->duty_station ?? '—' }}</div>
    </div>
    <p class="text-xs text-gray-500">
        Missing or out of date? Update it on your <a href="{{ route('profile.edit') }}" class="underline">profile</a>.
    </p>

    <div>
        <x-input-label for="leave_destination" value="Leave Destination" />
        <x-text-input id="leave_destination" name="leave_destination" type="text" class="mt-1 block w-full" :value="$value('leave_destination')" x-bind:required="type === 'annual'" />
        <x-input-error :messages="$errors->get('leave_destination')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="leave_address" value="Leave Address" />
        <x-text-input id="leave_address" name="leave_address" type="text" class="mt-1 block w-full" :value="$value('leave_address')" x-bind:required="type === 'annual'" />
        <x-input-error :messages="$errors->get('leave_address')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="phone_contact" value="Phone Contact" />
        <x-text-input id="phone_contact" name="phone_contact" type="text" class="mt-1 block w-full" :value="$value('phone_contact')" x-bind:required="type === 'annual'" />
        <x-input-error :messages="$errors->get('phone_contact')" class="mt-2" />
    </div>
</div>

<div x-show="type === 'annual'" class="space-y-4 border-t pt-4">
    <h3 class="text-sm font-semibold text-gray-700 uppercase">Annual Leave Assistance Package Calculations</h3>

    <div>
        <x-input-label for="level" value="Level" />
        <select id="level" name="level" x-bind:disabled="type !== 'annual'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" @selected($value('level') == $i)>Level {{ $i }}</option>
            @endfor
        </select>
        <x-input-error :messages="$errors->get('level')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="package_amount" value="Annual Leave Package Assistance" />
        <select id="package_amount" name="package_amount" x-bind:disabled="type !== 'annual'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="8000" @selected($value('package_amount') == 8000)>$8,000</option>
            <option value="10000" @selected($value('package_amount') == 10000)>$10,000</option>
        </select>
        <x-input-error :messages="$errors->get('package_amount')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="travel_expense_assistance" value="Annual Leave Travelling Expense Assistance ($)" />
        <x-text-input id="travel_expense_assistance" name="travel_expense_assistance" type="number" min="0" class="mt-1 block w-full" :value="$value('travel_expense_assistance')" />
        <x-input-error :messages="$errors->get('travel_expense_assistance')" class="mt-2" />
    </div>
</div>
