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
        Schema::create('games_giantbomb', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('summary')->nullable();
            $table->text('genres')->nullable();
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->text('videos')->nullable();
            $table->text('ratings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('games_giantbomb');
    }
};
