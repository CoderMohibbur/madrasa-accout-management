@props(['disabled' => false, 'value' => null])

<select 
    {{ $disabled ? 'disabled' : '' }} 
    {!! $attributes->merge([
        'class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-full shadow-sm'
    ]) !!}
>
    <option value="1" {{ $value == '1' ? 'selected' : '' }}>Active</option>
    <option value="0" {{ $value == '0' ? 'selected' : '' }}>Inactive</option>
</select>

