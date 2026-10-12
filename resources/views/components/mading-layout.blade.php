<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Beranda' }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-blue-50 text-gray-800 text-lg pb-28">

<header class="bg-blue-600 text-white sticky top-0 z-10 shadow">
    <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="font-bold text-xl">🏘️ {{ config('app.name') }}</div>
        <div class="flex items-center gap-3 text-base">
            <a href="{{ route('profile.edit') }}" class="underline">{{ Str::words(auth()->user()->name, 1, '') }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="bg-white/20 rounded-lg px-3 py-1">Keluar</button>
            </form>
        </div>
    </div>
</header>

<main class="max-w-2xl mx-auto px-4 py-5 space-y-4">
    @if(!empty($title))
        <h1 class="text-2xl font-bold text-blue-900">{{ $title }}</h1>
    @endif

    @if(session('sukses'))
        <div class="rounded-xl bg-green-100 text-green-800 p-4 font-semibold">{{ session('sukses') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-red-100 text-red-800 p-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{ $slot }}
</main>

@php
$menu = [
    ['Beranda', '🏠', 'dashboard', 'dashboard'],
    ['Mading', '📢', 'pengumuman.index', 'pengumuman.*'],
    ['Agenda', '📅', 'acara.index', 'acara.*'],
    ['Forum', '💬', 'forum.index', 'forum.*'],
    ['Lapak', '🛍️', 'lapak.index', 'lapak.*'],
];
@endphp
<nav class="fixed bottom-0 inset-x-0 bg-white border-t border-blue-100 shadow-[0_-2px_8px_rgba(0,0,0,0.05)]">
    <div class="max-w-2xl mx-auto grid grid-cols-5">
        @foreach($menu as [$nama, $ikon, $rute, $pola])
            <a href="{{ route($rute) }}"
               class="flex flex-col items-center py-2 text-sm {{ request()->routeIs($pola) ? 'text-blue-700 font-bold bg-blue-50' : 'text-gray-500' }}">
                <span class="text-2xl leading-none">{{ $ikon }}</span>
                {{ $nama }}
            </a>
        @endforeach
    </div>
</nav>
</body>
</html>