<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Staff Contracts',
                'color' => '#007bff',
                'icon' => 'bi-people',
                'description' => 'Employee contracts and agreements'
            ],
            [
                'name' => 'Donor Contracts',
                'color' => '#28a745',
                'icon' => 'bi-handshake',
                'description' => 'Donor agreements and partnerships'
            ],
            [
                'name' => 'Budgets',
                'color' => '#ffc107',
                'icon' => 'bi-calculator',
                'description' => 'Financial budgets and plans'
            ],
            [
                'name' => 'Sick Notes',
                'color' => '#dc3545',
                'icon' => 'bi-heart-pulse',
                'description' => 'Medical certificates and sick leave documents'
            ],
            [
                'name' => 'Petty Cash',
                'color' => '#6f42c1',
                'icon' => 'bi-cash-coin',
                'description' => 'Petty cash records and receipts'
            ],
            [
                'name' => 'Other',
                'color' => '#6c757d',
                'icon' => 'bi-file-earmark',
                'description' => 'Other documents'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
