<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'category_id' => 1,
                'unit_id' => 1,
                'sub_unit_id' => null,
                'office_id' => 1,
                'sub_office_id' => null,
                'rank_id' => 1,
                'name' => 'Admin User',
                'username' => 'admin',
                'role' => 1,
                'office' => 3,
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'category_id' => 1,
            //     'unit_id' => 1,
            //     'sub_unit_id' => 1,
            //     'office_id' => 1,
            //     'sub_office_id' => 1,
            //     'rank_id' => 2,
            //     'name' => 'Test User',
            //     'username' => 'testuser',
            //     'password' => Hash::make('password'),
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ]);
    }
}
