<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('pengumumans', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete();
        $t->string('judul');
        $t->text('isi');
        $t->string('lampiran')->nullable();
        $t->timestamps();
    });

    Schema::create('acaras', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete();
        $t->string('judul');
        $t->text('deskripsi')->nullable();
        $t->date('tanggal');
        $t->time('jam');
        $t->string('lokasi');
        $t->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
        $t->timestamps();
    });

    Schema::create('rsvps', function (Blueprint $t) {
        $t->id();
        $t->foreignId('acara_id')->constrained('acaras')->cascadeOnDelete();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete();
        $t->enum('status', ['hadir', 'tidak_hadir']);
        $t->timestamps();
        $t->unique(['acara_id', 'user_id']);
    });

    Schema::create('forum_posts', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete();
        $t->string('label');
        $t->string('judul');
        $t->text('isi');
        $t->timestamps();
    });

    Schema::create('forum_comments', function (Blueprint $t) {
        $t->id();
        $t->foreignId('forum_post_id')->constrained('forum_posts')->cascadeOnDelete();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete();
        $t->text('isi');
        $t->timestamps();
    });

    Schema::create('lapaks', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete();
        $t->string('nama');
        $t->unsignedBigInteger('harga');
        $t->text('deskripsi');
        $t->string('foto')->nullable();
        $t->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('lapaks');
    Schema::dropIfExists('forum_comments');
    Schema::dropIfExists('forum_posts');
    Schema::dropIfExists('rsvps');
    Schema::dropIfExists('acaras');
    Schema::dropIfExists('pengumumans');
}
};
