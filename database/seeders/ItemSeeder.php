<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::pluck('id', 'kode_kategori');

        // 12 Data Barang tersebar di 6 Kategori (minimal 10 barang terpenuhi)
        $items = [
            // Kategori 1: Elektronik & Multimedia (KAT-ELK)
            [
                'category_id' => $cat['KAT-ELK'],
                'kode_barang' => 'INV-ELK-001',
                'nama_barang' => 'Proyektor LCD Epson EB-X500',
                'merk' => 'Epson',
                'lokasi' => 'Ruang Sarpras Lt. 1',
                'stok_total' => 8,
                'stok_tersedia' => 6, // 2 unit sedang dipinjam (PMJ-20261028-001)
                'satuan' => 'Unit',
                'kondisi' => 'baik',
                'deskripsi' => 'Proyektor XGA 3600 Lumens lengkap dengan tas, kabel HDMI 3 meter, dan kabel power.',
            ],
            [
                'category_id' => $cat['KAT-ELK'],
                'kode_barang' => 'INV-ELK-002',
                'nama_barang' => 'Kamera Mirrorless Canon EOS M50',
                'merk' => 'Canon',
                'lokasi' => 'Lemari Multimedia TU',
                'stok_total' => 3,
                'stok_tersedia' => 2, // 1 set menunggu verifikasi kembali (PMJ-20261027-001)
                'satuan' => 'Set',
                'kondisi' => 'baik',
                'deskripsi' => 'Kamera dokumentasi sekolah lengkap dengan lensa kit 15-45mm, baterai cadangan, dan SD Card 64GB.',
            ],
            [
                'category_id' => $cat['KAT-ELK'],
                'kode_barang' => 'INV-ELK-003',
                'nama_barang' => 'Speaker Portable Wireless Baretone 15 Inch',
                'merk' => 'Baretone',
                'lokasi' => 'Gudang Aula Utama',
                'stok_total' => 4,
                'stok_tersedia' => 4,
                'satuan' => 'Unit',
                'kondisi' => 'baik',
                'deskripsi' => 'Speaker aktif portabel dengan 2 mikrofon wireless untuk upacara, rapat, atau kegiatan lapangan.',
            ],
            [
                'category_id' => $cat['KAT-ELK'],
                'kode_barang' => 'INV-ELK-004',
                'nama_barang' => 'Proyektor BenQ MS550 (Unit Cadangan)',
                'merk' => 'BenQ',
                'lokasi' => 'Gudang Perbaikan',
                'stok_total' => 2,
                'stok_tersedia' => 2,
                'satuan' => 'Unit',
                'kondisi' => 'rusak_berat',
                'deskripsi' => 'Lampu proyektor mati/redup, sedang menunggu penggantian komponen.',
            ],

            // Kategori 2: Komputer & Jaringan (KAT-KOM)
            [
                'category_id' => $cat['KAT-KOM'],
                'kode_barang' => 'INV-KOM-001',
                'nama_barang' => 'Laptop ASUS ExpertBook Core i5',
                'merk' => 'ASUS',
                'lokasi' => 'Lab Komputer 1',
                'stok_total' => 10,
                'stok_tersedia' => 9, // 1 unit sedang dipinjam (PMJ-20261024-001)
                'satuan' => 'Unit',
                'kondisi' => 'baik',
                'deskripsi' => 'Laptop operasional guru dan siswa untuk olimpiade, ANBK, dan presentasi kelas.',
            ],
            [
                'category_id' => $cat['KAT-KOM'],
                'kode_barang' => 'INV-KOM-002',
                'nama_barang' => 'Tablet Pembelajaran Samsung Galaxy Tab A9',
                'merk' => 'Samsung',
                'lokasi' => 'Lab Komputer 2',
                'stok_total' => 15,
                'stok_tersedia' => 15,
                'satuan' => 'Unit',
                'kondisi' => 'baik',
                'deskripsi' => 'Tablet literasi digital perpustakaan dan asesmen kelas.',
            ],

            // Kategori 3: Alat Laboratorium IPA (KAT-LAB)
            [
                'category_id' => $cat['KAT-LAB'],
                'kode_barang' => 'INV-LAB-001',
                'nama_barang' => 'Mikroskop Binokuler Elektrik Olympus',
                'merk' => 'Olympus',
                'lokasi' => 'Laboratorium Biologi',
                'stok_total' => 12,
                'stok_tersedia' => 12,
                'satuan' => 'Unit',
                'kondisi' => 'baik',
                'deskripsi' => 'Mikroskop binokuler pembesaran hingga 1000x untuk praktikum pengamatan sel.',
            ],
            [
                'category_id' => $cat['KAT-LAB'],
                'kode_barang' => 'INV-LAB-002',
                'nama_barang' => 'Multimeter Digital Sanwa CD800a',
                'merk' => 'Sanwa',
                'lokasi' => 'Laboratorium Fisika',
                'stok_total' => 15,
                'stok_tersedia' => 15,
                'satuan' => 'Buah',
                'kondisi' => 'rusak_ringan',
                'deskripsi' => 'Alat ukur tegangan, arus, dan hambatan listrik untuk praktikum rangkaian listrik.',
            ],

            // Kategori 4: Alat Olahraga (KAT-OLR)
            [
                'category_id' => $cat['KAT-OLR'],
                'kode_barang' => 'INV-OLR-001',
                'nama_barang' => 'Bola Basket Molten GG7X Official',
                'merk' => 'Molten',
                'lokasi' => 'Gudang Olahraga GOR',
                'stok_total' => 10,
                'stok_tersedia' => 10,
                'satuan' => 'Buah',
                'kondisi' => 'baik',
                'deskripsi' => 'Bola basket ukuran 7 standar pembelajaran PJOK dan ekstrakurikuler.',
            ],
            [
                'category_id' => $cat['KAT-OLR'],
                'kode_barang' => 'INV-OLR-002',
                'nama_barang' => 'Bola Voli Mikasa V200W',
                'merk' => 'Mikasa',
                'lokasi' => 'Gudang Olahraga GOR',
                'stok_total' => 8,
                'stok_tersedia' => 8,
                'satuan' => 'Buah',
                'kondisi' => 'baik',
                'deskripsi' => 'Bola voli standar latihan dan pertandingan antar kelas.',
            ],

            // Kategori 5: Perlengkapan Kelas & Aula (KAT-KLS)
            [
                'category_id' => $cat['KAT-KLS'],
                'kode_barang' => 'INV-KLS-001',
                'nama_barang' => 'Kabel Roll Gulung Uticon 15 Meter',
                'merk' => 'Uticon',
                'lokasi' => 'Ruang Sarpras Lt. 1',
                'stok_total' => 12,
                'stok_tersedia' => 12,
                'satuan' => 'Buah',
                'kondisi' => 'baik',
                'deskripsi' => 'Stop kontak kabel gulung 4 lubang panjang 15 meter.',
            ],

            // Kategori 6: Alat Musik & Kesenian (KAT-SNI)
            [
                'category_id' => $cat['KAT-SNI'],
                'kode_barang' => 'INV-SNI-001',
                'nama_barang' => 'Gitar Akustik Elektrik Yamaha APX600',
                'merk' => 'Yamaha',
                'lokasi' => 'Ruang Seni Musik',
                'stok_total' => 4,
                'stok_tersedia' => 4,
                'satuan' => 'Unit',
                'kondisi' => 'baik',
                'deskripsi' => 'Gitar akustik elektrik lengkap dengan softcase untuk praktek seni budaya.',
            ],
        ];

        foreach ($items as $itemData) {
            Item::updateOrCreate(
                ['kode_barang' => $itemData['kode_barang']],
                $itemData
            );
        }
    }
}
