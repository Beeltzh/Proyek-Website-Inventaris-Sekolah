<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@sekolah.sch.id')->firstOrFail();
        $guruBudi = User::where('email', 'guru@sekolah.sch.id')->firstOrFail();
        $siswaBryant = User::where('email', 'siswa@sekolah.sch.id')->firstOrFail();
        $guruSiti = User::where('email', 'siti@sekolah.sch.id')->firstOrFail();
        $siswaNadia = User::where('email', 'nadia@sekolah.sch.id')->firstOrFail();

        $items = Item::pluck('id', 'kode_barang');

        // 9 Data Peminjaman (minimal 5 terpenuhi) dengan variasi status lengkap
        $loans = [
            // 1. Status: dikembalikan (oleh Guru Budi)
            [
                'kode_peminjaman' => 'PMJ-20261015-001',
                'user_id' => $guruBudi->id,
                'item_id' => $items['INV-KLS-001'],
                'approved_by' => $admin->id,
                'jumlah' => 2,
                'tanggal_pinjam' => now()->subDays(14)->toDateString(),
                'rencana_kembali' => now()->subDays(12)->toDateString(),
                'keperluan' => 'Sambungan daya listrik untuk pameran karya ilmiah siswa di aula sekolah.',
                'status' => 'dikembalikan',
                'catatan_admin' => 'Disetujui untuk kegiatan pameran sekolah.',
            ],
            // 2. Status: dikembalikan (oleh Guru Budi)
            [
                'kode_peminjaman' => 'PMJ-20261018-001',
                'user_id' => $guruBudi->id,
                'item_id' => $items['INV-LAB-002'],
                'approved_by' => $admin->id,
                'jumlah' => 4,
                'tanggal_pinjam' => now()->subDays(11)->toDateString(),
                'rencana_kembali' => now()->subDays(10)->toDateString(),
                'keperluan' => 'Praktikum pengukuran arus dan tegangan listrik searah (DC) Kelas XII MIPA.',
                'status' => 'dikembalikan',
                'catatan_admin' => 'Disetujui, harap cek kondisi baterai multimeter.',
            ],
            // 3. Status: dikembalikan (oleh Siswa Bryant)
            [
                'kode_peminjaman' => 'PMJ-20261020-001',
                'user_id' => $siswaBryant->id,
                'item_id' => $items['INV-OLR-001'],
                'approved_by' => $admin->id,
                'jumlah' => 2,
                'tanggal_pinjam' => now()->subDays(9)->toDateString(),
                'rencana_kembali' => now()->subDays(8)->toDateString(),
                'keperluan' => 'Latihan tanding basket persahabatan antar kelas.',
                'status' => 'dikembalikan',
                'catatan_admin' => 'Disetujui untuk kegiatan olahraga sore.',
            ],
            // 4. Status: dikembalikan (oleh Siswa Nadia)
            [
                'kode_peminjaman' => 'PMJ-20261022-001',
                'user_id' => $siswaNadia->id,
                'item_id' => $items['INV-SNI-001'],
                'approved_by' => $admin->id,
                'jumlah' => 1,
                'tanggal_pinjam' => now()->subDays(7)->toDateString(),
                'rencana_kembali' => now()->subDays(6)->toDateString(),
                'keperluan' => 'Latihan penampilan akustik pembukaan acara Bulan Bahasa OSIS.',
                'status' => 'dikembalikan',
                'catatan_admin' => 'Disetujui, harap simpan kembali beserta softcase.',
            ],
            // 5. Status: dipinjam & melewati batas kembali (oleh Guru Siti)
            [
                'kode_peminjaman' => 'PMJ-20261024-001',
                'user_id' => $guruSiti->id,
                'item_id' => $items['INV-KOM-001'],
                'approved_by' => $admin->id,
                'jumlah' => 1,
                'tanggal_pinjam' => now()->subDays(5)->toDateString(),
                'rencana_kembali' => now()->subDays(1)->toDateString(),
                'keperluan' => 'Pelatihan pemrograman dan pembinaan olimpiade informatika.',
                'status' => 'dipinjam',
                'catatan_admin' => 'Disetujui untuk persiapan lomba.',
            ],
            // 6. Status: ditolak (oleh Siswa Bryant)
            [
                'kode_peminjaman' => 'PMJ-20261026-001',
                'user_id' => $siswaBryant->id,
                'item_id' => $items['INV-ELK-003'],
                'approved_by' => $admin->id,
                'jumlah' => 2,
                'tanggal_pinjam' => now()->subDays(3)->toDateString(),
                'rencana_kembali' => now()->subDays(2)->toDateString(),
                'keperluan' => 'Kegiatan latihan mandiri di lapangan sekolah.',
                'status' => 'ditolak',
                'catatan_admin' => 'Ditolak karena seluruh unit speaker sedang terjadwal untuk upacara dan rapat komite sekolah.',
            ],
            // 7. Status: menunggu_pengembalian (oleh Siswa Nadia)
            [
                'kode_peminjaman' => 'PMJ-20261027-001',
                'user_id' => $siswaNadia->id,
                'item_id' => $items['INV-ELK-002'],
                'approved_by' => $admin->id,
                'jumlah' => 1,
                'tanggal_pinjam' => now()->subDays(2)->toDateString(),
                'rencana_kembali' => now()->toDateString(),
                'keperluan' => 'Dokumentasi kegiatan rapat kerja OSIS dan lomba Bulan Bahasa.',
                'status' => 'menunggu_pengembalian',
                'catatan_admin' => 'Pastikan baterai diisi ulang dan SD card dikosongkan setelah selesai.',
            ],
            // 8. Status: dipinjam aktif (oleh Guru Budi)
            [
                'kode_peminjaman' => 'PMJ-20261028-001',
                'user_id' => $guruBudi->id,
                'item_id' => $items['INV-ELK-001'],
                'approved_by' => $admin->id,
                'jumlah' => 2,
                'tanggal_pinjam' => now()->subDay()->toDateString(),
                'rencana_kembali' => now()->addDays(1)->toDateString(),
                'keperluan' => 'Presentasi materi Fisika Modern di kelas XII MIPA 1 dan XII MIPA 2.',
                'status' => 'dipinjam',
                'catatan_admin' => 'Disetujui, harap kembalikan bersama remot dan kabel HDMI.',
            ],
            // 9. Status: menunggu_persetujuan (oleh Siswa Bryant)
            [
                'kode_peminjaman' => 'PMJ-20261029-001',
                'user_id' => $siswaBryant->id,
                'item_id' => $items['INV-LAB-001'],
                'approved_by' => null,
                'jumlah' => 3,
                'tanggal_pinjam' => now()->toDateString(),
                'rencana_kembali' => now()->addDays(2)->toDateString(),
                'keperluan' => 'Praktikum Biologi pengamatan sel jaringan tumbuhan kelas XII MIPA 1.',
                'status' => 'menunggu_persetujuan',
                'catatan_admin' => null,
            ],
        ];

        foreach ($loans as $loanData) {
            Loan::updateOrCreate(
                ['kode_peminjaman' => $loanData['kode_peminjaman']],
                $loanData
            );
        }
    }
}
