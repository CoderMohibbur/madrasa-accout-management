<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="space-y-4 p-6 text-gray-900">
                    <p class="text-sm leading-6 text-gray-700">
                        {{ $description }}
                    </p>

                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        Foundation routes are active, but no legacy accounting or auth behavior has been rewritten in this phase.
                    </div>

                    <ul class="list-disc space-y-2 pl-5 text-sm text-gray-600">
                        @foreach ($highlights as $highlight)
                            <li>{{ $highlight }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
