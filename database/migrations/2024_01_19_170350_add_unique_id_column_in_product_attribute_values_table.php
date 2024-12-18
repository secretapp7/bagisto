<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the index safely (check if it exists first for SQLite compatibility)
        if (DB::getDriverName() === 'sqlite') {
            $indexExists = DB::select(
                "SELECT name FROM sqlite_master WHERE type = 'index' AND name = 'price_indices_product_id_customer_group_id_channel_id_unique'"
            );

            if ($indexExists) {
                DB::statement('DROP INDEX price_indices_product_id_customer_group_id_channel_id_unique');
            }
        } else {
            // For other databases like MySQL
            Schema::table('product_price_indices', function (Blueprint $table) {
                $table->dropUnique('price_indices_product_id_customer_group_id_channel_id_unique');
            });
        }

        // Add the 'channel_id' column if it doesn't exist
        if (!Schema::hasColumn('product_price_indices', 'channel_id')) {
            Schema::table('product_price_indices', function (Blueprint $table) {
                $table->unsignedBigInteger('channel_id')->nullable()->after('customer_group_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the 'channel_id' column
        if (Schema::hasColumn('product_price_indices', 'channel_id')) {
            Schema::table('product_price_indices', function (Blueprint $table) {
                $table->dropColumn('channel_id');
            });
        }

        // Recreate the dropped index safely
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('CREATE UNIQUE INDEX price_indices_product_id_customer_group_id_channel_id_unique ON product_price_indices (product_id, customer_group_id, channel_id)');
        } else {
            Schema::table('product_price_indices', function (Blueprint $table) {
                $table->unique(['product_id', 'customer_group_id', 'channel_id'], 'price_indices_product_id_customer_group_id_channel_id_unique');
            });
        }
    }
};
