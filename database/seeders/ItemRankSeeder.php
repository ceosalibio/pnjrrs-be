<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ItemRank;

class ItemRankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemRank::insert([
            ['division_id' => 1, 'grade_id' => 1, 'name' => 'ADM', 'description' => 'Admiral officer rank'],
            ['division_id' => 1, 'grade_id' => 2, 'name' => 'GEN', 'description' => 'General officer rank'],
            ['division_id' => 1, 'grade_id' => 2, 'name' => 'VADM', 'description' => 'Vice Admiral officer rank'],
            ['division_id' => 1, 'grade_id' => 2, 'name' => 'LTGEN', 'description' => 'Lieutenant General officer rank'],
            ['division_id' => 1, 'grade_id' => 3, 'name' => 'RADM', 'description' => 'Rear Admiral officer rank'],
            ['division_id' => 1, 'grade_id' => 3, 'name' => 'MGEN', 'description' => 'Major General officer rank'],
            ['division_id' => 1, 'grade_id' => 4, 'name' => 'COMMO', 'description' => 'Commodore officer rank'],
            ['division_id' => 1, 'grade_id' => 4, 'name' => 'BGEN', 'description' => 'Brigadier General officer rank'],
            ['division_id' => 1, 'grade_id' => 5, 'name' => 'CAPT', 'description' => 'Captain officer rank'],
            ['division_id' => 1, 'grade_id' => 5, 'name' => 'COL', 'description' => 'Colonel officer rank'],
            ['division_id' => 1, 'grade_id' => 6, 'name' => 'CDR', 'description' => 'Commander officer rank'],
            ['division_id' => 1, 'grade_id' => 6, 'name' => 'LTCOL', 'description' => 'Lieutenant Colonel officer rank'],
            ['division_id' => 1, 'grade_id' => 7, 'name' => 'LCDR', 'description' => 'Lieutenant Commander officer rank'],
            ['division_id' => 1, 'grade_id' => 7, 'name' => 'MAJ', 'description' => 'Major officer rank'],
            ['division_id' => 1, 'grade_id' => 8, 'name' => 'CPT', 'description' => 'Captain officer rank'],
            ['division_id' => 1, 'grade_id' => 8, 'name' => 'LT', 'description' => 'Lieutenant officer rank'],
            ['division_id' => 1, 'grade_id' => 9, 'name' => '1LT', 'description' => 'First Lieutenant officer rank'],
            ['division_id' => 1, 'grade_id' => 9, 'name' => 'LTJG', 'description' => 'Lieutenant Junior Grade officer rank'],
            ['division_id' => 1, 'grade_id' => 10, 'name' => '2LT', 'description' => 'Second Lieutenant officer rank'],
            ['division_id' => 1, 'grade_id' => 10, 'name' => 'ENS', 'description' => 'Ensign officer rank'],
            ['division_id' => 2, 'grade_id' => 11, 'name' => 'CSM', 'description' => 'Command Sergeant Major rank'],
            ['division_id' => 2, 'grade_id' => 12, 'name' => 'MCPO', 'description' => 'Master Chief Petty Officer rank'],
            ['division_id' => 2, 'grade_id' => 12, 'name' => 'CMSGT', 'description' => 'Chief Master Sergeant rank'],
            ['division_id' => 2, 'grade_id' => 13, 'name' => 'SCPO', 'description' => 'Senior Chief Petty Officer rank'],
            ['division_id' => 2, 'grade_id' => 13, 'name' => 'SMSGT', 'description' => 'Senior Master Sergeant rank'],
            ['division_id' => 2, 'grade_id' => 14, 'name' => 'CPO', 'description' => 'Chief Petty Officer rank'],
            ['division_id' => 2, 'grade_id' => 14, 'name' => 'MSGT', 'description' => 'Master Sergeant rank'],
            ['division_id' => 2, 'grade_id' => 15, 'name' => 'PO1', 'description' => 'Petty Officer First Class rank'],
            ['division_id' => 2, 'grade_id' => 15, 'name' => 'TSGT', 'description' => 'Technical Sergeant rank'],
            ['division_id' => 2, 'grade_id' => 16, 'name' => 'PO2', 'description' => 'Petty Officer Second Class rank'],
            ['division_id' => 2, 'grade_id' => 16, 'name' => 'SSGT', 'description' => 'Staff Sergeant rank'],
            ['division_id' => 2, 'grade_id' => 17, 'name' => 'PO3', 'description' => 'Petty Officer Third Class rank'],
            ['division_id' => 2, 'grade_id' => 17, 'name' => 'SGT', 'description' => 'Sergeant rank'],
            ['division_id' => 2, 'grade_id' => 18, 'name' => 'SN1', 'description' => 'Seaman First Class rank'],
            ['division_id' => 2, 'grade_id' => 18, 'name' => 'FN1', 'description' => 'Fireman First Class rank'],
            ['division_id' => 2, 'grade_id' => 18, 'name' => 'CPL', 'description' => 'Corporal rank'],
            ['division_id' => 2, 'grade_id' => 19, 'name' => 'SN2', 'description' => 'Seaman Second Class rank'],
            ['division_id' => 2, 'grade_id' => 19, 'name' => 'FN2', 'description' => 'Fireman Second Class rank'],
            ['division_id' => 2, 'grade_id' => 19, 'name' => 'PFC', 'description' => 'Private First Class rank'],
            ['division_id' => 2, 'grade_id' => 20, 'name' => 'ASN', 'description' => 'Aviation Seaman rank'],
            ['division_id' => 2, 'grade_id' => 20, 'name' => 'AFN', 'description' => 'Aviation Fireman rank'],
            ['division_id' => 2, 'grade_id' => 20, 'name' => 'PVT', 'description' => 'Private rank'],
            ['division_id' => 3, 'grade_id' => 21, 'name' => 'SG24', 'description' => 'Salary Grade 24'],
            ['division_id' => 3, 'grade_id' => 22, 'name' => 'SG23', 'description' => 'Salary Grade 23'],
            ['division_id' => 3, 'grade_id' => 23, 'name' => 'SG22', 'description' => 'Salary Grade 22'],
            ['division_id' => 3, 'grade_id' => 24, 'name' => 'SG21', 'description' => 'Salary Grade 21'],
            ['division_id' => 3, 'grade_id' => 25, 'name' => 'SG20', 'description' => 'Salary Grade 20'],
            ['division_id' => 3, 'grade_id' => 26, 'name' => 'SG19', 'description' => 'Salary Grade 19'],
            ['division_id' => 3, 'grade_id' => 27, 'name' => 'SG18', 'description' => 'Salary Grade 18'],
            ['division_id' => 3, 'grade_id' => 28, 'name' => 'SG17', 'description' => 'Salary Grade 17'],
            ['division_id' => 3, 'grade_id' => 29, 'name' => 'SG16', 'description' => 'Salary Grade 16'],
            ['division_id' => 3, 'grade_id' => 30, 'name' => 'SG15', 'description' => 'Salary Grade 15'],
            ['division_id' => 3, 'grade_id' => 31, 'name' => 'SG14', 'description' => 'Salary Grade 14'],
            ['division_id' => 3, 'grade_id' => 32, 'name' => 'SG13', 'description' => 'Salary Grade 13'],
            ['division_id' => 3, 'grade_id' => 33, 'name' => 'SG12', 'description' => 'Salary Grade 12'],
            ['division_id' => 3, 'grade_id' => 34, 'name' => 'SG11', 'description' => 'Salary Grade 11'],
            ['division_id' => 3, 'grade_id' => 35, 'name' => 'SG10', 'description' => 'Salary Grade 10'],
            ['division_id' => 3, 'grade_id' => 36, 'name' => 'SG9', 'description' => 'Salary Grade 9'],
            ['division_id' => 3, 'grade_id' => 37, 'name' => 'SG8', 'description' => 'Salary Grade 8'],
            ['division_id' => 3, 'grade_id' => 38, 'name' => 'SG7', 'description' => 'Salary Grade 7'],
            ['division_id' => 3, 'grade_id' => 39, 'name' => 'SG6', 'description' => 'Salary Grade 6'],
            ['division_id' => 3, 'grade_id' => 40, 'name' => 'SG5', 'description' => 'Salary Grade 5'],
            ['division_id' => 3, 'grade_id' => 41, 'name' => 'SG4', 'description' => 'Salary Grade 4'],
            ['division_id' => 3, 'grade_id' => 42, 'name' => 'SG3', 'description' => 'Salary Grade 3'],
            ['division_id' => 3, 'grade_id' => 43, 'name' => 'SG2', 'description' => 'Salary Grade 2'],
            ['division_id' => 3, 'grade_id' => 44, 'name' => 'SG1', 'description' => 'Salary Grade 1']
        ]);
    }
}
