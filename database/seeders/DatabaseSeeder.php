<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $pelaksanaRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'pelaksana', 'guard_name' => 'web']);

        // Generate Shield Permissions
        \Illuminate\Support\Facades\Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin']);

        // 2. Seed Units
        $units = [
            ['code' => '100', 'name' => 'Pengawas', 'short_name' => 'PW', 'color' => '#f59e0b', 'description' => 'Pengawas'],
            ['code' => '110', 'name' => 'Pengurus', 'short_name' => 'PG', 'color' => '#3b82f6', 'description' => 'Pengurus'],
            ['code' => '120', 'name' => 'Unit SDM & Umum', 'short_name' => 'SU', 'color' => '#10b981', 'description' => 'Unit SDM & Umum'],
            ['code' => '130', 'name' => 'Unit Akuntansi & Keuangan', 'short_name' => 'AK', 'color' => '#ff0000', 'description' => 'Unit Akuntansi & Keuangan'],
            ['code' => '140', 'name' => 'Unit Perdagangan', 'short_name' => 'PDG', 'color' => '#00ff00', 'description' => 'Perdagangan'],
            ['code' => '150', 'name' => 'Unit Jasa', 'short_name' => 'JS', 'color' => '#000000', 'description' => 'Jasa'],
            ['code' => '160', 'name' => 'Unit Jasa Keuangan Syariah', 'short_name' => 'JKS', 'color' => '#605B51', 'description' => 'Jasa Keuangan Syariah'],
            ['code' => '170', 'name' => 'Unit Perdangan dan Jasa', 'short_name' => 'PBJ', 'color' => '#E6F082', 'description' => 'Perdagangan Barang dan Jasa'],
        ];

        foreach ($units as $unitData) {
            \App\Models\Unit::updateOrCreate(['code' => $unitData['code']], $unitData);
        }

        // 3. Seed Users
        $superAdmin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Administrator',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'nik' => '12345',
            ]
        );
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole($superAdminRole);
        }

        $bau = \App\Models\Unit::where('code', '100')->first();
        if ($bau) {
            $pelaksana = \App\Models\User::firstOrCreate(
                ['email' => 'pelaksana@admin.com'],
                [
                    'name' => 'Pelaksana BAU',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'nik' => '11111',
                    'unit_id' => $bau->id,
                ]
            );
            if (!$pelaksana->hasRole('pelaksana')) {
                $pelaksana->assignRole($pelaksanaRole);
            }

            // 4. Seed Category
            \App\Models\Category::firstOrCreate(['code' => 'SK'], [
                'name' => 'Surat Keputusan',
                'description' => 'Arsip Surat Keputusan Rektor/Dekan',
                'retention_years' => 5,
                'unit_id' => $bau->id
            ]);

            \App\Models\Category::firstOrCreate(['code' => 'MOU'], [
                'name' => 'Dokumen Kerja Sama',
                'description' => 'Akte/MoU Perjanjian Kerja Sama',
                'retention_years' => 10,
                'unit_id' => $bau->id
            ]);

            // 5. Seed Letter Types
            \App\Models\LetterType::firstOrCreate(['name' => 'Surat Keluar Biasa', 'unit_id' => $bau->id], [
                'color' => '#ef4444',
                'description' => 'Merah',
            ]);

            \App\Models\LetterType::firstOrCreate(['name' => 'Surat Edaran', 'unit_id' => $bau->id], [
                'color' => '#8b5cf6',
                'description' => 'Ungu',
            ]);

            // 6. Seed Letter Categories
            if (class_exists(\App\Models\LetterCategory::class)) {
                \App\Models\LetterCategory::firstOrCreate(['kode_surat' => 'UND'], [
                    'deskripsi' => 'Undangan Kegiatan',
                    'unit_id' => $bau->id
                ]);
            }
        }

        // 7. Seed Rak Arsip (Archive Shelves)
        $sdmUnit = \App\Models\Unit::where('code', '120')->first();

        // Tambahkan user Pelaksana untuk Unit SDM & Umum (120)
        if ($sdmUnit) {
            $pelaksanaSdm = \App\Models\User::firstOrCreate(
                ['email' => 'sdm@admin.com'],
                [
                    'name' => 'Pelaksana SDM & Umum',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'nik' => '22222',
                    'unit_id' => $sdmUnit->id,
                ]
            );
            if (!$pelaksanaSdm->hasRole('pelaksana')) {
                $pelaksanaSdm->assignRole($pelaksanaRole);
            }

            // Letter Type untuk unit SDM & Umum
            $ltSdm = \App\Models\LetterType::firstOrCreate(['name' => 'Surat Dinas SDM', 'unit_id' => $sdmUnit->id], [
                'color' => '#10b981',
                'description' => 'Surat resmi unit SDM',
            ]);

            // Category untuk unit SDM & Umum
            $catSdmSk = \App\Models\Category::firstOrCreate(['code' => 'SDM-SK'], [
                'name' => 'SK SDM & Umum',
                'description' => 'Surat Keputusan dari Unit SDM',
                'retention_years' => 5,
                'unit_id' => $sdmUnit->id,
            ]);
            $catSdmHrd = \App\Models\Category::firstOrCreate(['code' => 'SDM-HRD'], [
                'name' => 'Administrasi Kepegawaian',
                'description' => 'Dokumen HRD, kontrak, dll',
                'retention_years' => 10,
                'unit_id' => $sdmUnit->id,
            ]);

            $shelf1 = \App\Models\ArchiveShelf::firstOrCreate(['code' => 'RAK-001'], [
                'name' => 'Rak Arsip A - Periode 2018-2020',
                'location' => 'Lantai 2, Ruang Arsip Tengah',
                'description' => 'Rak untuk menyimpan ordner dengan periode 2018 hingga 2020',
                'status' => 'active',
                'managed_by_unit_id' => $sdmUnit->id,
            ]);
            $shelf2 = \App\Models\ArchiveShelf::firstOrCreate(['code' => 'RAK-002'], [
                'name' => 'Rak Arsip B - Periode 2020-2022',
                'location' => 'Lantai 2, Ruang Arsip Kanan',
                'description' => 'Rak cadangan untuk ordner masa retensi jangka menengah',
                'status' => 'active',
                'managed_by_unit_id' => $sdmUnit->id,
            ]);

            $superAdmin2 = \App\Models\User::where('email', 'admin@admin.com')->first();

            // Ordner EXPIRED, BELUM masuk rak (muncul di index dengan tombol "Pindah ke Rak")
            $ordner1 = \App\Models\Ordner::firstOrCreate(['code' => 'SDM-SK/001/2019'], [
                'unit_id' => $sdmUnit->id,
                'category_id' => $catSdmSk->id,
                'letter_type_id' => $ltSdm->id,
                'period' => '2019-01 - 2019-12',
                'status' => 'inactive',
                'description' => 'Ordner SK SDM Tahun 2019 - Retensi Habis',
                'retention_expires_at' => '2024-12-31',
                'archive_shelf_id' => null,
            ]);

            // Ordner EXPIRED, SUDAH masuk rak (tidak muncul di index ordner)
            \App\Models\Ordner::firstOrCreate(['code' => 'SDM-SK/002/2018'], [
                'unit_id' => $sdmUnit->id,
                'category_id' => $catSdmSk->id,
                'letter_type_id' => $ltSdm->id,
                'period' => '2018-01 - 2018-12',
                'status' => 'inactive',
                'description' => 'Ordner SK SDM Tahun 2018 - Sudah diarsipkan',
                'retention_expires_at' => '2023-06-01',
                'archive_shelf_id' => $shelf1->id,
            ]);

            // Ordner HRD AKTIF (muncul normal di index)
            $ordner3 = \App\Models\Ordner::firstOrCreate(['code' => 'SDM-HRD/001/2025'], [
                'unit_id' => $sdmUnit->id,
                'category_id' => $catSdmHrd->id,
                'letter_type_id' => $ltSdm->id,
                'period' => '2025-01 - 2025-12',
                'status' => 'active',
                'description' => 'Ordner Kepegawaian Aktif 2025',
                'retention_expires_at' => '2035-12-31',
                'archive_shelf_id' => null,
            ]);

            // Ordner HRD AKTIF 2 (muncul normal)
            $ordner4 = \App\Models\Ordner::firstOrCreate(['code' => 'SDM-HRD/002/2025'], [
                'unit_id' => $sdmUnit->id,
                'category_id' => $catSdmHrd->id,
                'letter_type_id' => $ltSdm->id,
                'period' => '2025-01 - 2025-06',
                'status' => 'active',
                'description' => 'Ordner Kontrak Kerja 2025',
                'retention_expires_at' => '2035-12-31',
                'archive_shelf_id' => null,
            ]);

            // Dokumen Arsip untuk ordner aktif SDM-HRD/001/2025
            $archivesSdm = [
                ['code' => 'SDM-2025-001', 'title' => 'Kontrak Kerja Karyawan Baru - Budi Santoso'],
                ['code' => 'SDM-2025-002', 'title' => 'SK Pengangkatan Staf Administrasi'],
                ['code' => 'SDM-2025-003', 'title' => 'Surat Keterangan Kerja - 2025'],
                ['code' => 'SDM-2025-004', 'title' => 'Absensi Karyawan Januari 2025'],
            ];
            foreach ($archivesSdm as $archData) {
                \App\Models\Archive::firstOrCreate(['code' => $archData['code']], [
                    'title' => $archData['title'],
                    'description' => 'Dokumen kepegawaian unit SDM',
                    'category_id' => $catSdmHrd->id,
                    'ordner_id' => $ordner3->id,
                    'unit_id' => $sdmUnit->id,
                    'status' => 'active',
                    'uploaded_by' => $superAdmin2?->id,
                    'upload_date' => now(),
                    'is_confidential' => false,
                ]);
            }

            // Dokumen di ordner EXPIRED (sudah masuk rak)
            \App\Models\Archive::firstOrCreate(['code' => 'SDM-2019-001'], [
                'title' => 'SK Penempatan Jabatan Struktural 2019',
                'description' => 'Sudah diarsipkan di rak',
                'category_id' => $catSdmSk->id,
                'ordner_id' => $ordner1->id,
                'unit_id' => $sdmUnit->id,
                'status' => 'inactive',
                'uploaded_by' => $superAdmin2?->id,
                'upload_date' => \Carbon\Carbon::parse('2019-08-20'),
                'is_confidential' => false,
            ]);
        }
    }
}
