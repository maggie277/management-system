<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DepreciationYear;
use Illuminate\Support\Facades\Log;

class DepreciationYearsSeeder extends Seeder
{
    public function run()
    {
        $startYear = 2011;
        $endYear = date('Y') + 1; // Current year + 1
        $years = range($startYear, $endYear);

        $existingYears = DepreciationYear::pluck('year')->toArray();
        $newYears = array_diff($years, $existingYears);

        if (empty($newYears)) {
            $this->command->info('All years already exist in the database.');
            return;
        }

        foreach ($newYears as $index => $year) {
            try {
                DepreciationYear::create([
                    'year' => $year,
                    'position' => $index + 1 + count($existingYears),
                    'background_color' => '#f8f9fa',
                    'text_color' => '#000000',
                    'is_active' => true
                ]);
                $this->command->info("Added year: {$year}");
            } catch (\Exception $e) {
                $this->command->error("Failed to add year {$year}: " . $e->getMessage());
            }
        }

        $this->command->info('Successfully seeded depreciation years.');
    }
}
