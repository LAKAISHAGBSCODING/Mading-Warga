<!DOCTYPE html>
<html lang="id" class="bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>{{ $title ?? 'Beranda' }} - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased pb-28">

<header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-slate-200">
    <div class="max-w-2xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <x-logo class="h-10 w-10" />
            <span class="text-lg font-extrabold tracking-tight text-slate-900">{{ config('app.name') }}</span>
        </a>
        <a href="{{ route('profile.edit') }}" aria-label="Profil" class="rounded-full ring-2 ring-transparent hover:ring-blue-200">
    <x-avatar :user="auth()->user()" size="h-10 w-10" />
</a>
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
    ['Forum', 'chat', 'forum.index', 'forum.*'],
    ['Lapak', 'bag', 'lapak.index', 'lapak.*'],
];
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
<div id="modal-konfirm" class="fixed inset-0 z-50 hidden items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="konfirm-judul">
    <div data-tutup class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl">
        <div id="konfirm-ikon" class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-red-50 text-red-600">
            <x-ic name="trash" class="h-8 w-8" />
        </div>
        <h3 id="konfirm-judul" class="mt-4 text-center text-xl font-extrabold text-slate-900">Hapus data ini?</h3>
        <p id="konfirm-teks" class="mt-1 text-center leading-relaxed text-slate-600">Data yang dihapus tidak bisa dikembalikan.</p>
        <div class="mt-6 grid grid-cols-2 gap-3">
            <button type="button" data-tutup class="btn-outline">Tidak</button>
            <button type="button" id="konfirm-ya" class="btn-primary !bg-red-600 hover:!bg-red-700">Ya, hapus</button>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('modal-konfirm');
    const ya = document.getElementById('konfirm-ya');
    const ikon = document.getElementById('konfirm-ikon');
    let form = null, tombol = null;

    function buka(sumber, f, t) {
        form = f; tombol = t;
        document.getElementById('konfirm-judul').textContent = sumber.dataset.konfirmJudul || 'Hapus data ini?';
        document.getElementById('konfirm-teks').textContent = sumber.dataset.konfirm || 'Data yang dihapus tidak bisa dikembalikan.';
        ya.textContent = sumber.dataset.konfirmYa || 'Ya, hapus';
        const biasa = sumber.dataset.konfirmTipe === 'biasa';
        ikon.className = 'mx-auto grid h-16 w-16 place-items-center rounded-full ' + (biasa ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600');
        ya.className = 'btn-primary ' + (biasa ? '' : '!bg-red-600 hover:!bg-red-700');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        ya.focus();
    }
    function tutup() {
        modal.classList.add('hidden'); modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        form = tombol = null;
    }

    document.addEventListener('submit', function (e) {
        const f = e.target, t = e.submitter;
        const sumber = (t && t.dataset.konfirm !== undefined) ? t : (f.dataset.konfirm !== undefined ? f : null);
        if (!sumber) return;
        e.preventDefault();
        buka(sumber, f, t);
    });

    ya.addEventListener('click', function () {
        const f = form, t = tombol;
        if (!f) return;
        if (t && t.name) {
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = t.name; h.value = t.value;
            f.appendChild(h);
        }
        tutup();
        HTMLFormElement.prototype.submit.call(f);
    });

    modal.addEventListener('click', e => { if (e.target.hasAttribute('data-tutup')) tutup(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') tutup(); });
})();
</script>
</body>
</html>