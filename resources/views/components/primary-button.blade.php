<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ui-button ui-button--primary text-xs uppercase tracking-[0.24em]']) }}>
    {{ $slot }}
</button>
