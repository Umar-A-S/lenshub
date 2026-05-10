<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Gear;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'owner@lenshub.test'],
            [
                'name' => 'LensHub Owner',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@lenshub.test'],
            [
                'name' => 'LensHub Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        Client::query()->updateOrCreate(
            ['nik' => '0000000000000001'],
            [
                'name' => 'Client Demo',
                'whatsapp_number' => '081234567890',
                'address' => 'Semarang',
                'identity_photo' => null,
            ]
        );

        Gear::query()->updateOrCreate(
            ['name' => 'Camera Canon EOS R10'],
            [
                'category' => 'Camera',
                'stock_total' => 2,
                'stock_available' => 2,
                'price_per_hour' => 50000,
                'price_per_day' => 250000,
                'status' => 'available',
            ]
        );
    }
}
