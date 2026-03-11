<button {{ $attributes->merge(['type' => 'button', 'class' => 'ui-button ui-button--secondary text-xs uppercase tracking-[0.24em]']) }}>
    {{ $slot }}
</button>
