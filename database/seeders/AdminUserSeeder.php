<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')
            ->whereNull('role')
            ->orWhere('role', '')
            ->update(['role' => UserRole::USER->value]);

        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role' => UserRole::ADMIN,
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Regular users
        for ($i = 1; $i <= 10; $i++) {
            User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'role' => UserRole::USER,
                    'name' => "User {$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'avatar_url' => "https://i.pravatar.cc/150?img={$i}",
                ]
            );
        }
    }
}