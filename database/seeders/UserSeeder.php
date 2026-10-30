<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // 1. Akun Admin (Pengelola Inventaris Sekolah)
            [
                'email' => 'admin@sekolah.sch.id',
                'name' => 'Drs. Hendra Wijaya (Admin Sarpras)',
                'role' => 'admin',
                'user_type' => 'Pengelola',
                'nomor_induk' => '198004122005011002',
                'kelas_jabatan' => 'Kepala Sarana & Prasarana',
                'no_telepon' => '081234567890',
                'password' => Hash::make('password123'),
            ],
            // 2. Akun User 1 (Guru)
            [
                'email' => 'guru@sekolah.sch.id',
                'name' => 'Budi Santoso, S.Pd.',
                'role' => 'user',
                'user_type' => 'Guru',
                'nomor_induk' => '198807192014021001',
                'kelas_jabatan' => 'Guru Fisika & Laboratorium',
                'no_telepon' => '081345678901',
                'password' => Hash::make('password123'),
            ],
            // 3. Akun User 2 (Siswa)
            [
                'email' => 'siswa@sekolah.sch.id',
                'name' => 'Bryant Pratama',
                'role' => 'user',
                'user_type' => 'Siswa',
                'nomor_induk' => '0084512390',
                'kelas_jabatan' => 'XII MIPA 1',
                'no_telepon' => '085712345678',
                'password' => Hash::make('password123'),
            ],
            // 4. Akun User 3 (Guru Tambahan)
            [
                'email' => 'siti@sekolah.sch.id',
                'name' => 'Siti Aminah, M.Pd.',
                'role' => 'user',
                'user_type' => 'Guru',
                'nomor_induk' => '199103252018012003',
                'kelas_jabatan' => 'Guru Informatika',
                'no_telepon' => '081398765432',
                'password' => Hash::make('password123'),
            ],
            // 5. Akun User 4 (Siswa Tambahan)
            [
                'email' => 'nadia@sekolah.sch.id',
                'name' => 'Nadia Putri Lestari',
                'role' => 'user',
                'user_type' => 'Siswa',
                'nomor_induk' => '0089123456',
                'kelas_jabatan' => 'XI IPS 2 (Ketua OSIS)',
                'no_telepon' => '085898765432',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
