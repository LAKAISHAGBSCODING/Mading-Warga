<x-guest-layout>
    <h1 class="text-2xl font-extrabold text-slate-900">Daftar warga</h1>
    <p class="mt-1 text-slate-500">Isi data dengan benar. Akun akan diperiksa pengurus RT sebelum aktif.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="name" class="lbl">Nama lengkap</label>
            <input id="name" class="field" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Sesuai KTP">
            @error('name') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="lbl">Email</label>
            <input id="email" class="field" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com">
            @error('email') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="no_rumah" class="lbl">Rumah / Blok</label>
                <input id="no_rumah" class="field" type="text" name="no_rumah" value="{{ old('no_rumah') }}" required placeholder="A-05">
                @error('no_rumah') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="no_wa" class="lbl">WhatsApp</label>
                <input id="no_wa" class="field" type="tel" inputmode="tel" name="no_wa" value="{{ old('no_wa') }}" required placeholder="08123456789">
                @error('no_wa') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="password" class="lbl">Kata sandi</label>
            <div class="relative">
                <input id="password" class="field pr-24" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                <button type="button"
                        onclick="const i=document.getElementById('password');const j=document.getElementById('password_confirmation');const t=i.type==='password'?'text':'password';i.type=t;j.type=t;this.textContent=t==='password'?'Lihat':'Sembunyi'"
                        class="absolute inset-y-0 right-4 text-sm font-semibold text-blue-700">Lihat</button>
            </div>
            @error('password') <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="lbl">Ulangi kata sandi</label>
            <input id="password_confirmation" class="field" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik sekali lagi">
        </div>

        <button class="btn-primary w-full">Daftar</button>
    </form>

    <p class="mt-6 text-center text-slate-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold text-blue-700">Masuk</a>
    </p>
</x-guest-layout>