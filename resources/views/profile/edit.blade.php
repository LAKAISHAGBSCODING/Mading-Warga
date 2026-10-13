<x-mading-layout title="Profil saya" subtitle="Foto dan data diri kamu.">

    @if(session('status') === 'password-updated')
        <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            <x-ic name="check" class="h-5 w-5 mt-0.5 shrink-0" />
            <p class="font-semibold">Kata sandi berhasil diganti.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card space-y-5">
        @csrf
        @method('PATCH')

        <div class="flex flex-col items-center gap-3 text-center">
            <div id="foto-lama">
                <x-avatar :user="$user" size="h-28 w-28" text="text-5xl" class="ring-4 ring-blue-100" />
            </div>
            <img id="foto-baru" class="hidden h-28 w-28 rounded-full object-cover ring-4 ring-blue-100" alt="Pratinjau foto">

            <label class="btn-outline cursor-pointer">
                <x-ic name="plus" class="h-5 w-5" /> Pilih foto
                <input id="input-foto" type="file" name="foto" accept="image/*" class="hidden">
            </label>

            @if($user->foto)
                <label class="flex items-center gap-2 text-[15px] text-slate-600">
                    <input type="checkbox" name="hapus_foto" value="1" class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                    Hapus foto saat ini
                </label>
            @endif
            <p class="meta">Format JPG atau PNG, maksimal 3 MB.</p>
        </div>

        <div>
            <label class="lbl">Nama lengkap</label>
            <input class="field" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div>
            <label class="lbl">Email</label>
            <input class="field" type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="lbl">Rumah / Blok</label>
                <input class="field" name="no_rumah" value="{{ old('no_rumah', $user->no_rumah) }}" required>
            </div>
            <div>
                <label class="lbl">WhatsApp</label>
                <input class="field" type="tel" name="no_wa" value="{{ old('no_wa', $user->no_wa) }}" required>
            </div>
        </div>

        <button class="btn-primary w-full"><x-ic name="save" class="h-5 w-5" /> Simpan profil</button>
    </form>

    <form method="POST" action="{{ route('password.update') }}" class="card space-y-5">
        @csrf
        @method('PUT')
        <div>
            <h2 class="text-lg font-bold text-slate-900">Ganti kata sandi</h2>
            <p class="meta">Gunakan sandi yang sulit ditebak orang lain.</p>
        </div>

        @if($errors->updatePassword->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->updatePassword->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="lbl">Kata sandi saat ini</label>
            <input class="field" type="password" name="current_password" autocomplete="current-password">
        </div>
        <div>
            <label class="lbl">Kata sandi baru</label>
            <input class="field" type="password" name="password" autocomplete="new-password">
        </div>
        <div>
            <label class="lbl">Ulangi kata sandi baru</label>
            <input class="field" type="password" name="password_confirmation" autocomplete="new-password">
        </div>
        <button class="btn-dark w-full">Ganti kata sandi</button>
    </form>

        <form method="POST" action="{{ route('logout') }}"
          data-konfirm="Kamu harus masuk lagi untuk membuka aplikasi."
          data-konfirm-judul="Keluar dari akun?"
          data-konfirm-ya="Ya, keluar"
          data-konfirm-tipe="biasa">
        @csrf
        <button class="btn-outline w-full">
            <x-ic name="logout" class="h-5 w-5" /> Keluar dari akun
        </button>
    </form>

    @unless($user->isAdmin())
        <details class="card" @if($errors->userDeletion->any()) open @endif>
            <summary class="cursor-pointer font-bold text-red-700">Hapus akun saya</summary>
            <form method="POST" action="{{ route('profile.destroy') }}" class="mt-4 space-y-4"
                  <form method="POST" action="{{ route('profile.destroy') }}" class="mt-4 space-y-4"
      data-konfirm="Akun dan semua data kamu akan hilang permanen."
      data-konfirm-judul="Hapus akun saya?">
                @csrf
                @method('DELETE')
                <p class="text-slate-600">Semua data kamu akan hilang dan tidak bisa dikembalikan. Masukkan kata sandi untuk melanjutkan.</p>
                @if($errors->userDeletion->any())
                    <p class="text-sm font-medium text-red-600">{{ $errors->userDeletion->first() }}</p>
                @endif
                <input class="field" type="password" name="password" placeholder="Kata sandi">
                <button class="btn-danger w-full"><x-ic name="trash" class="h-5 w-5" /> Hapus akun</button>
            </form>
        </details>
    @endunless

    <script>
        document.getElementById('input-foto').addEventListener('change', function (e) {
            const f = e.target.files[0];
            if (!f) return;
            const img = document.getElementById('foto-baru');
            img.src = URL.createObjectURL(f);
            img.classList.remove('hidden');
            document.getElementById('foto-lama').classList.add('hidden');
        });
    </script>
</x-mading-layout>