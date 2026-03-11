@props([
    'url' => null,
    'ctaLabel' => 'বাহ্যিক ভর্তি ফর্ম খুলুন',
    'availableMessage' => "ভর্তি আবেদনপত্র অনুমোদিত প্রতিষ্ঠানের বাহ্যিক admission site-এই রয়েছে।",
    'safetyMessage' => null,
    'fallbackTitle' => 'External application link unavailable',
    'fallbackMessage' => 'ভর্তি নির্দেশনা এখানে দেখা যাচ্ছে, তবে এই পরিবেশে live external application destination কনফিগার করা নেই।',
    'messageClass' => 'text-sm leading-7 text-slate-600',
    'buttonClass' => 'ui-public-action ui-public-action--primary mt-5',
    'safetyClass' => 'mt-4 text-sm leading-7 text-slate-500',
])

@if ($url)
    @if ($availableMessage)
        <p class="{{ $messageClass }}">
            {{ $availableMessage }}
        </p>
    @endif

    <a href="{{ $url }}" class="{{ $buttonClass }}" target="_blank" rel="noreferrer">
        {{ $ctaLabel }}
    </a>

    @if ($safetyMessage)
        <p class="{{ $safetyClass }}">
            {{ $safetyMessage }}
        </p>
    @endif
@else
    <x-ui.alert variant="warning" :title="$fallbackTitle">
        {{ $fallbackMessage }}
    </x-ui.alert>
@endif
