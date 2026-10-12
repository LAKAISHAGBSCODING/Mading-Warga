<!DOCTYPE html>
<html lang="id" class="bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#2563eb">
    <title>{{ $title ?? 'Beranda' }} - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased pb-28">

<header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-slate-200">
    <div class="max-w-2xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="grid place-items-center h-10 w-10 rounded-xl bg-blue-600 text-white text-lg font-extrabold">M</span>
            <span class="text-lg font-extrabold tracking-tight text-slate-900">{{ config('app.name') }}</span>
        </a>
        <div class="flex items-center gap-1.5">
            <a href="{{ route('profile.edit') }}" aria-label="Profil"
               class="grid place-items-center h-10 w-10 rounded-full bg-blue-100 text-blue-700 font-bold">
                {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button aria-label="Keluar" class="grid place-items-center h-10 w-10 rounded-full text-slate-500 hover:bg-slate-100">
                    <x-ic name="logout" class="h-5 w-5" />
                </button>
            </form>
        </div>
    </div>
</header>

<main class="max-w-2xl mx-auto px-4 pt-6 space-y-5">
    @if(!empty($title))
        <div>
            <h1 class="text-[28px] leading-tight font-extrabold tracking-tight text-slate-900">{{ $title }}</h1>
            @if(!empty($subtitle))
                <p class="mt-1 text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    @if(session('sukses'))
        <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            <x-ic name="check" class="h-5 w-5 mt-0.5 shrink-0" />
            <p class="font-semibold">{{ session('sukses') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{ $slot }}
</main>

@php
$menu = [
    ['Beranda', 'home', 'dashboard', 'dashboard'],
    ['Mading', 'doc', 'pengumuman.index', 'pengumuman.*'],
    ['Agenda', 'calendar', 'acara.index', 'acara.*'],
    ['Forum', 'chat', 'forum.*', 'forum.*'],
    ['Lapak', 'bag', 'lapak.index', 'lapak.*'],
];
$menu[3][2] = 'forum.index';
@endphp
<nav class="fixed bottom-0 inset-x-0 z-20 bg-white border-t border-slate-200 pb-[env(safe-area-inset-bottom)]">
    <div class="max-w-2xl mx-auto grid grid-cols-5">
        @foreach($menu as [$nama, $ikon, $rute, $pola])
            @php $aktif = request()->routeIs($pola); @endphp
            <a href="{{ route($rute) }}" class="flex flex-col items-center gap-1 pt-2 pb-2.5 text-[12px] font-semibold {{ $aktif ? 'text-blue-700' : 'text-slate-500' }}">
                <span class="grid place-items-center h-8 w-14 rounded-full transition {{ $aktif ? 'bg-blue-100' : '' }}">
                    <x-ic :name="$ikon" class="h-6 w-6" />
                </span>
                {{ $nama }}
            </a>
        @endforeach
    </div>
</nav>
</body>
</html>