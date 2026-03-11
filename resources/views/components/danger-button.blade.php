<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ui-button ui-button--danger text-xs uppercase tracking-[0.24em]']) }}>
    {{ $slot }}
</button>
