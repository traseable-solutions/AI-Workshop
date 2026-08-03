@props(['title', 'bars'])

@php
    $rawMax = collect($bars)->max() ?: 0;
    $axisMax = match (true) {
        $rawMax <= 4 => 4,
        $rawMax <= 10 => 10,
        default => (int) ceil($rawMax / 10) * 10,
    };
@endphp

<div>
    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $title }}</h3>

    <div class="flex">
        <div class="flex flex-col justify-between h-40 pr-2 text-xs text-gray-400 text-right">
            <span>{{ $axisMax }}</span>
            <span>{{ (int) round($axisMax / 2) }}</span>
            <span>0</span>
        </div>

        <div class="flex-1">
            <div class="relative h-40 border-l border-gray-200">
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                    <div class="border-t border-gray-100"></div>
                    <div class="border-t border-gray-100"></div>
                    <div class="border-t border-gray-200"></div>
                </div>

                <div class="absolute inset-0 flex items-end gap-4 px-3">
                    @foreach ($bars as $label => $value)
                        <div
                            class="flex-1 flex flex-col items-center justify-end h-full"
                            title="{{ ucfirst(str_replace('_', ' ', $label)) }}: {{ $value }}"
                        >
                            <span class="text-xs font-medium text-gray-700 mb-1">{{ $value }}</span>
                            <div
                                class="w-full max-w-10 bg-blue-600 rounded-t-md"
                                style="height: {{ $value > 0 ? max(4, round($value / $axisMax * 100)) : 0 }}%"
                            ></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-4 px-3 mt-2">
                @foreach ($bars as $label => $value)
                    <span class="flex-1 text-center text-xs text-gray-500 capitalize truncate">{{ str_replace('_', ' ', $label) }}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>
