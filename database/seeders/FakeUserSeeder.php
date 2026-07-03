<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FakeUserSeeder extends Seeder
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
                'sub_unit_id' => 1,
                'office_id' => 1,
                'sub_office_id' => null,
                'rank_id' => 1,
                'name' => 'User 1',
                'position' => 'Drafter',
                'username' => 'user1',
                'role' => 0,
                'approver' => 0,
                'office_role' => 1,
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1,
                'unit_id' => 1,
                'sub_unit_id' => 1,
                'office_id' => 1,
                'sub_office_id' => null,
                'rank_id' => 1,
                'name' => 'User 2',
                'position' => 'OIC',
                'username' => 'user2',
                'role' => 2,
                'approver' => 1,
                'office_role' => 1,
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1,
                'unit_id' => 1,
                'sub_unit_id' => 1,
                'office_id' => 1,
                'sub_office_id' => null,
                'rank_id' => 1,
                'name' => 'User 3',
                'position' => 'OIC',
                'username' => 'user3',
                'role' => 2,
                'approver' => 2,
                'office_role' => 1,
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'category_id' => 1,
                'unit_id' => 1,
                'sub_unit_id' => 1,
                'office_id' => 1,
                'sub_office_id' => null,
                'rank_id' => 1,
                'name' => 'User 4',
                'position' => 'Deputy',
                'username' => 'user4',
                'role' => 2,
                'approver' => 2,
                'office_role' => 1,
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
