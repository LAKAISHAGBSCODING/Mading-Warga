@props(['user', 'size' => 'h-10 w-10', 'text' => 'text-base'])
@if($user->foto)
    <img src="{{ asset('storage/'.$user->foto) }}" alt="{{ $user->name }}"
         {{ $attributes->merge(['class' => $size.' rounded-full object-cover shrink-0 bg-slate-100']) }}>
@else
    <span {{ $attributes->merge(['class' => 'grid place-items-center '.$size.' '.$text.' rounded-full bg-blue-100 text-blue-700 font-bold shrink-0']) }}>
        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
    </span>
@endif