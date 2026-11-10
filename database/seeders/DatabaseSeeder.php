<?php

namespace Database\Seeders;

use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role' => 'admin',
            ]
        );

        $member = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Member User',
                'username' => 'member',
                'password' => Hash::make('member'),
                'role' => 'member',
            ]
        );

        Stock::firstOrCreate(
            ['user_id' => $member->id, 'name' => 'Laptop ThinkPad X1 Carbon'],
            [
                'sku' => 'ELK-LTP-001',
                'description' => 'Laptop bisnis ringan dengan prosesor Intel Core Ultra dan RAM 16GB.',
                'quantity' => 15,
                'unit' => 'unit',
                'price' => 24500000,
                'min_stock' => 5,
                'status' => 'available',
            ]
        );

        Stock::firstOrCreate(
            ['user_id' => $member->id, 'name' => 'Mouse Wireless Logitech MX Master 3S'],
            [
                'sku' => 'ACC-MSE-002',
                'description' => 'Mouse ergonomis wireless dengan sensor presisi tinggi 8000 DPI.',
                'quantity' => 4,
                'unit' => 'pcs',
                'price' => 1650000,
                'min_stock' => 5,
                'status' => 'low_stock',
            ]
        );

        Stock::firstOrCreate(
            ['user_id' => $member->id, 'name' => 'Kertas HVS A4 80gsm PaperOne'],
            [
                'sku' => 'ATK-KRT-003',
                'description' => 'Kertas HVS A4 80 gram kualitas premium untuk kebutuhan cetak kantor.',
                'quantity' => 0,
                'unit' => 'rim',
                'price' => 65000,
                'min_stock' => 10,
                'status' => 'out_of_stock',
            ]
        );
    }
}
