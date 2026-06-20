<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PnCategory;
use App\Models\PnUnit;
use App\Models\PnSubUnit;
use App\Models\PnOffice;

class HPNSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
     {
        // 1. Create Category
        $category = PnCategory::create([
            'name'        => 'HEADQUARTERS PHILIPPINE NAVY',
            'abreviation' => 'HPN',
            'description' => null,
            'address'     => null,
            'icon'        => null,
            'created_by'  => null,
            'updated_by'  => null,
        ]);
 
        // 2. Create Unit (under the category above)
        $unit = PnUnit::create([
            'category_id' => $category->id,
            'name'        => 'HEADQUARTERS PHILIPPINE NAVY',
            'abreviation' => 'HPN',
            'address'     => null,
            'description' => null,
            'icon'        => null,
            'created_by'  => null,
            'updated_by'  => null,
        ]);
 
        // 3. Create Sub Units (under the category & unit above)
        $subUnits = [
            'PERSONAL STAFF',
            'COORDINATING STAFF',
            'TECHNICAL STAFF',
            'SPECIAL STAFF',
            'FUNCTIONAL STAFF',
        ];
 
        foreach ($subUnits as $subUnitName) {
            PnSubUnit::create([
                'category_id' => $category->id,
                'unit_id'     => $unit->id,
                'name'        => $subUnitName,
                'abreviation' => null,
                'address'     => null,
                'description' => null,
                'icon'        => null,
                'created_by'  => null,
                'updated_by'  => null,
            ]);
        }
 
        // 4. (Optional) Offices go here once you have office data, e.g.:
        // PnOffice::create([
        //     'category_id' => $category->id,
        //     'unit_id'     => $unit->id,
        //     'sub_unit_id' => $subUnit->id, // nullable
        //     'name'        => 'OFFICE NAME HERE',
        //     ...
        // ]);
    }
}
