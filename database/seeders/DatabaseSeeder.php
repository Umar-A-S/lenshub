<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            // membuat seed akun owner dan admin sekaligus untuk testing
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('ownerpassword'),
            'role' => 'owner',
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('adminpassword'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Umar Alfi',
            'email' => 'umar@example.com',
            'password' => bcrypt('userpassword'),
            'role' => 'user',
        ]);


        $this->call([
            InventorySeeder::class,
            //RentalSeeder::class,
        ]);
    }
}
