<!DOCTYPE html>
<html lang="id" class="bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="relative overflow-hidden bg-blue-600 pb-24 pt-10 text-center text-white rounded-b-[2.5rem]">
        <div class="absolute -left-12 -top-12 h-44 w-44 rounded-full bg-white/10"></div>
        <div class="absolute -right-8 top-10 h-28 w-28 rounded-full bg-white/10"></div>
        <a href="/" class="relative inline-flex flex-col items-center gap-3">
            <span class="rounded-2xl bg-white p-1.5 shadow-lg shadow-blue-900/20">
                <x-logo class="h-14 w-14" />
            </span>
            <span class="text-2xl font-extrabold tracking-tight">{{ config('app.name') }}</span>
        </a>
        <p class="relative mt-1 text-blue-100">Informasi dan kegiatan warga dalam satu tempat</p>
    </div>

    <div class="-mt-16 px-4 pb-12">
        <div class="mx-auto w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 sm:p-8">
            {{ $slot }}
        </div>
    </div>
</body>
</html>