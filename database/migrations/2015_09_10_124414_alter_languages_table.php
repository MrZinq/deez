<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('languages', function ($table) {
            $table->string('script', 20)->nullable()->after('abbr');
            $table->string('native', 20)->nullable()->after('script');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('languages', function ($table) {
            $table->dropColumn('script');
            $table->dropColumn('native');
        });
    }
};
