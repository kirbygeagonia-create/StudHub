@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-seait-400 focus:ring-seait-400 rounded-md shadow-sm']) }}>
