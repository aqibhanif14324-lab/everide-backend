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
        // Relax enum constraint temporarily to update data and add new enum values
        DB::statement("ALTER TABLE shops MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pending'");

        Schema::table('shops', function (Blueprint $table) {
            if (! Schema::hasColumn('shops', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
        });

        // Normalize existing data into the new workflow
        DB::table('shops')
            ->where('status', 'active')
            ->update([
                'status' => 'approved',
                'approved_at' => DB::raw('COALESCE(approved_at, CURRENT_TIMESTAMP)'),
            ]);

        DB::table('shops')
            ->where('status', 'inactive')
            ->update([
                'status' => 'pending',
                'approved_at' => null,
            ]);

        DB::table('shops')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update([
                'status' => 'pending',
                'approved_at' => null,
            ]);

        // Enforce the new enum constraint
        DB::statement("ALTER TABLE shops MODIFY status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE shops MODIFY status VARCHAR(20) NOT NULL DEFAULT 'active'");

        DB::table('shops')
            ->where('status', 'approved')
            ->update([
                'status' => 'active',
            ]);

        DB::table('shops')
            ->where('status', 'pending')
            ->update([
                'status' => 'inactive',
            ]);

        DB::statement("ALTER TABLE shops MODIFY status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'");

        Schema::table('shops', function (Blueprint $table) {
            if (Schema::hasColumn('shops', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
        });
    }
};

