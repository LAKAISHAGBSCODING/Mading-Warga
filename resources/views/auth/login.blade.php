<x-guest-layout>
    <h1 class="text-2xl font-extrabold text-slate-900">Masuk</h1>
    <p class="mt-1 text-slate-500">Silakan masuk ke akun kamu.</p>

    <x-auth-session-status class="mt-4 rounded-xl bg-emerald-50 p-3 text-emerald-800" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="email" class="lbl">Email</label>
            <input id="email" class="field" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
            @error('email') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="lbl">Kata sandi</label>
            <div class="relative">
                <input id="password" class="field pr-24" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
                <button type="button"
                        onclick="const i=document.getElementById('password');i.type=i.type==='password'?'text':'password';this.textContent=i.type==='password'?'Lihat':'Sembunyi'"
                        class="absolute inset-y-0 right-4 text-sm font-semibold text-blue-700">Lihat</button>
            </div>
            @error('password') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-[15px] text-slate-600">
                <input type="checkbox" name="remember" class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-[15px] font-semibold text-blue-700">Lupa sandi?</a>
            @endif
        </div>

        <button class="btn-primary w-full">Masuk</button>
    </form>

    <p class="mt-6 text-center text-slate-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-blue-700">Daftar di sini</a>
    </p>
</x-guest-layout>