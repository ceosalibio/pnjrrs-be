<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ItemDivision;

class ItemDivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemDivision::insert([
            ['name' => 'OFFICER', 'description' => 'Officer rank'],
            ['name' => 'ENLISTED', 'description' => 'Enlisted rank'],
            ['name' => 'CIVILIAN', 'description' => 'Civilian personnel'],
        ]);
    }
}
