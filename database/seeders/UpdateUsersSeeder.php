<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUsersSeeder extends Seeder
{
    public function run(): void
    {
        $usersData = [
            // System Admin (You - Executive)
            [
                'name' => 'Margret',
                'email' => 'margret@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'system_admin',
                'department' => 'Executive',
                'position' => 'System Administrator',
            ],
            // 1. Executive Director
            [
                'name' => 'Isaac Mwaipopo',
                'email' => 'isaac@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'Executive Directors office',
                'position' => 'Executive Director',
            ],
            // 2. Head of Research
            [
                'name' => 'Ibrahim Kamara',
                'email' => 'ibrahim@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'head',
                'department' => 'Research',
                'position' => 'Head of Research',
            ],
            // 3. Head of Advocacy and Campaigns
            [
                'name' => 'Natalie Kaunda',
                'email' => 'natalie@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'head',
                'department' => 'Advocacy and Campaigns',
                'position' => 'Head of Advocacy and Campaigns',
            ],
            // 4. M&E Specialist
            [
                'name' => 'Isaiah Mbewe',
                'email' => 'isaiah@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'department' => 'M&E',
                'position' => 'M&E Specialist',
            ],
            // 5. Human Resource Manager
            [
                'name' => 'Sylvia Phiri',
                'email' => 'sylvia@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'department' => 'Finance and Admin',
                'position' => 'Human Resource Manager',
            ],
            // 6. Finance Manager (NEW)
            [
                'name' => 'Khutoma Kaira Bwalya',
                'email' => 'khutoma@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'department' => 'Finance and Admin',
                'position' => 'Finance Manager',
            ],
            // 7. Fundraising and Partnership Manager
            [
                'name' => 'Mukuka Chilalika',
                'email' => 'mukuka@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'department' => 'Executive Directors office',
                'position' => 'Fundraising and Partnership Manager',
            ],
            // 8. Communications & Advocacy Officer
            [
                'name' => 'Mwaka Nyimbili',
                'email' => 'mwaka@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Advocacy and Campaigns',
                'position' => 'Communications & Advocacy Officer',
            ],
            // 9. Finance Assistant
            [
                'name' => 'Mambwe Kaluba',
                'email' => 'mambwe@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Finance and Admin',
                'position' => 'Finance Assistant',
            ],
            // 10. Graphics Designer
            [
                'name' => 'Aaron Ngonga',
                'email' => 'aaron@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Advocacy and Campaigns',
                'position' => 'Graphics Designer',
            ],
            // 11. Climate Change and Environment Researcher
            [
                'name' => 'Solomon Mwampikita',
                'email' => 'solomon@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Research',
                'position' => 'Climate Change and Environment Researcher',
            ],
            // 12. Public Finance Researcher
            [
                'name' => 'Robert Mwale',
                'email' => 'robert@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Research',
                'position' => 'Public Finance Researcher',
            ],
            // 13. Trade and Investment Research Assistant
            [
                'name' => 'Barnabas Mwale',
                'email' => 'barnabas@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Research',
                'position' => 'Trade and Investment Research Assistant',
            ],
            // 14. Finance and Admin Assistant (NEW)
            [
                'name' => 'Annette Mulenga',
                'email' => 'annette@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Finance and Admin',
                'position' => 'Finance and Admin Assistant',
            ],
            // 15. Office Assistant
            [
                'name' => 'Donald Nyondo',
                'email' => 'donald@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Finance and Admin',
                'position' => 'Office Assistant',
            ],
            // 16. Office Cleaner
            [
                'name' => 'Eunice Simwanza',
                'email' => 'eunice@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Finance and Admin',
                'position' => 'Office Cleaner',
            ],
            // 17. Legal Researcher
            [
                'name' => 'Lucy Musonda',
                'email' => 'lucy@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Research',
                'position' => 'Legal Researcher',
            ],
            // 18. Research Associate – Climate Change and Environment
            [
                'name' => 'Dr Matildah Kaliba',
                'email' => 'matildah@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Research',
                'position' => 'Research Associate – Climate Change and Environment',
            ],
            // 19. Research Associate-Extractives
            [
                'name' => 'Dr Stephen Kambani',
                'email' => 'stephen@ctpd.org.zm',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'department' => 'Research',
                'position' => 'Research Associate-Extractives',
            ],
        ];

        foreach ($usersData as $userData) {
            $user = User::where('email', $userData['email'])->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'name' => $userData['name'],
                    'role' => $userData['role'],
                    'department' => $userData['department'],
                    'position' => $userData['position'],
                    // Don't update password if you want to keep existing passwords
                    // 'password' => $userData['password'],
                ]);
                echo "Updated: {$user->name}\n";
            } else {
                // Create new user if doesn't exist
                User::create($userData);
                echo "Created: {$userData['name']}\n";
            }
        }

        // Optional: Remove users that are no longer in the client's list
        $currentEmails = array_column($usersData, 'email');
        $deletedUsers = User::whereNotIn('email', $currentEmails)
            ->where('email', '!=', 'margret@ctpd.org.zm') // Keep your admin account
            ->delete();

        echo "Removed {$deletedUsers} users that are no longer in the client's list\n";

        echo "User update completed successfully!\n";
    }
}
