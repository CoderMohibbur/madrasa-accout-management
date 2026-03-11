@props(['disabled' => false, 'value' => null])

<select 
    {{ $disabled ? 'disabled' : '' }} 
    {!! $attributes->merge([
        'class' => 'ui-select'
    ]) !!}
>
    <option value="1" {{ $value == '1' ? 'selected' : '' }}>Active</option>
    <option value="0" {{ $value == '0' ? 'selected' : '' }}>Inactive</option>
</select>
