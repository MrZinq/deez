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
        Schema::create('games_metacritic', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
            $table->string('name');
            $table->integer('score')->nullable();
            $table->integer('userscore')->nullable();
            $table->string('thumbnail');
            $table->text('summary');
            $table->text('genre')->nullable();
            $table->string('platform');
            $table->string('publisher');
            $table->string('developer');
            $table->string('rating');
            $table->date('release_date');
            $table->string('url');
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
        Schema::drop('games_metacritic');
    }
};
