<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use App\Models\ForumPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    const LABEL = ['Info', 'Pinjam Barang', 'Tanya Jawab', 'Ngobrol', 'Lainnya'];

    private function boleh($pemilikId): void
    {
        $u = auth()->user();
        abort_unless($u->isAdmin() || $u->id === $pemilikId, 403);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        return view('forum.index', [
            'posts' => ForumPost::with('user')->withCount('comments')
                ->when($q !== '', function (Builder $query) use ($q): void {
                    $query->where(function (Builder $matches) use ($q): void {
                        $matches->where('judul', 'like', "%{$q}%")
                            ->orWhere('isi', 'like', "%{$q}%")
                            ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$q}%"));
                    });
                })
                ->latest()->paginate(10)->withQueryString(),
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('forum.create', ['labels' => self::LABEL]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|in:'.implode(',', self::LABEL),
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
        ]);

        $post = ForumPost::create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('forum.show', $post)->with('sukses', 'Postingan terkirim.');
    }

    public function show(ForumPost $forum)
    {
        $forum->load([
            'user',
            'comments' => fn ($q) => $q->oldest()->orderBy('id')->with('user'),
        ]);

        return view('forum.show', ['post' => $forum]);
    }

    public function edit(ForumPost $forum)
    {
        $this->boleh($forum->user_id);

        return view('forum.create', ['post' => $forum, 'labels' => self::LABEL]);
    }

    public function update(Request $request, ForumPost $forum)
    {
        $this->boleh($forum->user_id);

        $data = $request->validate([
            'label' => 'required|in:'.implode(',', self::LABEL),
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
        ]);

        $forum->update($data);

        return redirect()->route('forum.show', $forum)->with('sukses', 'Postingan diperbarui.');
    }

    public function destroy(ForumPost $forum)
    {
        $this->boleh($forum->user_id);
        $forum->delete();

        return redirect()->route('forum.index')->with('sukses', 'Postingan dihapus.');
    }

    public function komentar(Request $request, ForumPost $forum)
    {
        $data = $request->validate(['isi' => 'required|string|max:2000']);

        $komentar = ForumComment::create($data + [
            'forum_post_id' => $forum->id,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->to(route('forum.show', $forum).'#komentar-'.$komentar->id)
            ->with('sukses', 'Komentar terkirim.');
    }

    public function hapusKomentar(ForumComment $komentar)
    {
        $this->boleh($komentar->user_id);
        $post = $komentar->forum_post_id;
        $komentar->delete();

        return redirect()
            ->to(route('forum.show', $post).'#daftar-komentar')
            ->with('sukses', 'Komentar dihapus.');
    }
}
