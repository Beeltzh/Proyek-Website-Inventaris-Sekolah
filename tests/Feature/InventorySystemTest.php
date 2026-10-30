<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_breeze_authentication_and_registration_record_activity_logs(): void
    {
        // Guest views render cleanly
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();

        // Register new user
        $this->post(route('register'), [
            'name' => 'Rina Kartika',
            'email' => 'rina@sekolah.sch.id',
            'user_type' => 'Siswa',
            'nomor_induk' => '0091234567',
            'kelas_jabatan' => 'XI MIPA 2',
            'no_telepon' => '081298765432',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $registeredUser = User::where('email', 'rina@sekolah.sch.id')->firstOrFail();
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $registeredUser->id,
            'action' => 'REGISTER',
            'module' => 'Auth',
        ]);

        // Profile view & update
        $this->get(route('profile.edit'))->assertOk();
        $this->patch(route('profile.update'), [
            'name' => 'Rina Kartika Putri',
            'email' => 'rina@sekolah.sch.id',
            'nomor_induk' => '0091234567',
            'kelas_jabatan' => 'XII MIPA 2',
            'no_telepon' => '081298765432',
        ])->assertRedirect();

        $this->assertEquals('Rina Kartika Putri', $registeredUser->fresh()->name);

        // Logout
        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $registeredUser->id,
            'action' => 'LOGOUT',
            'module' => 'Auth',
        ]);

        // Failed login
        $this->post(route('login'), [
            'email' => 'rina@sekolah.sch.id',
            'password' => 'salah-sandi',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'LOGIN_FAILED',
            'module' => 'Auth',
        ]);

        // Successful login
        $this->post(route('login'), [
            'email' => 'rina@sekolah.sch.id',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $registeredUser->id,
            'action' => 'LOGIN',
            'module' => 'Auth',
        ]);
    }

    public function test_admin_crud_uses_soft_deletes_and_audit_trail(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'user_type' => 'Pengelola',
        ]);

        // 1. Admin creates category
        $this->actingAs($admin)
            ->post(route('categories.store'), [
                'kode_kategori' => 'kat-elk',
                'nama_kategori' => 'Elektronik',
                'deskripsi' => 'Alat elektronik sekolah',
            ])
            ->assertRedirect(route('categories.index'));

        $category = Category::where('kode_kategori', 'KAT-ELK')->firstOrFail();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'CREATE',
            'module' => 'Categories',
            'subject_id' => $category->id,
        ]);

        // 2. Admin creates item
        $this->actingAs($admin)
            ->post(route('items.store'), [
                'category_id' => $category->id,
                'kode_barang' => 'inv-001',
                'nama_barang' => 'Proyektor Epson',
                'merk' => 'Epson',
                'lokasi' => 'Ruang TU',
                'stok_total' => 5,
                'satuan' => 'Unit',
                'kondisi' => 'baik',
                'deskripsi' => 'Proyektor kelas',
            ])
            ->assertRedirect(route('items.index'));

        $item = Item::where('kode_barang', 'INV-001')->firstOrFail();
        $this->assertEquals(5, $item->stok_total);
        $this->assertEquals(5, $item->stok_tersedia);

        // 3. Admin updates item
        $this->actingAs($admin)
            ->put(route('items.update', $item), [
                'category_id' => $category->id,
                'kode_barang' => 'INV-001',
                'nama_barang' => 'Proyektor Epson EB-X500',
                'merk' => 'Epson',
                'lokasi' => 'Ruang Multimedia',
                'stok_total' => 6,
                'satuan' => 'Unit',
                'kondisi' => 'baik',
                'deskripsi' => 'Diperbarui',
            ])
            ->assertRedirect(route('items.index'));

        // 4. Admin soft-deletes item
        $this->actingAs($admin)
            ->delete(route('items.destroy', $item))
            ->assertRedirect(route('items.index'));

        $this->assertSoftDeleted('items', ['id' => $item->id]);

        // 5. Admin soft-deletes category (allowed now that active items = 0)
        $this->actingAs($admin)
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertSoftDeleted('categories', ['id' => $category->id]);

        // 6. Admin creates & soft-deletes a user account
        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Pak Dedi',
                'email' => 'dedi@sekolah.sch.id',
                'role' => 'user',
                'user_type' => 'Staf',
                'nomor_induk' => '198801012015011002',
                'kelas_jabatan' => 'Staf Tata Usaha',
                'password' => 'password123',
            ])
            ->assertRedirect(route('users.index'));

        $managedUser = User::where('email', 'dedi@sekolah.sch.id')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $managedUser))
            ->assertRedirect(route('users.index'));

        $this->assertSoftDeleted('users', ['id' => $managedUser->id]);

        // 7. Admin soft-deletes a completed/rejected loan
        $loan = Loan::create([
            'kode_peminjaman' => 'PMJ-TEST-DEL',
            'user_id' => $admin->id,
            'item_id' => $item->id,
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'rencana_kembali' => now()->addDay()->toDateString(),
            'keperluan' => 'Peminjaman uji coba',
            'status' => 'ditolak',
        ]);

        $this->actingAs($admin)
            ->delete(route('loans.destroy', $loan))
            ->assertRedirect();

        $this->assertSoftDeleted('loans', ['id' => $loan->id]);
    }

    public function test_form_request_validation_enforces_required_fields(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'user_type' => 'Pengelola',
        ]);

        $this->actingAs($admin)
            ->post(route('categories.store'), [])
            ->assertSessionHasErrors(['kode_kategori', 'nama_kategori']);

        $this->actingAs($admin)
            ->post(route('items.store'), [])
            ->assertSessionHasErrors(['category_id', 'kode_barang', 'nama_barang', 'lokasi', 'stok_total', 'satuan', 'kondisi']);

        $this->actingAs($admin)
            ->post(route('loans.store'), [])
            ->assertSessionHasErrors(['item_id', 'jumlah', 'tanggal_pinjam', 'rencana_kembali', 'keperluan']);
    }

    public function test_user_cannot_access_admin_routes(): void
    {
        $siswa = User::factory()->create([
            'role' => 'user',
            'user_type' => 'Siswa',
        ]);

        $this->actingAs($siswa)->get(route('categories.index'))->assertStatus(403);
        $this->actingAs($siswa)->get(route('reports.index'))->assertStatus(403);
        $this->actingAs($siswa)->get(route('reports.print'))->assertStatus(403);
        $this->actingAs($siswa)->get(route('reports.export-csv'))->assertStatus(403);
        $this->actingAs($siswa)->get(route('users.index'))->assertStatus(403);
        $this->actingAs($siswa)->get(route('activity-logs.index'))->assertStatus(403);
    }

    public function test_complete_loans_and_return_logs_workflow(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'user_type' => 'Pengelola',
        ]);

        $guru = User::factory()->create([
            'role' => 'user',
            'user_type' => 'Guru',
            'kelas_jabatan' => 'Guru Biologi',
        ]);

        $category = Category::create([
            'kode_kategori' => 'KAT-LAB',
            'nama_kategori' => 'Laboratorium',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'kode_barang' => 'INV-LAB-01',
            'nama_barang' => 'Mikroskop Binokuler',
            'lokasi' => 'Lab Biologi',
            'stok_total' => 4,
            'stok_tersedia' => 4,
            'satuan' => 'Unit',
            'kondisi' => 'baik',
        ]);

        // 1. User (Guru) mengajukan peminjaman 2 unit -> masuk ke tabel loans
        $this->actingAs($guru)
            ->post(route('loans.store'), [
                'item_id' => $item->id,
                'jumlah' => 2,
                'tanggal_pinjam' => now()->toDateString(),
                'rencana_kembali' => now()->addDays(2)->toDateString(),
                'keperluan' => 'Praktikum Biologi Kelas XI',
            ])
            ->assertRedirect(route('loans.index'));

        $loan = Loan::firstOrFail();
        $this->assertEquals('menunggu_persetujuan', $loan->status);
        $this->assertEquals(4, $item->fresh()->stok_tersedia);

        // 2. Admin menyetujui peminjaman
        $this->actingAs($admin)
            ->patch(route('loans.approve', $loan), [
                'catatan_admin' => 'Silakan diambil di Lab Biologi',
            ])
            ->assertRedirect();

        $this->assertEquals('dipinjam', $loan->fresh()->status);
        $this->assertEquals(2, $item->fresh()->stok_tersedia);

        // 3. User mengajukan pengembalian barang
        $this->actingAs($guru)
            ->patch(route('loans.request-return', $loan))
            ->assertRedirect();

        $this->assertEquals('menunggu_pengembalian', $loan->fresh()->status);

        // 4. Admin memverifikasi pengembalian barang -> tersimpan lengkap di return_logs
        $this->actingAs($admin)
            ->patch(route('loans.confirm-return', $loan), [
                'kondisi_saat_kembali' => 'baik',
                'catatan_pengembalian' => 'Dikembalikan lengkap beserta penutup lensa',
            ])
            ->assertRedirect();

        $loan->refresh();
        $this->assertEquals('dikembalikan', $loan->status);
        $this->assertEquals(4, $item->fresh()->stok_tersedia);

        $returnLog = ReturnLog::where('loan_id', $loan->id)->firstOrFail();
        $this->assertNotNull($returnLog->tanggal_dikembalikan);
        $this->assertEquals($admin->id, $returnLog->received_by);
        $this->assertEquals('baik', $returnLog->kondisi_saat_kembali);
        $this->assertEquals('Dikembalikan lengkap beserta penutup lensa', $returnLog->catatan_pengembalian);

        // 5. Admin mencatat peminjaman langsung (status langsung dipinjam & stok auto berkurang)
        $this->actingAs($admin)
            ->post(route('loans.store'), [
                'user_id' => $guru->id,
                'item_id' => $item->id,
                'jumlah' => 1,
                'tanggal_pinjam' => now()->toDateString(),
                'rencana_kembali' => now()->addDay()->toDateString(),
                'keperluan' => 'Peminjaman langsung oleh Admin',
            ])
            ->assertRedirect(route('loans.index'));

        $this->assertEquals(3, $item->fresh()->stok_tersedia);

        // 6. User mengajukan peminjaman lain & Admin menolaknya
        $this->actingAs($guru)
            ->post(route('loans.store'), [
                'item_id' => $item->id,
                'jumlah' => 1,
                'tanggal_pinjam' => now()->toDateString(),
                'rencana_kembali' => now()->addDay()->toDateString(),
                'keperluan' => 'Peminjaman kedua',
            ])
            ->assertRedirect(route('loans.index'));

        $secondLoan = Loan::where('keperluan', 'Peminjaman kedua')->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('loans.reject', $secondLoan), [
                'catatan_admin' => 'Barang diprioritaskan untuk acara sekolah',
            ])
            ->assertRedirect();

        $this->assertEquals('ditolak', $secondLoan->fresh()->status);
        $this->assertEquals(3, $item->fresh()->stok_tersedia);

        // 7. Pastikan semua halaman utama, detail, laporan, cetak, dan ekspor berjalan tanpa error
        $this->actingAs($admin)->get(route('dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('categories.index', ['search' => 'Lab']))->assertOk();
        $this->actingAs($admin)->get(route('items.index', ['search' => 'Mikroskop']))->assertOk();
        $this->actingAs($admin)->get(route('items.show', $item))->assertOk();
        $this->actingAs($admin)->get(route('loans.index'))->assertOk();
        $this->actingAs($admin)->get(route('return-logs.index'))->assertOk();
        $this->actingAs($admin)->get(route('activity-logs.index'))->assertOk();
        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($admin)->get(route('reports.index', ['type' => 'loans']))->assertOk();
        $this->actingAs($admin)->get(route('reports.index', ['type' => 'items']))->assertOk();
        $this->actingAs($admin)->get(route('reports.print', ['type' => 'loans']))->assertOk();
        $this->actingAs($admin)->get(route('reports.print', ['type' => 'items']))->assertOk();
        $this->actingAs($admin)->get(route('reports.export-csv', ['type' => 'loans']))->assertOk();
        $this->actingAs($admin)->get(route('reports.export-csv', ['type' => 'items']))->assertOk();

        $this->actingAs($guru)->get(route('dashboard'))->assertOk();
        $this->actingAs($guru)->get(route('items.index'))->assertOk();
        $this->actingAs($guru)->get(route('items.show', $item))->assertOk();
        $this->actingAs($guru)->get(route('loans.index'))->assertOk();
        $this->actingAs($guru)->get(route('return-logs.index'))->assertOk();

        $this->assertGreaterThanOrEqual(4, ActivityLog::count());
    }

    public function test_indonesian_number_and_date_formatting_helpers(): void
    {
        $this->assertSame('1.250.000', format_number(1250000));
        $this->assertSame('0', format_number(null));
        $this->assertSame('1 Feb 2026', format_date('2026-02-01'));
        $this->assertSame('17 Agu 2026, 08:30', format_datetime('2026-08-17 08:30:00'));
    }

    public function test_database_seeder_fulfills_all_minimum_requirements_and_pagination_renders(): void
    {
        $this->seed();

        // Minimal 1 Admin & 2 User
        $this->assertGreaterThanOrEqual(1, User::where('role', 'admin')->count());
        $this->assertGreaterThanOrEqual(2, User::where('role', 'user')->count());

        // Minimal 3 Kategori
        $this->assertGreaterThanOrEqual(3, Category::count());

        // Minimal 10 Barang (items)
        $this->assertGreaterThanOrEqual(10, Item::count());

        // Minimal 5 Peminjaman (loans) dengan variasi status
        $this->assertGreaterThanOrEqual(5, Loan::count());
        $this->assertGreaterThanOrEqual(1, Loan::where('status', 'dipinjam')->count());
        $this->assertGreaterThanOrEqual(3, Loan::where('status', 'dikembalikan')->count());

        // Minimal 3 Pengembalian (return_logs) sesuai dengan loans berstatus dikembalikan
        $this->assertGreaterThanOrEqual(3, ReturnLog::count());
        $this->assertEquals(
            Loan::where('status', 'dikembalikan')->count(),
            ReturnLog::count()
        );

        // Beberapa data activity_logs contoh
        $this->assertGreaterThanOrEqual(5, ActivityLog::count());

        // Test pagination page 2 rendering on items (since seeded items = 12 > 10 per page)
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)->get(route('items.index', ['page' => 2]))->assertOk();
    }
}
