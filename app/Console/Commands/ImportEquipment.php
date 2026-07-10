<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EquipmentCategory;
use App\Models\EquipmentDivision;
use App\Models\EquipmentType;
use App\Models\EquipmentList;

class ImportEquipment extends Command
{
    /**
     * php artisan import:equipment {path}
     *
     * Example:
     *   php artisan import:equipment storage/app/pn_equipments.csv
     * 
     * CSV Format:
     *   category, division, type, list, urrs
     */
    protected $signature = 'import:equipment {path}';

    protected $description = 'Import equipment template data from a CSV file';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->error("Could not open file: {$path}");
            return self::FAILURE;
        }

        // Read header row
        $header = fgetcsv($handle);
        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $header);

        $expected = ['categories', 'divisions', 'types', 'lists'];
        if ($header !== $expected) {
            $this->warn('Header row found: ' . implode(', ', $header));
            $this->warn('Expected: ' . implode(', ', $expected));
            $this->warn('Continuing anyway, assuming column order is category, division, type, list, urrs...');
        }

        $rowNumber = 1;
        $created = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip completely empty rows
            if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            [$category, $division, $type, $list] = array_pad(
                array_map(fn ($v) => trim((string) $v), $row),
                5,
                ''
            );

            // Validate required fields
            if (!$category || !$division || !$type || !$list) {
                $this->warn("Row {$rowNumber}: skipped, category, division, type, and list are required.");
                $skipped++;
                continue;
            }

            try {
                // Create/Get category
                $categoryModel = EquipmentCategory::firstOrCreate(
                    ['name' => $category],
                    ['name' => $category]
                );

                // Create/Get division
                $divisionModel = EquipmentDivision::firstOrCreate(
                    ['name' => $division, 'category_id' => $categoryModel->id],
                    ['name' => $division, 'category_id' => $categoryModel->id]
                );

                // Create/Get type
                $typeModel = EquipmentType::firstOrCreate(
                    ['name' => $type, 'division_id' => $divisionModel->id],
                    [
                        'name' => $type,
                        'division_id' => $divisionModel->id,
                        'category_id' => $categoryModel->id
                    ]
                );

                // Create/Get list item
                $listModel = EquipmentList::firstOrCreate(
                    ['name' => $list, 'type_id' => $typeModel->id],
                    [
                        'name' => $list,
                        'type_id' => $typeModel->id,
                        'division_id' => $divisionModel->id,
                        'category_id' => $categoryModel->id
                    ]
                );

                $created++;
            } catch (\Exception $e) {
                $this->error("Row {$rowNumber}: error - " . $e->getMessage());
                $skipped++;
            }
        }

        fclose($handle);

        $this->info('Import complete:');
        $this->table(
            ['Type', 'Count'],
            [
                ['Created', $created],
                ['Skipped', $skipped],
            ]
        );

        return self::SUCCESS;
    }
}
