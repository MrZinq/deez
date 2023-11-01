<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->foreign('thread_id')->references('id')->on('messenger_threads');
            $table->foreign('trade_game')->references('id')->on('games');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('listing_id');
            $table->dropConstrainedForeignId('thread_id');
            $table->dropConstrainedForeignId('trade_game');
        });
    }
};
