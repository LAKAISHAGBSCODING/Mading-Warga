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
    Schema::table('users', function (Blueprint $table) {
        $table->string('no_rumah')->default('-');
        $table->string('no_wa')->default('-');
        $table->enum('role', ['admin', 'warga'])->default('warga');
        $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])->default('pending');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['no_rumah', 'no_wa', 'role', 'status_verifikasi']);
    });
}
};
