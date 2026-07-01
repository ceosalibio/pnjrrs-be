<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ItemAfposCluster;

class ImportRatings extends Command
{
    /**
     * php artisan import:ratings {path}
     *
     * Example:
     *   php artisan import:ratings storage/app/ratings.csv
     */
    protected $signature = 'import:ratings {path}';

    protected $description = 'Import ItemAfposCluster ratings from a CSV file';

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

        // Read header row: group, name, description
        $header = fgetcsv($handle);
        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $header);

        $expected = ['group', 'name', 'description'];
        if ($header !== $expected) {
            $this->warn('Header row found: ' . implode(', ', $header));
            $this->warn('Expected: ' . implode(', ', $expected));
            $this->warn('Continuing anyway, assuming column order is group, name, description...');
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

            [$group, $name, $description] = array_pad(
                array_map(fn ($v) => trim((string) $v), $row),
                3,
                ''
            );

            // Validate required fields
            if (!$group || !$name) {
                $this->warn("Row {$rowNumber}: skipped, group and name are required.");
                $skipped++;
                continue;
            }

            // Create or update the rating
            $cluster = ItemAfposCluster::firstOrCreate(
                ['group' => $group, 'name' => $name],
                ['description' => $description ?: null]
            );

            if ($cluster->wasRecentlyCreated) {
                $created++;
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
