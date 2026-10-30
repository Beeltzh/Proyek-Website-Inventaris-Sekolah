<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        if (ActivityLog::count() > 0) {
            return;
        }

        $admin = User::where('email', 'admin@sekolah.sch.id')->firstOrFail();
        $guruBudi = User::where('email', 'guru@sekolah.sch.id')->firstOrFail();
        $siswaBryant = User::where('email', 'siswa@sekolah.sch.id')->firstOrFail();
        $siswaNadia = User::where('email', 'nadia@sekolah.sch.id')->firstOrFail();

        $catLab = Category::where('kode_kategori', 'KAT-LAB')->first();
        $itemEpson = Item::where('kode_barang', 'INV-ELK-001')->first();
        $itemSanwa = Item::where('kode_barang', 'INV-LAB-002')->first();
        $loanReturned = Loan::where('kode_peminjaman', 'PMJ-20261020-001')->first();
        $returnLog = $loanReturned ? ReturnLog::where('loan_id', $loanReturned->id)->first() : null;
        $loanRejected = Loan::where('kode_peminjaman', 'PMJ-20261026-001')->first();
        $loanApproved = Loan::where('kode_peminjaman', 'PMJ-20261028-001')->first();
        $loanPending = Loan::where('kode_peminjaman', 'PMJ-20261029-001')->first();
        $loanReturnReq = Loan::where('kode_peminjaman', 'PMJ-20261027-001')->first();

        $logs = [
            [
                'user_id' => $admin->id,
                'nama_user' => $admin->name,
                'role_user' => 'admin',
                'action' => 'LOGIN',
                'module' => 'Auth',
                'subject_type' => User::class,
                'subject_id' => $admin->id,
                'description' => "Pengguna {$admin->name} (Pengelola) berhasil login ke dalam sistem.",
                'properties' => ['email' => $admin->email, 'role' => 'admin'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/135.0',
                'created_at' => now()->subDays(9)->setTime(8, 0),
            ],
            [
                'user_id' => $admin->id,
                'nama_user' => $admin->name,
                'role_user' => 'admin',
                'action' => 'CREATE',
                'module' => 'Categories',
                'subject_type' => Category::class,
                'subject_id' => $catLab?->id,
                'description' => 'Menambahkan kategori barang baru: Alat Laboratorium IPA (KAT-LAB).',
                'properties' => ['new' => ['kode_kategori' => 'KAT-LAB', 'nama_kategori' => 'Alat Laboratorium IPA']],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/135.0',
                'created_at' => now()->subDays(9)->setTime(8, 15),
            ],
            [
                'user_id' => $admin->id,
                'nama_user' => $admin->name,
                'role_user' => 'admin',
                'action' => 'CREATE',
                'module' => 'Items',
                'subject_type' => Item::class,
                'subject_id' => $itemEpson?->id,
                'description' => 'Menambahkan barang inventaris baru: Proyektor LCD Epson EB-X500 (INV-ELK-001) sebanyak 8 Unit.',
                'properties' => ['new' => ['kode_barang' => 'INV-ELK-001', 'nama_barang' => 'Proyektor LCD Epson EB-X500', 'stok_total' => 8, 'lokasi' => 'Ruang Sarpras Lt. 1']],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/135.0',
                'created_at' => now()->subDays(9)->setTime(8, 30),
            ],
            [
                'user_id' => $admin->id,
                'nama_user' => $admin->name,
                'role_user' => 'admin',
                'action' => 'UPDATE',
                'module' => 'Items',
                'subject_type' => Item::class,
                'subject_id' => $itemSanwa?->id,
                'description' => 'Memperbarui data barang inventaris: Multimeter Digital Sanwa CD800a (INV-LAB-002).',
                'properties' => [
                    'old' => ['kondisi' => 'baik', 'stok_total' => 15],
                    'new' => ['kondisi' => 'rusak_ringan', 'stok_total' => 15],
                ],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/135.0',
                'created_at' => now()->subDays(8)->setTime(10, 0),
            ],
            [
                'user_id' => $admin->id,
                'nama_user' => $admin->name,
                'role_user' => 'admin',
                'action' => 'RETURN_CONFIRM',
                'module' => 'ReturnLogs',
                'subject_type' => ReturnLog::class,
                'subject_id' => $returnLog?->id,
                'description' => "Admin memverifikasi pengembalian barang Bola Basket Molten GG7X Official (PMJ-20261020-001) dari {$siswaBryant->name} dengan kondisi: Baik.",
                'properties' => ['kode_peminjaman' => 'PMJ-20261020-001', 'kondisi_saat_kembali' => 'baik', 'terlambat_hari' => 0],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/135.0',
                'created_at' => now()->subDays(8)->setTime(15, 30),
            ],
            [
                'user_id' => $admin->id,
                'nama_user' => $admin->name,
                'role_user' => 'admin',
                'action' => 'LOAN_REJECT',
                'module' => 'Loans',
                'subject_type' => Loan::class,
                'subject_id' => $loanRejected?->id,
                'description' => "Admin menolak pengajuan peminjaman PMJ-20261026-001 oleh {$siswaBryant->name}. Alasan: Seluruh unit speaker sedang terjadwal untuk upacara.",
                'properties' => ['old_status' => 'menunggu_persetujuan', 'new_status' => 'ditolak'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/135.0',
                'created_at' => now()->subDays(3)->setTime(11, 20),
            ],
            [
                'user_id' => $admin->id,
                'nama_user' => $admin->name,
                'role_user' => 'admin',
                'action' => 'LOAN_APPROVE',
                'module' => 'Loans',
                'subject_type' => Loan::class,
                'subject_id' => $loanApproved?->id,
                'description' => "Admin menyetujui peminjaman PMJ-20261028-001 (2 Unit Proyektor LCD Epson EB-X500) oleh {$guruBudi->name}.",
                'properties' => ['old_status' => 'menunggu_persetujuan', 'new_status' => 'dipinjam', 'stok_sebelum' => 8, 'stok_sesudah' => 6],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/135.0',
                'created_at' => now()->subDay()->setTime(7, 45),
            ],
            [
                'user_id' => $siswaNadia->id,
                'nama_user' => $siswaNadia->name,
                'role_user' => 'user',
                'action' => 'RETURN_REQUEST',
                'module' => 'Loans',
                'subject_type' => Loan::class,
                'subject_id' => $loanReturnReq?->id,
                'description' => 'Pengajuan pengembalian barang untuk transaksi PMJ-20261027-001 (Kamera Mirrorless Canon EOS M50).',
                'properties' => ['kode_peminjaman' => 'PMJ-20261027-001', 'old_status' => 'dipinjam', 'new_status' => 'menunggu_pengembalian'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/135.0',
                'created_at' => now()->subHours(3),
            ],
            [
                'user_id' => $siswaBryant->id,
                'nama_user' => $siswaBryant->name,
                'role_user' => 'user',
                'action' => 'LOAN_REQUEST',
                'module' => 'Loans',
                'subject_type' => Loan::class,
                'subject_id' => $loanPending?->id,
                'description' => "Pengajuan peminjaman baru PMJ-20261029-001 oleh {$siswaBryant->name}: 3 Unit Mikroskop Binokuler Elektrik Olympus.",
                'properties' => ['kode_peminjaman' => 'PMJ-20261029-001', 'jumlah' => 3, 'status' => 'menunggu_persetujuan'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/135.0',
                'created_at' => now()->subHour(),
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create($log);
        }
    }
}
