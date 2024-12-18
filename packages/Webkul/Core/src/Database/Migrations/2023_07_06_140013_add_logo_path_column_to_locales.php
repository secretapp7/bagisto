<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the 'logo_path' column first
        if (!Schema::hasColumn('locales', 'logo_path')) {
            Schema::table('locales', function ($table) {
                $table->string('logo_path')->nullable();
            });
        }

        // Update the 'logo_path' column
        DB::table('locales')->whereNull('logo_path')->update([
            'logo_path' => DB::raw('"locales/" || code || ".png"'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the 'logo_path' column
        if (Schema::hasColumn('locales', 'logo_path')) {
            Schema::table('locales', function ($table) {
                $table->dropColumn('logo_path');
            });
        }
    }
};
