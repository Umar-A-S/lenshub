<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Gear;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definisi Kategori
        $categories = [
            ['name' => 'Kamera', 'prefix' => 'CAM'],
            ['name' => 'Lensa', 'prefix' => 'LENS'],
            ['name' => 'Drone', 'prefix' => 'DRN'],
        ];

        foreach ($categories as $catData) {
            // Buat Kategori
            $category = Category::create([
                'name' => $catData['name'],
                'slug' => Str::slug($catData['name']),
                'prefix' => $catData['prefix']
            ]);

            // 2. Tambahkan Barang Contoh untuk setiap kategori
            // Kita buat 2 barang per kategori sebagai contoh
            for ($i = 1; $i <= 2; $i++) {
                Gear::create([
                    'category_id' => $category->id,
                    'name'        => $category->name . " Seri " . $i,
                    'unit_code'   => $category->generateNewCode(), // Panggil fungsi sakti di sini
                    'rent_price'  => 100000 * $i,
                    'penalty_fee' => 50000,
                    'status'      => 'available',
                    'condition_status' => 'baik',
                ]);
            }
        }
    }
}