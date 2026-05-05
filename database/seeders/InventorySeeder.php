<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Gear;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Kategori Master
        $cam = Category::create(['name' => 'Kamera', 'slug' => 'kamera', 'prefix' => 'CAM']);
        $lens = Category::create(['name' => 'Lensa', 'slug' => 'lensa', 'prefix' => 'LENS']);

        // 2. Buat Unit Gear (Kode unit akan terisi otomatis via Model Booting kita tadi)
        Gear::create([
            'category_id' => $cam->id,
            'name' => 'Sony A7III',
            'rent_price' => 150000,
            'penalty_fee' => 50000,
        ]);

        Gear::create([
            'category_id' => $lens->id,
            'name' => 'Sony FE 50mm f/1.8',
            'rent_price' => 75000,
            'penalty_fee' => 25000,
        ]);
    }
}