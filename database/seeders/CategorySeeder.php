<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'income' => [
                'Gaji',
                'Bonus',
                'Investasi',
                'Hadiah',
                'Pendapatan Lainnya'
            ],

            'expense' => [
                'Makanan & Minuman',
                'Transportasi',
                'Belanja',
                'Tagihan',
                'Hiburan',
                'Pendidikan',
                'Kesehatan',
                'Pengeluaran Lainnya'
            ]
        ];

        foreach (User::all() as $user) {
            foreach ($categories as $type => $names) {
                foreach ($names as $name) {
                    Category::firstOrCreate([
                        'user_id' => $user->id,
                        'name' => $name,
                        'type' => $type
                    ]);
                }
            }
        }
    }
}