<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'seller', 'admin'])
                ->default('user')
                ->after('id');
        });

        // Map existing role assignments to the new enum column
        $roleSlugById = DB::table('roles')->pluck('slug', 'id');

        DB::table('users')->select('id', 'role_id')->chunkById(100, function ($users) use ($roleSlugById) {
            foreach ($users as $user) {
                $slug = $roleSlugById[$user->role_id] ?? null;

                $role = match ($slug) {
                    'admin' => 'admin',
                    'moderator', 'seller' => 'seller',
                    'user' => 'user',
                    default => 'user',
                };

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['role' => $role]);
            }
        });

        // Normalize any null or empty values to user
        DB::table('users')
            ->whereNull('role')
            ->orWhere('role', '')
            ->update(['role' => 'user']);

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->nullable()
                ->after('id')
                ->constrained('roles')
                ->nullOnDelete();
        });

        $roleIdBySlug = DB::table('roles')->pluck('id', 'slug');

        DB::table('users')->select('id', 'role')->chunkById(100, function ($users) use ($roleIdBySlug) {
            foreach ($users as $user) {
                $roleId = match ($user->role) {
                    'admin' => $roleIdBySlug['admin'] ?? null,
                    'seller' => $roleIdBySlug['seller'] ?? ($roleIdBySlug['moderator'] ?? null),
                    default => $roleIdBySlug['user'] ?? null,
                };

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['role_id' => $roleId]);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};

