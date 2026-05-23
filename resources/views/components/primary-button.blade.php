<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-seait-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-seait-600 focus:bg-seait-600 active:bg-seait-800 focus:outline-none focus:ring-2 focus:ring-seait-400 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
