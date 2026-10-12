<!DOCTYPE html>
<html lang="id" class="bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">
        <a href="/" class="flex flex-col items-center gap-3 mb-8">
            <span class="grid place-items-center h-16 w-16 rounded-2xl bg-blue-600 text-white text-3xl font-extrabold shadow-lg shadow-blue-600/20">M</span>
            <span class="text-2xl font-extrabold tracking-tight text-slate-900">{{ config('app.name') }}</span>
        </a>
        <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
            {{ $slot }}
        </div>
    </div>
</body>
</html>