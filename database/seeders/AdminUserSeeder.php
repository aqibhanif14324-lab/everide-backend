<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $moderatorRole = Role::where('slug', 'moderator')->first();
        $userRole = Role::where('slug', 'user')->first();

        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Moderator user
        User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'role_id' => $moderatorRole->id,
                'name' => 'Moderator User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Regular users
        for ($i = 1; $i <= 10; $i++) {
            User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'role_id' => $userRole->id,
                    'name' => "User {$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'avatar_url' => "https://i.pravatar.cc/150?img={$i}",
                ]
            );
        }
    }
}