<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asset;

class FixAssetDepreciation extends Command
{
    protected $signature = 'assets:fix-depreciation';
    protected $description = 'Fix depreciation calculations for all assets';

    public function handle()
    {
        $assets = Asset::all();
        $currentYear = date('Y');

        foreach ($assets as $asset) {
            $this->info("Fixing asset: {$asset->name}");

            $accumulated = $asset->calculateAccumulatedDepreciation($currentYear);
            $netBookValue = $asset->calculateNetBookValue($currentYear);

            $asset->update([
                'accumulated_depreciation' => $accumulated,
                'net_book_value' => $netBookValue,
            ]);

            $this->line("  Cost: {$asset->cost}");
            $this->line("  Purchase Year: {$asset->date_of_purchase->year}");
            $this->line("  Dep Rate: {$asset->depreciation_rate}%");
            $this->line("  Annual Depreciation: {$asset->calculateAnnualDepreciation()}");
            $this->line("  Accumulated Depreciation: {$accumulated}");
            $this->line("  Net Book Value: {$netBookValue}");
            $this->line("---");
        }

        $this->info('Depreciation fixed for all assets.');
        return Command::SUCCESS;
    }
}
