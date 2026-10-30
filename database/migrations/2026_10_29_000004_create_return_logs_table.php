<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->unique()->constrained('loans')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_pengajuan')->nullable();
            $table->dateTime('tanggal_dikembalikan')->nullable();
            $table->unsignedInteger('jumlah_kembali')->default(1);
            $table->enum('kondisi_saat_kembali', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->unsignedInteger('terlambat_hari')->default(0);
            $table->text('catatan_pengembalian')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_logs');
    }
};
