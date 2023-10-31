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
        Schema::create('user_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id_from')->unsigned();
            $table->foreign('user_id_from')->references('id')->on('users');

            $table->integer('user_id_to')->unsigned();
            $table->foreign('user_id_to')->references('id')->on('users');

            $table->boolean('is_seller');

            $table->integer('offer_id')->unsigned();
            $table->foreign('offer_id')->references('id')->on('offers');

            $table->integer('listing_id')->unsigned();
            $table->foreign('listing_id')->references('id')->on('listings');

            $table->integer('rating');
            $table->text('notice')->nullable();
            $table->boolean('active');
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
        Schema::drop('user_ratings');
    }
};
