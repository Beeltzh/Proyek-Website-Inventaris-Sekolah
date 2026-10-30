<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peminjaman', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
            $table->date('tanggal_pinjam');
            $table->date('rencana_kembali')->index();
            $table->text('keperluan');
            $table->enum('status', [
                'menunggu_persetujuan',
                'dipinjam',
                'menunggu_pengembalian',
                'dikembalikan',
                'ditolak',
            ])->default('menunggu_persetujuan')->index();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
