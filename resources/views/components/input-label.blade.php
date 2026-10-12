@props(['value'])
<label {{ $attributes->merge(['class' => 'block text-[15px] font-semibold text-slate-700 mb-2']) }}>
    {{ $value ?? $slot }}
</label>