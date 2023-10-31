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
        Schema::table('games', function (Blueprint $table) {
            $table->foreign('metacritic_id')->references('id')->on('games_metacritic');
            $table->foreign('giantbomb_id')->references('id')->on('games_giantbomb');
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->foreign('genre_id')->references('id')->on('genres');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropConstrainedForeignId('metacritic_id');
            $table->dropConstrainedForeignId('giantbomb_id');
            $table->dropConstrainedForeignId('platform_id');
            $table->dropConstrainedForeignId('genre_id');
        });
    }
};
