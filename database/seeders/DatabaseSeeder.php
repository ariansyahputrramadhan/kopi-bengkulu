<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin
        User::updateOrCreate(
            ['email' => 'admin@kopibengkulu.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Akun pelanggan contoh
        User::updateOrCreate(
            ['email' => 'customer@kopibengkulu.test'],
            [
                'name' => 'Pelanggan Contoh',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        // Kategori & produk
        $data = [
            'Biji Kopi' => [
                ['Kopi Kerinci Arabika 250g', 'Kopi arabika dengan aroma floral dan rasa asam segar.', 85000, 50],
                ['Kopi Robusta Bengkulu 250g', 'Robusta khas Bengkulu, body tebal dan pahit mantap.', 55000, 80],
                ['Kopi Liberika 250g', 'Rasa unik dengan aroma buah nangka yang khas.', 95000, 30],
            ],
            'Kopi Bubuk' => [
                ['Kopi Bubuk Robusta 200g', 'Bubuk halus siap seduh, cocok untuk kopi tubruk.', 40000, 100],
                ['Kopi Bubuk Arabika 200g', 'Bubuk arabika dengan keasaman seimbang.', 65000, 60],
            ],
            'Peralatan' => [
                ['V60 Dripper Keramik', 'Dripper keramik untuk seduh manual.', 120000, 25],
                ['Kettle Leher Angsa 600ml', 'Kettle stainless untuk tuang presisi.', 220000, 15],
            ],
        ];

        foreach ($data as $categoryName => $products) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            foreach ($products as [$name, $description, $price, $stock]) {
                Product::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'stock' => $stock,
                    ]
                );
            }
        }
    }
}