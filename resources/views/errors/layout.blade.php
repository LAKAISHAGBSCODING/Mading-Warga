<!DOCTYPE html>
<html lang="id" class="bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>@yield('code', '500') - @yield('title', 'Terjadi Kesalahan')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200 p-8 shadow-sm text-center">
        <div class="mx-auto mb-5 grid h-20 w-20 place-items-center rounded-3xl @yield('badge-class', 'bg-blue-50 text-blue-600')">
            @yield('icon')
        </div>
        <span class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full @yield('pill-class', 'bg-slate-100 text-slate-600') mb-2">
            Kode @yield('code', 'Error')
        </span>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            @yield('title')
        </h1>
        <p class="mt-2 text-slate-600 text-sm leading-relaxed">
            @yield('message')
        </p>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center gap-2 min-h-[48px] rounded-xl px-5 py-2.5 text-sm font-semibold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 active:scale-[.98] transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 min-h-[48px] rounded-xl px-5 py-2.5 text-sm font-semibold bg-blue-600 text-white shadow-sm hover:bg-blue-700 active:scale-[.98] transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
    <div class="mt-6 text-center text-xs text-slate-400">
        {{ config('app.name', 'Mading Warga') }} &bull; Lingkungan Rukun Tetangga Digital
    </div>
</body>
</html>
