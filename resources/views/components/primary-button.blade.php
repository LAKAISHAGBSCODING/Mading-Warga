<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 min-h-[52px] rounded-xl px-6 py-3 text-base font-semibold bg-blue-600 text-white shadow-sm hover:bg-blue-700 active:scale-[.98] transition']) }}>
    {{ $slot }}
</button>