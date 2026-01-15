<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add amount column if it doesn't exist
        if (!Schema::hasColumn('bids', 'amount')) {
            Schema::table('bids', function (Blueprint $table) {
                $table->decimal('amount', 10, 2)->after('user_id');
            });
        }

        // Add status column
        Schema::table('bids', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('user_id');
        });

        // Make event and metadata nullable
        Schema::table('bids', function (Blueprint $table) {
            $table->text('event')->nullable()->change();
            $table->longText('metadata')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        if (Schema::hasColumn('bids', 'amount')) {
            Schema::table('bids', function (Blueprint $table) {
                $table->dropColumn('amount');
            });
        }
    }
};
