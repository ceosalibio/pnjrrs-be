<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PnCategory;
use App\Models\PnUnit;
use App\Models\PnSubUnit;
use App\Models\PnOffice;

class ImportPnOrganization extends Command
{
    /**
     * php artisan pn:import-csv {path}
     *
     * Example:
     *   php artisan pn:import-csv storage/app/pn_organization.csv
     */
    protected $signature = 'pn:import-csv {path}';

    protected $description = 'Import PN Category / Unit / SubUnit / Office hierarchy from a CSV file';

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

        // Read header row: Category, Unit, SubUnit, Office
        $header = fgetcsv($handle);
        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $header);

        $expected = ['category', 'unit', 'subunit', 'office'];
        if ($header !== $expected) {
            $this->warn('Header row found: ' . implode(', ', $header));
            $this->warn('Expected: ' . implode(', ', $expected));
            $this->warn('Continuing anyway, assuming column order is Category, Unit, SubUnit, Office...');
        }

        // Forward-fill trackers (for merged-cell style CSVs)
        $lastCategoryName = null;
        $lastUnitName     = null;
        $lastSubUnitName  = null;

        $lastCategory = null;
        $lastUnit     = null;
        $lastSubUnit  = null;

        $rowNumber = 1;
        $created = [
            'categories' => 0,
            'units'      => 0,
            'sub_units'  => 0,
            'offices'    => 0,
        ];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip completely empty rows
            if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            [$categoryName, $unitName, $subUnitName, $officeName] = array_pad(
                array_map(fn ($v) => trim((string) $v), $row),
                4,
                ''
            );

            // Forward-fill: if blank, reuse the last non-blank value
            if ($categoryName === '') {
                $categoryName = $lastCategoryName;
            } else {
                $lastCategoryName = $categoryName;
                // Category changed -> reset everything below it
                $lastUnitName = null;
                $lastSubUnitName = null;
            }

            if ($unitName === '') {
                $unitName = $lastUnitName;
            } else {
                $lastUnitName = $unitName;
                $lastSubUnitName = null;
            }

            if ($subUnitName === '') {
                $subUnitName = $lastSubUnitName;
            } else {
                $lastSubUnitName = $subUnitName;
            }

            if (!$categoryName) {
                $this->warn("Row {$rowNumber}: skipped, no category could be resolved.");
                continue;
            }

            // --- Category ---
            if (!$lastCategory || $lastCategory->name !== $categoryName) {
                $lastCategory = PnCategory::firstOrCreate(
                    ['name' => $categoryName],
                    ['abreviation' => null, 'description' => null, 'address' => null, 'icon' => null]
                );
                if ($lastCategory->wasRecentlyCreated) {
                    $created['categories']++;
                }
            }

            // --- Unit ---
            if ($unitName) {
                if (!$lastUnit || $lastUnit->name !== $unitName || $lastUnit->category_id !== $lastCategory->id) {
                    $lastUnit = PnUnit::firstOrCreate(
                        ['category_id' => $lastCategory->id, 'name' => $unitName],
                        ['abreviation' => null, 'address' => null, 'description' => null, 'icon' => null]
                    );
                    if ($lastUnit->wasRecentlyCreated) {
                        $created['units']++;
                    }
                }
            } else {
                $lastUnit = null;
            }

            // --- SubUnit ---
            if ($subUnitName && $lastUnit) {
                if (!$lastSubUnit || $lastSubUnit->name !== $subUnitName || $lastSubUnit->unit_id !== $lastUnit->id) {
                    $lastSubUnit = PnSubUnit::firstOrCreate(
                        [
                            'category_id' => $lastCategory->id,
                            'unit_id'     => $lastUnit->id,
                            'name'        => $subUnitName,
                        ],
                        ['abreviation' => null, 'address' => null, 'description' => null, 'icon' => null]
                    );
                    if ($lastSubUnit->wasRecentlyCreated) {
                        $created['sub_units']++;
                    }
                }
            } else {
                $lastSubUnit = null;
            }

            // --- Office ---
            if ($officeName !== '' && $officeName !== null) {
                if (!$lastUnit) {
                    $this->warn("Row {$rowNumber}: office '{$officeName}' skipped, no unit resolved.");
                    continue;
                }

                $office = PnOffice::firstOrCreate(
                    [
                        'category_id' => $lastCategory->id,
                        'unit_id'     => $lastUnit->id,
                        'sub_unit_id' => $lastSubUnit?->id,
                        'name'        => $officeName,
                    ],
                    ['abreviation' => null, 'address' => null, 'description' => null, 'icon' => null]
                );
                if ($office->wasRecentlyCreated) {
                    $created['offices']++;
                }
            }
        }

        fclose($handle);

        $this->info('Import complete:');
        $this->table(
            ['Type', 'Newly created'],
            [
                ['Categories', $created['categories']],
                ['Units', $created['units']],
                ['Sub Units', $created['sub_units']],
                ['Offices', $created['offices']],
            ]
        );

        return self::SUCCESS;
    }
}