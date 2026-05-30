<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LensHubSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Equipment::query()->delete();
        Category::query()->delete();
        User::query()->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        // =====================
        // AKUN DEMO (per role)
        // =====================

        User::create([
            'username' => 'owner',
            'name'     => 'Anggeline Owner',
            'email'    => 'owner@lenshub.com',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        User::create([
            'username' => 'admin',
            'name'     => 'Admin LensHub',
            'email'    => 'admin@lenshub.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'username' => 'user1',
            'name'     => 'Budi Santoso',
            'email'    => 'user@lenshub.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);


        // =====================
        // KATEGORI & EQUIPMENT
        // =====================

        $kamera     = Category::create(['nama' => 'Kamera']);
        $lensa      = Category::create(['nama' => 'Lensa']);
        $drone      = Category::create(['nama' => 'Drone']);
        $tripod     = Category::create(['nama' => 'Tripod / Stand']);
        $stabilizer = Category::create(['nama' => 'Stabilizer']);
        $lighting   = Category::create(['nama' => 'Lighting']);

        Equipment::create([
            'category_id' => $kamera->id,
            'nama'         => 'Sony A7 IV',
            'deskripsi'    => 'Mirrorless full-frame',
            'stok'         => 5,
            'harga_harian' => 300000,
        ]);

        Equipment::create([
            'category_id' => $kamera->id,
            'nama'         => 'Canon EOS R6',
            'deskripsi'    => 'Mirrorless profesional',
            'stok'         => 3,
            'harga_harian' => 250000,
        ]);

        Equipment::create([
            'category_id' => $lensa->id,
            'nama'         => 'Sony FE 24–70mm',
            'deskripsi'    => 'Lensa zoom',
            'stok'         => 4,
            'harga_harian' => 200000,
        ]);

        Equipment::create([
            'category_id' => $drone->id,
            'nama'         => 'DJI Mini 4 Pro',
            'deskripsi'    => 'Drone sinematik',
            'stok'         => 2,
            'harga_harian' => 500000,
        ]);

        Equipment::create([
            'category_id' => $tripod->id,
            'nama'         => 'Manfrotto Tripod',
            'deskripsi'    => 'Tripod profesional',
            'stok'         => 6,
            'harga_harian' => 75000,
        ]);

        Equipment::create([
            'category_id' => $stabilizer->id,
            'nama'         => 'Zhiyun Crane',
            'deskripsi'    => 'Stabilizer video',
            'stok'         => 2,
            'harga_harian' => 150000,
        ]);

        Equipment::create([
            'category_id' => $lighting->id,
            'nama'         => 'Godox SL60',
            'deskripsi'    => 'Lighting studio',
            'stok'         => 8,
            'harga_harian' => 90000,
        ]);
    }
}
