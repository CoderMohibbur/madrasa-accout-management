@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->class(['ui-table-shell']) }}>
    @if ($title || $description || isset($actions))
        <div class="ui-card__header">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    @if ($title)
                        <h2 class="ui-card__title">{{ $title }}</h2>
                    @endif

                    @if ($description)
                        <p class="ui-card__description">{{ $description }}</p>
                    @endif
                </div>

                @isset($actions)
                    <div class="shrink-0">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="ui-table">
            {{ $slot }}
        </table>
    </div>

    @isset($mobile)
        <div class="border-t border-slate-200/80 p-4 md:hidden">
            <div class="space-y-3">
                {{ $mobile }}
            </div>
        </div>
    @endisset
</section>
