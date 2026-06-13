<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ItemGrade;

class ItemGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemGrade::insert([
            ['division_id' => 1, 'name' => 'O10', 'description' => 'OFFICER 10'],
            ['division_id' => 1, 'name' => 'O9', 'description' => 'OFFICER 9'],
            ['division_id' => 1, 'name' => 'O8', 'description' => 'OFFICER 8'],
            ['division_id' => 1, 'name' => 'O7', 'description' => 'OFFICER 7'],
            ['division_id' => 1, 'name' => 'O6', 'description' => 'OFFICER 6'],
            ['division_id' => 1, 'name' => 'O5', 'description' => 'OFFICER 5'],
            ['division_id' => 1, 'name' => 'O4', 'description' => 'OFFICER 4'],
            ['division_id' => 1, 'name' => 'O3', 'description' => 'OFFICER 3'],
            ['division_id' => 1, 'name' => 'O2', 'description' => 'OFFICER 2'],
            ['division_id' => 1, 'name' => 'O1', 'description' => 'OFFICER 1'],
            ['division_id' => 2, 'name' => 'E10', 'description' => 'ENLISTED 10'],
            ['division_id' => 2, 'name' => 'E9', 'description' => 'ENLISTED 9'],
            ['division_id' => 2, 'name' => 'E8', 'description' => 'ENLISTED 8'],
            ['division_id' => 2, 'name' => 'E7', 'description' => 'ENLISTED 7'],
            ['division_id' => 2, 'name' => 'E6', 'description' => 'ENLISTED 6'],
            ['division_id' => 2, 'name' => 'E5', 'description' => 'ENLISTED 5'],
            ['division_id' => 2, 'name' => 'E4', 'description' => 'ENLISTED 4'],
            ['division_id' => 2, 'name' => 'E3', 'description' => 'ENLISTED 3'],
            ['division_id' => 2, 'name' => 'E2', 'description' => 'ENLISTED 2'],
            ['division_id' => 2, 'name' => 'E1', 'description' => 'ENLISTED 1'],
            ['division_id' => 3, 'name' => 'SG24', 'description' => 'SALARY GRADE 24'],
            ['division_id' => 3, 'name' => 'SG23', 'description' => 'SALARY GRADE 23'],
            ['division_id' => 3, 'name' => 'SG22', 'description' => 'SALARY GRADE 22'],
            ['division_id' => 3, 'name' => 'SG21', 'description' => 'SALARY GRADE 21'],
            ['division_id' => 3, 'name' => 'SG20', 'description' => 'SALARY GRADE 20'],
            ['division_id' => 3, 'name' => 'SG19', 'description' => 'SALARY GRADE 19'],
            ['division_id' => 3, 'name' => 'SG18', 'description' => 'SALARY GRADE 18'],
            ['division_id' => 3, 'name' => 'SG17', 'description' => 'SALARY GRADE 17'],
            ['division_id' => 3, 'name' => 'SG16', 'description' => 'SALARY GRADE 16'],
            ['division_id' => 3, 'name' => 'SG15', 'description' => 'SALARY GRADE 15'],
            ['division_id' => 3, 'name' => 'SG14', 'description' => 'SALARY GRADE 14'],
            ['division_id' => 3, 'name' => 'SG13', 'description' => 'SALARY GRADE 13'],
            ['division_id' => 3, 'name' => 'SG12', 'description' => 'SALARY GRADE 12'],
            ['division_id' => 3, 'name' => 'SG11', 'description' => 'SALARY GRADE 11'],
            ['division_id' => 3, 'name' => 'SG10', 'description' => 'SALARY GRADE 10'],
            ['division_id' => 3, 'name' => 'SG9', 'description' => 'SALARY GRADE 9'],
            ['division_id' => 3, 'name' => 'SG8', 'description' => 'SALARY GRADE 8'],
            ['division_id' => 3, 'name' => 'SG7', 'description' => 'SALARY GRADE 7'],
            ['division_id' => 3, 'name' => 'SG6', 'description' => 'SALARY GRADE 6'],
            ['division_id' => 3, 'name' => 'SG5', 'description' => 'SALARY GRADE 5'],
            ['division_id' => 3, 'name' => 'SG4', 'description' => 'SALARY GRADE 4'],
            ['division_id' => 3, 'name' => 'SG3', 'description' => 'SALARY GRADE 3'],
            ['division_id' => 3, 'name' => 'SG2', 'description' => 'SALARY GRADE 2'],
            ['division_id' => 3, 'name' => 'SG1', 'description' => 'SALARY GRADE 1']
        ]);
    }
}
