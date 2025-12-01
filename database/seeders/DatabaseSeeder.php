<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Grade;
use App\Models\GradeL;
use App\Models\GradeService;
use App\Models\GradeHService;
use App\Models\KategoriBeratPenerimaan;
use App\Models\KategoriByprodukCt;
use App\Models\KategoriProduk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key check sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan tabel
        User::truncate();
        Grade::truncate();
        Supplier::truncate();
        KategoriBeratPenerimaan::truncate();
        KategoriByprodukCt::truncate();
        KategoriProduk::truncate();
        // Aktifkan foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Buat data user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role_id' => '1',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        
        User::create([
            'name' => 'Pimpinan',
            'email' => 'superadmin@gmail.com',
            'role_id' => '3',
            'password' => Hash::make('superadmin'),
            'email_verified_at' => now(),
        ]);

        // Buat data supplier
        Supplier::create([
            'supplier_id' => 1,
            'nama_supplier' => 'BPM',
            'alamat' => 'Jakarta Barat',
        ]);

        // Buat data grade penerimaan
        $grades = [
            ['grade' => 'A'],
            ['grade' => 'B/C'],
            ['grade' => 'D'],
        ];
        Grade::insert($grades);

        // Buat data kategori berat penerimaan
        $kategoriBerat = [
            ['kategori_berat' => '20 UP'],
            ['kategori_berat' => '20 DOWN'],
            ['kategori_berat' => '30 UP'],
        ];
        KategoriBeratPenerimaan::insert($kategoriBerat);

        //Buat data grade/sizing Loin
        $gradel = [
            ['grade_sizing' => 'A 3kg Up'],
            ['grade_sizing' => 'A 3kg Down'],
            ['grade_sizing' => 'B/C 3kg Up'],
            ['grade_sizing' => 'B/C 3kg Down'],
            ['grade_sizing' => 'C 3kg Up'],
            ['grade_sizing' => 'C 3kg Down'],
            ['grade_sizing' => 'D All Size'],
            ['grade_sizing' => 'Mixed Grade'],
            ['grade_sizing' => 'Loin Natural Lokal'],
            ['grade_sizing' => 'Loin Sashi'],
            ['grade_sizing' => 'Loin Bau'],
            ['grade_sizing' => 'Loin Parasit'],
        ];
        GradeL::insert($gradel);

        //Buat data grade hasil service loin
        $gradehs = [
            ['grade_servicehs' => 'Saku AA'],
            ['grade_servicehs' => 'Saku AAA'],
            ['grade_servicehs' => 'AAA 4 Kg Up'],
            ['grade_servicehs' => 'AAA 2,5 - 4 Kg'],
            ['grade_servicehs' => 'AAA 2,5 Kg Down'],
            ['grade_servicehs' => 'AA+ 4 Kg Up'],
            ['grade_servicehs' => 'AA+ 2,5 - 4 Kg'],
            ['grade_servicehs' => 'AA+ 2,5 Kg Down'],
            ['grade_servicehs' => 'AA 4 Kg Up'],
            ['grade_servicehs' => 'AA 2,5 - 4 Kg'],
            ['grade_servicehs' => 'AA 2,5 Kg Down'],
            ['grade_servicehs' => 'AA- 4 Kg Up'],
            ['grade_servicehs' => 'AA- 2,5 - 4 Kg'],
            ['grade_servicehs' => 'AA- 2,5 Kg Down'],
            ['grade_servicehs' => 'Loin Co QD'],
            ['grade_servicehs' => 'Loin LS'],
            ['grade_servicehs' => 'Loin LP'],
            ['grade_servicehs' => 'Loin LB'],
            ['grade_servicehs' => 'Loin LH'],
        ];
        GradeHService::insert($gradehs);

        //Buat data grade service
        $gradeservice = [
            ['grading' => 'AAA'],
            ['grading' => 'AAA+'],
            ['grading' => 'AAA-'],
            ['grading' => 'AA'],
            ['grading' => 'AA+'],
            ['grading' => 'AA-'],
            ['grading' => 'Delta'],
            ['grading' => 'Sashi'],
            ['grading' => 'Parasit'],
            ['grading' => 'Bau'],
            ['grading' => 'Histamin'],
        ];
        GradeService::insert($gradeservice);

        // Buat data kategori byproduk
        $byproducts = [
            ['nama_produk' => 'Belly'],
            ['nama_produk' => 'D. Kepala'],
            ['nama_produk' => 'D. Pipi'],
            ['nama_produk' => 'D. Kerok'],
            ['nama_produk' => 'Iga Kerok'],
            ['nama_produk' => 'Kama'],
            ['nama_produk' => 'O-toro'],
            ['nama_produk' => 'TM (Tetelan Merah)'],
        ];
        KategoriByprodukCt::insert($byproducts);

        // Buat data kategori produk
        $produks = [
            ['nama_produk' => 'Saku'],
            ['nama_produk' => 'Cube'],
            ['nama_produk' => 'Loin CC'],
            ['nama_produk' => 'Steak'],
            ['nama_produk' => 'Strips'],
        ];
        KategoriProduk::insert($produks);

        $this->command->info('Database seeded successfully!');
    }
}
