<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Folder;
use App\Models\Category;

class CheckFolderCategories extends Command
{
    protected $signature = 'check:folders-categories';
    protected $description = 'Check folders and categories relationship';

    public function handle()
    {
        $folders = Folder::all();
        $categories = Category::all();

        $this->info("Total folders: " . $folders->count());
        $this->info("Total categories: " . $categories->count());

        $this->info("\nFolder details:");
        foreach ($folders as $folder) {
            $categoryName = $folder->category ? $folder->category->name : 'None';
            $this->line("Folder: {$folder->name} | Category ID: {$folder->category_id} | Category: {$categoryName}");
        }

        // Check for invalid category relationships
        $invalidFolders = Folder::whereNotNull('category_id')
            ->whereNotIn('category_id', Category::pluck('id'))
            ->get();

        $this->info("\nFolders with invalid categories: " . $invalidFolders->count());

        if ($invalidFolders->count() > 0) {
            $this->info("Fixing invalid folders...");
            $defaultCategory = Category::first();
            if ($defaultCategory) {
                $fixedCount = Folder::whereNull('category_id')
                    ->orWhereNotIn('category_id', Category::pluck('id'))
                    ->update(['category_id' => $defaultCategory->id]);
                $this->info("Fixed {$fixedCount} folders");
            }
        }

        return Command::SUCCESS;
    }
}
