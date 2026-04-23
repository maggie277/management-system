<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class BudgetsTableSeeder extends Seeder
{
    public function run()
    {
        // Get a user to assign as creator
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create([
                'name' => 'System Admin',
                'email' => 'admin@ctpd.org.zm',
                'role' => 'system_admin',
            ]);
        }

        // Create sample budget - USING project_code
        $budget = Budget::create([
            'project_title' => 'Building Capacity for Sustainable Tobacco Control Implementation in Zambia',
            'project_goal' => 'To contribute to enhancing policy and legal environment for effective and sustainable implementation of tobacco control interventions in Zambia',
            'project_code' => 'TC-ZM-2024-001', // This is project_code
            'contact_person' => 'Isaac Mwaipopo',
            'contact_email' => 'isaacmwaipopo@ctpd.org.zm',
            'contact_phone' => '+260976667999',
            'duration' => '18 Months (2024-2025)',
            'exchange_rate' => 25.00,
            'total_budget_zmw' => 2500000,
            'total_budget_usd' => 100000,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'status' => 'approved',
        ]);

        // Add budget items...
        $items = [
            // A - CORE PROGRAM EXPENDITURE
            [
                'section' => 'A - CORE PROGRAM EXPENDITURE',
                'objective' => 'OBJECTIVE 1: To strengthen technical and institutional capacity of CTPD for effective delivery of its tobacco control mandate in Zambia.',
                'activity' => 'ACTIVITY 1.1: Conduct the Quarterly review monitoring and evaluation process',
                'component' => null,
                'description_cost_category' => 'Civil Society Institutional Support',
                'description_cost_item' => 'Venue and Conference',
                'number' => 1,
                'frequency' => '8',
                'unit' => 'Quarter',
                'unit_cost' => 12522,
                'total_amount_zmw' => 100176,
                'total_amount_usd' => 4007,
                'comments' => 'Venue and conference at K450 per person for 22 persons, for 2 days, per quarter',
            ],
            // Add more items...
        ];

        foreach ($items as $index => $item) {
            $item['budget_id'] = $budget->id;
            $item['sort_order'] = $index + 1;
            BudgetItem::create($item);
        }

        // Create second budget
        Budget::create([
            'project_title' => 'Health Advocacy Program',
            'project_goal' => 'Improving healthcare access in rural communities',
            'project_code' => 'HEALTH-2024-002', // project_code
            'contact_person' => 'Jane Doe',
            'contact_email' => 'jane@ctpd.org.zm',
            'contact_phone' => '+260977123456',
            'duration' => '12 Months',
            'exchange_rate' => 25.00,
            'total_budget_zmw' => 1500000,
            'total_budget_usd' => 60000,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'status' => 'pending',
        ]);
    }
}
