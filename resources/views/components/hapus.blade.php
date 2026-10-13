@props(['action', 'judul' => 'Hapus data ini?', 'teks' => 'Data yang dihapus tidak bisa dikembalikan.'])
<form method="POST" action="{{ $action }}" data-konfirm="{{ $teks }}" data-konfirm-judul="{{ $judul }}">
    @csrf
    @method('DELETE')
    <button class="btn-danger w-full">
        <x-ic name="trash" class="h-5 w-5" /> Hapus
    </button>
</form>