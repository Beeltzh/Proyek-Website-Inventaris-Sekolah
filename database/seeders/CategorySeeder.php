<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'kode_kategori' => 'KAT-ELK',
                'nama_kategori' => 'Elektronik & Multimedia',
                'deskripsi' => 'Proyektor, kamera dokumentasi, speaker portabel, mikrofon, dan perangkat presentasi.',
            ],
            [
                'kode_kategori' => 'KAT-KOM',
                'nama_kategori' => 'Komputer & Jaringan',
                'deskripsi' => 'Laptop operasional, tablet pembelajaran, router wireless, switch, dan periferal TI.',
            ],
            [
                'kode_kategori' => 'KAT-LAB',
                'nama_kategori' => 'Alat Laboratorium IPA',
                'deskripsi' => 'Mikroskop binokuler, alat ukur fisika, serta kit praktikum biologi dan kimia.',
            ],
            [
                'kode_kategori' => 'KAT-OLR',
                'nama_kategori' => 'Alat Olahraga',
                'deskripsi' => 'Bola basket, bola voli, raket bulutangkis, matras senam, dan stopwatch olahraga.',
            ],
            [
                'kode_kategori' => 'KAT-KLS',
                'nama_kategori' => 'Perlengkapan Kelas & Aula',
                'deskripsi' => 'Kabel roll panjang, papan tulis portabel, pointer presentasi, dan perlengkapan acara.',
            ],
            [
                'kode_kategori' => 'KAT-SNI',
                'nama_kategori' => 'Alat Musik & Kesenian',
                'deskripsi' => 'Gitar akustik, keyboard musik, stand partitur, dan perlengkapan ekstrakurikuler seni.',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['kode_kategori' => $categoryData['kode_kategori']],
                $categoryData
            );
        }
    }
}
