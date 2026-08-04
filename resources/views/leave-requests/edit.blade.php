<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Leave Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('leave-requests.update', $leaveRequest) }}" class="space-y-4" x-data="{ type: '{{ old('type', $leaveRequest->type) }}' }">
                    @csrf
                    @method('PATCH')

                    <x-leave-request-fields :leave-request="$leaveRequest" :show-documents="false" />

                    <p class="text-xs text-gray-500 border-t pt-4">
                        Supporting documents can be added from the request's <a href="{{ route('leave-requests.show', $leaveRequest) }}" class="underline">details page</a>.
                    </p>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Save Changes</x-primary-button>
                        <a href="{{ route('leave-requests.show', $leaveRequest) }}" class="text-sm text-gray-500">Cancel editing</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
