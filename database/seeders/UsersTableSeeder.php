<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // System Admin (You - Executive)
        User::create([
            'name' => 'Margret',
            'email' => 'margret@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'system_admin',
            'department' => 'Executive',
            'position' => 'System Administrator',
        ]);

        // 1. Executive Director
        User::create([
            'name' => 'Isaac Mwaipopo',
            'email' => 'isaac@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'Executive Directors office',
            'position' => 'Executive Director',
        ]);

        // 2. Head of Research
        User::create([
            'name' => 'Ibrahim Kamara',
            'email' => 'ibrahim@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'head',
            'department' => 'Research',
            'position' => 'Head of Research',
        ]);

        // 3. Head of Advocacy and Campaigns
        User::create([
            'name' => 'Natalie Kaunda',
            'email' => 'natalie@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'head',
            'department' => 'Advocacy and Campaigns',
            'position' => 'Head of Advocacy and Campaigns',
        ]);

        // 4. M&E Specialist
        User::create([
            'name' => 'Isaiah Mbewe',
            'email' => 'isaiah@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department' => 'M&E',
            'position' => 'M&E Specialist',
        ]);

        // 5. Human Resource Manager
        User::create([
            'name' => 'Sylvia Phiri',
            'email' => 'sylvia@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department' => 'Finance and Admin',
            'position' => 'Human Resource Manager',
        ]);

        // 6. Finance Manager (NEW)
        User::create([
            'name' => 'Khutoma Kaira Bwalya',
            'email' => 'khutoma@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department' => 'Finance and Admin',
            'position' => 'Finance Manager',
        ]);

        // 7. Fundraising and Partnership Manager
        User::create([
            'name' => 'Mukuka Chilalika',
            'email' => 'mukuka@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department' => 'Executive Directors office',
            'position' => 'Fundraising and Partnership Manager',
        ]);

        // 8. Communications & Advocacy Officer
        User::create([
            'name' => 'Mwaka Nyimbili',
            'email' => 'mwaka@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Advocacy and Campaigns',
            'position' => 'Communications & Advocacy Officer',
        ]);

        // 9. Finance Assistant
        User::create([
            'name' => 'Mambwe Kaluba',
            'email' => 'mambwe@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Finance and Admin',
            'position' => 'Finance Assistant',
        ]);

        // 10. Graphics Designer
        User::create([
            'name' => 'Aaron Ngonga',
            'email' => 'aaron@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Advocacy and Campaigns',
            'position' => 'Graphics Designer',
        ]);

        // 11. Climate Change and Environment Researcher
        User::create([
            'name' => 'Solomon Mwampikita',
            'email' => 'solomon@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Research',
            'position' => 'Climate Change and Environment Researcher',
        ]);

        // 12. Public Finance Researcher
        User::create([
            'name' => 'Robert Mwale',
            'email' => 'robert@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Research',
            'position' => 'Public Finance Researcher',
        ]);

        // 13. Trade and Investment Research Assistant
        User::create([
            'name' => 'Barnabas Mwale',
            'email' => 'barnabas@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Research',
            'position' => 'Trade and Investment Research Assistant',
        ]);

        // 14. Finance and Admin Assistant (NEW)
        User::create([
            'name' => 'Annette Mulenga',
            'email' => 'annette@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Finance and Admin',
            'position' => 'Finance and Admin Assistant',
        ]);

        // 15. Office Assistant
        User::create([
            'name' => 'Donald Nyondo',
            'email' => 'donald@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Finance and Admin',
            'position' => 'Office Assistant',
        ]);

        // 16. Office Cleaner
        User::create([
            'name' => 'Eunice Simwanza',
            'email' => 'eunice@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Finance and Admin',
            'position' => 'Office Cleaner',
        ]);

        // 17. Legal Researcher
        User::create([
            'name' => 'Lucy Musonda',
            'email' => 'lucy@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Research',
            'position' => 'Legal Researcher',
        ]);

        // 18. Research Associate – Climate Change and Environment
        User::create([
            'name' => 'Dr Matildah Kaliba',
            'email' => 'matildah@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Research',
            'position' => 'Research Associate – Climate Change and Environment',
        ]);

        // 19. Research Associate-Extractives
        User::create([
            'name' => 'Dr Stephen Kambani',
            'email' => 'stephen@ctpd.org.zm',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Research',
            'position' => 'Research Associate-Extractives',
        ]);
    }
}
