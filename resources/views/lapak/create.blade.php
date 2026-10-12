<x-mading-layout title="Pasang Lapak">
    <form method="POST" action="{{ route('lapak.store') }}" enctype="multipart/form-data" class="card space-y-4">
        @csrf
        <div>
            <label class="lbl">Nama Produk / Jasa</label>
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
        <p class="text-sm text-gray-500">Pembeli akan menghubungi lewat nomor WhatsApp yang kamu daftarkan.</p>
        <button class="btn-primary w-full">💾 Tayangkan</button>
    </form>
</x-mading-layout>