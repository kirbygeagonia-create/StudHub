<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary !bg-red-500 hover:!bg-red-600 focus:!ring-red-400']) }}>
    {{ $slot }}
</button>