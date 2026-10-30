<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReturnLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@sekolah.sch.id')->firstOrFail();

        // 4 Data Pengembalian (minimal 3 terpenuhi), 100% sesuai dengan data loans berstatus 'dikembalikan'
        $returns = [
            'PMJ-20261015-001' => [
                'received_by' => $admin->id,
                'tanggal_pengajuan' => now()->subDays(11)->setTime(9, 0),
                'tanggal_dikembalikan' => now()->subDays(11)->setTime(9, 25),
                'jumlah_kembali' => 2,
                'kondisi_saat_kembali' => 'baik',
                'terlambat_hari' => 1,
                'catatan_pengembalian' => 'Dikembalikan H+1 dalam kondisi baik karena pameran baru selesai sore hari.',
            ],
            'PMJ-20261018-001' => [
                'received_by' => $admin->id,
                'tanggal_pengajuan' => now()->subDays(10)->setTime(13, 30),
                'tanggal_dikembalikan' => now()->subDays(10)->setTime(14, 0),
                'jumlah_kembali' => 4,
                'kondisi_saat_kembali' => 'rusak_ringan',
                'terlambat_hari' => 0,
                'catatan_pengembalian' => 'Tepat waktu. Salah satu kabel probe multimeter agak longgar (rusak ringan).',
            ],
            'PMJ-20261020-001' => [
                'received_by' => $admin->id,
                'tanggal_pengajuan' => now()->subDays(8)->setTime(15, 10),
                'tanggal_dikembalikan' => now()->subDays(8)->setTime(15, 30),
                'jumlah_kembali' => 2,
                'kondisi_saat_kembali' => 'baik',
                'terlambat_hari' => 0,
                'catatan_pengembalian' => 'Kedua bola basket dikembalikan lengkap, bersih, dan kondisi tekanan udara baik.',
            ],
            'PMJ-20261022-001' => [
                'received_by' => $admin->id,
                'tanggal_pengajuan' => now()->subDays(6)->setTime(11, 45),
                'tanggal_dikembalikan' => now()->subDays(6)->setTime(12, 10),
                'jumlah_kembali' => 1,
                'kondisi_saat_kembali' => 'baik',
                'terlambat_hari' => 0,
                'catatan_pengembalian' => 'Gitar akustik dikembalikan tepat waktu lengkap bersama softcase dan kabel jack.',
            ],
        ];

        foreach ($returns as $kodePeminjaman => $returnData) {
            $loan = Loan::where('kode_peminjaman', $kodePeminjaman)
                ->where('status', 'dikembalikan')
                ->firstOrFail();

            ReturnLog::updateOrCreate(
                ['loan_id' => $loan->id],
                $returnData
            );
        }
    }
}
