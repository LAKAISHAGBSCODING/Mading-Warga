@php
    $isEdit = isset($lapak) && $lapak->exists;
@endphp
<x-mading-layout :title="$isEdit ? 'Ubah lapak' : 'Pasang lapak'">
    <form method="POST" action="{{ $isEdit ? route('lapak.update', $lapak) : route('lapak.store') }}" enctype="multipart/form-data" class="card space-y-5">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif
        <div>
            <label class="lbl">Nama produk atau jasa</label>
            <input class="field" name="nama" value="{{ old('nama', $lapak->nama ?? '') }}" required>
        </div>
        <div>
            <label class="lbl">Harga (Rp, angka saja)</label>
            <input type="number" inputmode="numeric" class="field" name="harga" value="{{ old('harga', $lapak->harga ?? '') }}" required>
        </div>
        <div>
            <label class="lbl">Deskripsi</label>
            <textarea class="field" name="deskripsi" rows="5" required>{{ old('deskripsi', $lapak->deskripsi ?? '') }}</textarea>
        </div>
        <div>
            <label class="lbl">Foto Produk/Jasa</label>
            @if($isEdit && !empty($lapak->foto))
                <div class="mb-3 flex items-center gap-4 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <img src="{{ asset('storage/' . $lapak->foto) }}" alt="{{ $lapak->nama }}" class="h-16 w-16 object-cover rounded-lg">
                    <label class="flex items-center gap-2 text-sm text-red-600 font-semibold cursor-pointer">
                        <input type="checkbox" name="hapus_foto" value="1" class="rounded text-red-600 focus:ring-red-500">
                        Hapus foto saat ini
                    </label>
                </div>
            @endif
            <input type="file" name="foto" accept="image/*" class="field">
            <span class="meta mt-1 block">Format gambar: JPG, PNG, WEBP (maks. 5MB)</span>
        </div>
        <p class="rounded-xl bg-blue-50 p-3 text-sm text-blue-800">Pembeli akan menghubungi lewat nomor WhatsApp yang terdaftar pada akun kamu.</p>
        <button class="btn-primary w-full"><x-ic name="save" class="h-5 w-5" /> {{ $isEdit ? 'Simpan Perubahan' : 'Tayangkan Lapak' }}</button>
        <a href="{{ route('lapak.index') }}" class="btn-outline w-full">Batal</a>
    </form>
</x-mading-layout>