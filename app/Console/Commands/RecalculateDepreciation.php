<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asset;

class RecalculateDepreciation extends Command
{
    protected $signature = 'assets:recalculate-depreciation';
    protected $description = 'Recalculate depreciation for all assets';

    public function handle()
    {
        $assets = Asset::all();
        $currentYear = date('Y');

        foreach ($assets as $asset) {
            $this->info("Recalculating for asset: {$asset->name}");

            $accumulated = $asset->calculateAccumulatedDepreciation($currentYear);
            $netBookValue = $asset->calculateNetBookValue($currentYear);

            $asset->update([
                'accumulated_depreciation' => $accumulated,
                'net_book_value' => $netBookValue,
            ]);

            $this->line("  Cost: {$asset->cost}");
            $this->line("  Accumulated Depreciation: {$accumulated}");
            $this->line("  Net Book Value: {$netBookValue}");
        }

        $this->info('Depreciation recalculated for all assets.');
        return Command::SUCCESS;
    }
}
