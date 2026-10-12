<x-mading-layout title="Pasang lapak">
    <form method="POST" action="{{ route('lapak.store') }}" enctype="multipart/form-data" class="card space-y-5">
        @csrf
        <div>
            <label class="lbl">Nama produk atau jasa</label>
            <input class="field" name="nama" value="{{ old('nama') }}" required>
        </div>
        <div>
            <label class="lbl">Harga (Rp, angka saja)</label>
            <input type="number" inputmode="numeric" class="field" name="harga" value="{{ old('harga') }}" required>
        </div>
        <div>
            <label class="lbl">Deskripsi</label>
            <textarea class="field" name="deskripsi" rows="5" required>{{ old('deskripsi') }}</textarea>
        </div>
        <div>
            <label class="lbl">Foto</label>
            <input type="file" name="foto" accept="image/*" class="field">
        </div>
        <p class="rounded-xl bg-blue-50 p-3 text-sm text-blue-800">Pembeli akan menghubungi lewat nomor WhatsApp yang kamu daftarkan.</p>
        <button class="btn-primary w-full"><x-ic name="save" class="h-5 w-5" /> Tayangkan</button>
        <a href="{{ route('lapak.index') }}" class="btn-outline w-full">Batal</a>
    </form>
</x-mading-layout>